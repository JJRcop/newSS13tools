<?php

class GameLogs{

  public function __construct($round){
    $this->round = $round;
  }

  public function __destruct(){
    unset($this->round);
  }

  public function getGameLogs(){
    if(file_exists($this->round->logCache)){ //Load from cache
      $this->round->logs = $this->getCachedLogs($this->round->logCache);
      $this->round->fromCache = TRUE;
    } else { //Load from remote
      $this->reset();
      $app = new app();
      $this->round->logs = $app->getRemoteFile($this->round->logsURL,"/game.txt");
      $this->round->attack = $app->getRemoteFile($this->round->logsURL,"/attack.txt");
      $this->round->logs = $this->parseGameLog($this->round->logs,$this->round->attack);
      $this->cacheParsedLogs($this->round->logs);
      $this->round->fromCache = FALSE;
    }
    return $this->round;
  }

  public function resetLog(){
    $user = new user();
    if(1 >= $user->level){
      return returnError("You do not have permission to do this.");
    }
    $this->reset();
    return returnSuccess($this->round->logCache." deleted. Regenerating stats.");
  }

  public function reset(){
    $db = new database(true);
    $db->query("DELETE FROM explosion_log WHERE round = ?");
    $db->bind(1,$this->round->id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $db->query("DELETE FROM antag_log WHERE round = ?");
    $db->bind(1,$this->round->id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    if(defined('LOG_CACHE_MODE') && 'database' == LOG_CACHE_MODE){
      $db->query("DELETE FROM round_logs WHERE round = ?");
      $db->bind(1,$this->round->id);
      try {
        $db->execute();
      } catch (Exception $e) {
        return returnError("Database error: ".$e->getMessage());
      }
    } else {
      if(file_exists($this->round->logCache)){
        unlink($this->round->logCache);
      }
    }
  }
  
  public function getAvailableLogs(){
    $app = new app();
    $files = $app->getRemoteFile($this->round->logsURL);
    $files = strip_tags($files);
    $files = explode("\r\n",$files);
    $tmp = array();
    foreach ($files as $f){
      if(strpos($f, '.gz')!== false){
        $f = explode('.gz',$f);
        $tmp[] = $f[0].".gz";
      }
    }
    $this->round->availableLogs = (object) $tmp;
    return $round;
  }

  public function parseGameLog($logs,$attack){
    $date = date('Y-m-d',strtotime($this->round->start_datetime));
    // var_dump($this->round->logsURL."/game.log.gz");
    //This method is ONLY for cleaning out useless lines from the logs
    $logs = strip_tags($logs);
    //For the sake of transparency, this method is heavily documented
    //
    //-censored lines have no value in public logs, and are removed entirely
    $logs = str_replace("-censored(misc)-\r\n",'',$logs);
    $logs = str_replace("-censored(sql logs)-\r\n",'',$logs);
    $logs = str_replace("-censored(asay/apm/ahelp/notes/etc)-\r\n",'',$logs);
    $logs = str_replace(" from -censored(ip/cid)- ",'',$logs);
    $logs = str_replace("-censored(private logtype)-\r\n", '', $logs);

    //For some reason, some lines are a single - and a return. These lines are
    //removed
    $logs = str_replace("-\r\n", '', $logs);

    //[hh:mm:ss]SAY: [mob name]/[ckey] : Something said
    //This line removes the spaces:   ^ ^
    $logs = str_replace(" : ",': ',$logs);

    //HTML spans are superfluous and removed
    $logs = str_replace("<span class='notice'>",'',$logs);
    $logs = str_replace("<span class='boldannounce'>",'',$logs);
    $logs = str_replace('</span>', '', $logs);

    //*no key* are mob emotes from mobs that aren't player controlled
    $logs = str_replace('*no key*/', '', $logs);

    //Deadchat(dsay) lines are prefixed with ghost/, this changes that to GHOST
    $logs = str_replace("SAY: Ghost/","GHOST: ", $logs);

    //This line splits the line into three parts:
    // Timestamp
    // Type
    // Content
    //The #-# is a line that I can easily break on in another method
    //
    //TODO: Randomize the #-# to stop people from (un)intentionally breaking
    //the parser
    $logs = preg_replace("/(\[)(\d{2}:\d{2}:\d{2})(])(GAME|ACCESS|SAY|OOC|ADMIN|EMOTE|WHISPER|PDA|CHAT|LAW|PRAY|COMMENT|VOTE|GHOST)(:\s)/",$date." $2#-#$4#-#", $logs);
    $logs = preg_replace("/([ ][(])([\d]{1,3}[,][\d]{1,3}[,][\d]{1,3})([)])/","#-#$2", $logs);

    //UTF8 encode (hey look who was ahead of the curve)
    $logs = utf8_encode($logs);

    //And split everything into a nice big array that we can iterate over
    $logs = explode("\r\n",$logs);

    //Remove empty lines
    array_filter($logs);

    $explosions = array();
    $antagLine = 0;
    $antags = array();
    $antagLines = array();
    $i = 0;

    //Further explode the log line 
    foreach ($logs as &$log){
      $log = explode('#-#',$log);
      if(isset($log[2]) && 'Antagonists at round end were...' == $log[2]){
        $antagLine = $i;
      }
      if(isset($log[3])){
        $coords = explode(',',$log[3]);
        $log['coord_x'] = (int) $coords[0];
        $log['coord_y'] = (int) $coords[1];
        $log['coord_z'] = (int) $coords[2];
        unset($log[3]);
      } else {
        $log['coord_x'] = null;
        $log['coord_y'] = null;
        $log['coord_z'] = null;
      }
      if (isset($log[1]) && 'GAME' == $log[1]
        && strpos($log[2], 'Explosion with size (') !== FALSE){
        $explosions[] = (object) $this->parseExplosion($log);
      }
      $i++;
    }

    foreach ($logs as $l => $log){
      if($l < $antagLine + 1) continue;
      if('GAME' != $log[1]){
        break;
      }
      $antagLines[] = $log;
    }
    foreach ($antagLines as $log){
      if('Blackbox sealed.' == $log[2]) continue;
      $antags[] = (object) $this->parseAntags($log);
    }

    if(isset($attack)){
      $attack = explode("---------------------\r\n",$attack);
      $attack = $attack[1];
      $attack = preg_replace("/(\[)(\d{2}:\d{2}:\d{2})(])(ATTACK)(:\s)/",$date." $2#-#$4#-#", $attack);
      $attack = preg_replace("/([ ][(])([\d]{1,3}[,][\d]{1,3}[,][\d]{1,3})([)])/","#-#$2", $attack);
      $attack = explode("\r\n",$attack);
      array_filter($attack);
      array_pop($attack);
      foreach ($attack as &$a){
        $a = explode('#-#',$a);
        if(isset($log[3])){
          $coords = explode(',',$a[3]);
          $a['coord_x'] = (int) $coords[0];
          $a['coord_y'] = (int) $coords[1];
          $a['coord_z'] = (int) $coords[2];
          unset($a[3]);
        } else {
          $a['coord_x'] = null;
          $a['coord_y'] = null;
          $a['coord_z'] = null;
        }
        $logs[] = $a;
      }
    }
    
    $this->extractDataFromLogs($explosions, $antags);

    usort($logs, function($a, $b) {
      return $a[0] <=> $b[0];
    });

    return $logs;
  }

  public function areRoundLogsInDB(){
    $db = new database(TRUE);
    $db->query("SELECT round FROM round_logs WHERE round = ? LIMIT 0,1");
    $db->bind(1, $this->round->id);
    try {
      if($db->single()){
        return true;
      }
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return false;
  }

  public function getCachedLogs($cache){
    if(defined('LOG_CACHE_MODE') && 'database' == LOG_CACHE_MODE){
      $db = new database(TRUE);
      $db->query("SELECT round_logs.timestamp as `0`,
        round_logs.type as `1`,
        round_logs.text as `2`,
        round_logs.x,
        round_logs.y,
        round_logs.z
        FROM round_logs WHERE round = ?");
      $db->bind(1, $this->round->id);
      try {
        return $db->resultset(\PDO::FETCH_ASSOC);
      } catch (Exception $e) {
        var_dump("Database error: ".$e->getMessage());
      }
    } else {
      $logs = file_get_contents($cache);
      return json_decode($logs, TRUE);
    }
  }

  public function cacheParsedLogs($round){
    if(defined('LOG_CACHE_MODE') && 'database' == LOG_CACHE_MODE){
      $this->pushLogsToDB();
    } else {
      $logsavefile = fopen($this->round->logCache,"w+");
      fwrite($logsavefile,json_encode($this->round->logs,JSON_FORCE_OBJECT |JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES));
      fclose($logsavefile);
    }
  }

  public function pushLogsToDB(){
    $db = new database(TRUE);
    $db->query("INSERT INTO round_logs 
      (round, `timestamp`, type, `text`, x, y, z)
      VALUES (?, ?, ?, ?, ?, ?, ?)");
    // var_dump($this->round->logs);
    foreach ($this->round->logs as $log){
      $db->bind(1, $this->round->id);
      $db->bind(2, $log[0]);
      $db->bind(3, $log[1]);
      $db->bind(4, $log[2]);
      $db->bind(5, $log['x']);
      $db->bind(6, $log['y']);
      $db->bind(7, $log['z']);
      try {
        $db->execute();
      } catch (Exception $e) {
        var_dump("Database error: ".$e->getMessage());
      }
    }
  }

  public function extractDataFromLogs($explosions, $antags){
    $db = new database(TRUE);

    //Save explosions
    $db->query("INSERT IGNORE INTO explosion_log
      (round, `time`, devestation, heavy, light, flash, area, x, y, z, added)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    foreach($explosions as $e){
      $db->bind(1, $e->round);
      $db->bind(2, $e->time);
      $db->bind(3, $e->devestation);
      $db->bind(4, $e->heavy);
      $db->bind(5, $e->light);
      $db->bind(6, $e->flash);
      $db->bind(7, $e->area);
      $db->bind(8, $e->x);
      $db->bind(9, $e->y);
      $db->bind(10, $e->z);
      try {
        $db->execute();
      } catch (Exception $e) {
        return returnError("Database error: ".$e->getMessage());
      }
    }

    //Save antags
    $db->query("INSERT IGNORE INTO antag_log
      (round, `time`, role, ckey, name, added)
      VALUES (?, ?, ?, ?, ?, NOW())");
    foreach($antags as $role){
      $db->bind(1, $role->round);
      $db->bind(2, $role->time);
      $db->bind(3, $role->role);
      foreach($role->antags as $name => $ckey){
        $db->bind(4, $name);
        $db->bind(5, $ckey);
        try {
          $db->execute();
        } catch (Exception $e) {
          return returnError("Database error: ".$e->getMessage());
        }
      }
    }
  }

  public function parseExplosion($log){
    $log[2] = str_replace('Explosion with size (', '', $log[2]);
    $log[2] = explode(') in area ',$log[2]);
    $e['area'] = $log[2][1];
    $e['time'] = $log[0];
    $e['round'] = $this->round->id;
    $e['x'] = $log['coord_x'];
    $e['y'] = $log['coord_y'];
    $e['z'] = $log['coord_z'];
    $log[2][0] = explode(', ',$log[2][0]);
    $e['devestation'] = (int) $log[2][0][0];
    $e['heavy'] =       (int) $log[2][0][0];
    $e['light'] =       (int) $log[2][0][0];
    $e['flash'] =       (int) $log[2][0][0];
    return $e;
  }

  public function parseAntags($log){
    $a['round'] = $this->round->id;
    $a['time'] = $log[0];
    $log = explode(':',$log[2]);
    $a['role'] = $log[0];
    // if('Blackbox sealed.' == $a['role']) return;
    $a['antag'] = $log[0];
    $antags = trim(rtrim($log[1]));
    $antags = str_replace('.', '', $antags);
    $antags = str_replace(')', '', $antags);
    $antags = explode(',',$antags);
    foreach($antags as &$antag){
      $antag = trim(rtrim($antag));
      $antag = explode('(',$antag);
      $a['antags'][$antag[1]] = $antag[0];
    }
    return $a;
  }

}