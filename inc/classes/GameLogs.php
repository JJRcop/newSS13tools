<?php

class GameLogs{

  public function __construct($round){
    $this->round = $round;
  }

  public function __destruct(){
    unset($this->round);
  }

  public function fetchRemoteFile($url, $file=null){
    $get = $url."/".$file;
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_URL => $get,
      CURLOPT_USERAGENT => "atlantaned.space log parser",
      CURLOPT_SSL_VERIFYPEER => FALSE,
      CURLOPT_SSL_VERIFYHOST => FALSE,
      CURLOPT_FOLLOWLOCATION => TRUE,
      CURLOPT_REFERER => "atlantaned.space",
      CURLOPT_ENCODING => 'gzip',
    ));
    $logs = curl_exec($curl);
    curl_close($curl);
    return $logs;
  }

  public function resetLog(){
    $user = new user();
    if(1 >= $user->level){
      return returnError("You do not have permission to do this.");
    }
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
    unlink($this->round->logCache);
    return returnSuccess($this->round->logCache." deleted. Regenerating stats.");
  }
  
  public function getAvailableLogs(){
    $files = $this->fetchRemoteFile($this->round->logsURL);
    $files = strip_tags($files);
    $files = explode("\r\n",$files);
    $tmp = array();
    foreach ($files as $f){
      if(strpos($f, '.gz')!== false){
        $f = explode('.gz',$f);
        $tmp[] = $f[0].".gz";
      }
    }
    $files = (object) $tmp;
    return $files;
  }

  public function parseGameLog($logs,$attack){
    $date = date('Y-m-d',strtotime($this->round->start_datetime));
    // var_dump($this->round->logsURL."/game.log.gz");
    //This method is ONLY for cleaning out useless lines from the logs

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

    //UTF8 encode (hey look who was ahead of the curve)
    $logs = utf8_encode($logs);

    //And split everything into a nice big array that we can iterate over
    $logs = explode("\r\n",$logs);

    //Remove empty lines
    array_filter($logs);

    //Remove the last line
    array_pop($logs);

    foreach ($logs as &$log){
      $log = explode('#-#',$log);
    }

    if(isset($attack)){
      $attack = explode("---------------------\r\n",$attack);
      $attack = $attack[1];
      $attack = preg_replace("/(\[)(\d{2}:\d{2}:\d{2})(])(ATTACK)(:\s)/",$date." $2#-#$4#-#", $attack);
      $attack = explode("\r\n",$attack);
      array_filter($attack);
      array_pop($attack);
      foreach ($attack as &$a){
        $a = explode('#-#',$a);
        $logs[] = $a;
      }
    }
    
    $this->extractDataFromLogs($logs);

    if($ogs){
      usort($logs, function($a, $b) {
        return $a[0] <=> $b[0];
      });
    }
    return $logs;
  }

  public function getGameLogs(){
    if(file_exists($this->round->logCache)){ //Load from cache
      $this->round->logs = $this->getCachedLogs($this->round->logCache);
      $this->round->fromCache = TRUE;
    } else { //Load from remote
      $this->round->logs = $this->fetchRemoteFile($this->round->logsURL,"game.txt.gz");
      $this->round->attack = $this->fetchRemoteFile($this->round->logsURL,"attack.txt.gz");
      $this->round->logs = $this->parseGameLog($this->round->logs,$this->round->attack);
      $this->cacheParsedLogs($this->round->logs);
      $this->round->fromCache = FALSE;
    }
    return $this->round;
  }

  public function getCachedLogs($cache){
    $logs = file_get_contents($cache);
    return json_decode($logs);
  }

  public function cacheParsedLogs($round){
    $logsavefile = fopen($this->round->logCache,"w+");
    fwrite($logsavefile,json_encode($this->round->logs,JSON_UNESCAPED_UNICODE));
    fclose($logsavefile);
  }

  public function extractDataFromLogs($logs){
    $explosions = array();
    $antags = array();
    $antagLines = array();
    $i = 0;
    foreach ($logs as $l => $log){
      // $log = explode('#-#',$log);
      if ('GAME' == $log[1]
        && strpos($log[2], 'Explosion with size (') !== FALSE){
        $explosions[] = (object) $this->parseExplosion($log);
      }
      if('Antagonists at round end were...' == $log[2]){
        $antagLine = $i;
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
      $antags[] = (object) $this->parseAntags($log);
    }
    $db = new database(TRUE);

    //Save explosions
    $db->query("INSERT INTO explosion_log
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
    $db->query("INSERT INTO antag_log
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
    $date = new dateTime($this->round->start_datetime);
    $date = $date->format('Y-m-d');
    $e['round'] = $this->round->id;
    $e['time'] = $date." ".$log[0];
    $exp = str_replace('Explosion with size (', '', $log[2]);
    $exp = explode(') in area ',$exp);
    $exp[0] = explode(', ',$exp[0]);
    $e['devestation'] = (int) $exp[0][0];
    $e['heavy'] = (int) $exp[0][1];
    $e['light'] = (int) $exp[0][2];
    $e['flash'] = (int) $exp[0][3];
    $exp[1] = explode(' (',$exp[1]);
    $exp[1][1] = str_replace(')', '', $exp[1][1]);
    $e['area'] = $exp[1][0];
    $exp[1][1] = explode(',',$exp[1][1]);
    $e['x'] = (int) $exp[1][1][0];
    $e['y'] = (int) $exp[1][1][1];
    $e['z'] = (int) $exp[1][1][2];
    return $e;
  }

  public function parseAntags($log){
    $date = new dateTime($this->round->start_datetime);
    $date = $date->format('Y-m-d');
    $a['round'] = $this->round->id;
    $a['time'] = $date." ".$log[0];
    $log = explode(':',$log[2]);
    $a['role'] = $log[0];
    // $a['antag'] = $log[0];
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