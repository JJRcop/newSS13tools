<?php 

class GameLogs {

  public $round;

  public $logs;

  public $explosions;

  public $status;

  public $remoteZip;
  public $zipCache;
  public $listing;

  public $fromCache;

  public $generated;


  public function __construct($round = null, $reset = FALSE) {
    //Round is a round object that we're going to get data from, which will be
    // used to find our logs. When/if we find them, they'll be attached to the
    // round object
    
    if ($round){
      if(is_object($round)){
        $this->round = $round;
      } else {
        $this->round = new round($round);
      }
     
     $this->generateURLs();

      if($reset){
        $user = new user();
        if(1 >= $user->level){
          echo parseReturn(returnError("You do not have permission to do this."));
        }
        $this->resetLogs();
      }

      $this->getLogs();
    } else {
      return;
    }

  }

  public function __destruct() {
    $this->round = null;
    $this->logs = null;
  }

  public function generateURLs(){
    $this->remoteZip = $this->round->logsURL.".zip";
    $this->zipCache = TMPDIR."/".$this->round->id."-".$this->round->server.".zip";
  }

  public function getLogs(){
    //Let's find our logs. They can be in one of two states:
    // • Saved in the database
    //    In which case all we need to do is find and fetch them
    //
    // • Not saved in the database
    //    We'll have to get the remote zip, grab the files we need, parse them
    //    and then stick em in the db
    //

    $db = new database(TRUE);
    $db->query("SELECT * FROM round_logs
      WHERE round = ?");
    $db->bind(1, $this->round->id);
    try {
      $this->logs = $db->resultset();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    if(!$this->logs){
      $this->fromCache = false;
      //Logs are not saved in the DB, proceed to getting the remote zip file
      //and parsing from there
      $this->status = FALSE; 
      if($this->getRemoteLogs()){
        $this->commitLogsToDB();
        $this->commitExplosionsToDB();
      } else {
        echo parseReturn(returnError("Unable to locate remote logs. They might not be available yet. Pleae wait a few minutes and try again."));
        return false;
      }
    } else {
      $this->fromCache = true;
      return true;
    }
  }

  public function getRemoteLogs(){
    $cache = $this->zipCache;
    if(file_exists($this->zipCache)){
      $this->parseCachedZip();
    } else {
      $app = new app();
      $logs = $app->getRemoteFile($this->remoteZip);
      if(!$logs){
        return false;
      }
      $handle = fopen($cache, 'w+');
      fwrite($handle, $logs);
      fclose($handle);
      $this->parseCachedZip();
    }
    if(isset($this->listing['game.txt'])
      && isset($this->listing['attack.txt'])){
      $this->parseLogs();
    }
    return true;
  }

  public function parseCachedZip(){
    // $file = fopen("phar://$this->zipCache",'r');
    // var_dump($file);
    $this->listing = array();
    $files = new RecursiveDirectoryIterator("phar://$this->zipCache");
    foreach ($files as $name => $file){
      if (!$file->isFile()) continue;
      $name = str_replace("phar://$this->zipCache/", '', $name);
      $this->listing[$name] = $file;
      //We'll deal with you later!
    } 
  }

  public function parseLogs(){
    $this->explosions = array();
    $lines = array();
    $date = date('Y-m-d',strtotime($this->round->start_datetime));

    //Game.txt
    $handle = fopen("phar://".$this->zipCache."/game.txt",'r');
    if($handle){
      while (($line = fgets($handle)) !== false) {
        //Remove leading and trailing whitespace
        $line = trim(rtrim($line));

        //Create a log type for ghost/deadchat
        $line = str_replace("SAY: Ghost/","GHOST: ", $line);

        //Drop any lines that don't have a timestamp
        if (!preg_match("/(\[)(\d{2}:\d{2}:\d{2})(])/", $line)) continue;

        //Prep lines to explode() into three parts:
        //  The Date
        //  The Type 
        //  The Content
        $line = preg_replace("/(\[)(\d{2}:\d{2}:\d{2})(])([\w\S]*)(: )([\w\W]*)/", "$2#-#$4#-#$6", $line);

        //If the content has coordinates, we're gonna split those out too
        $line = preg_replace("/([ ][(])([\d]{1,3}[,][\d]{1,3}[,][\d]{1,3})([)])/","#-#$2", $line);

        //Kaboom
        $line = explode('#-#',$line);

        //Convert the line time to an actual date
        $line['timestamp'] = $date.' '.$line[0];
        unset($line[0]);

        //Turn the type into a named key
        $line['type'] = $line[1];
        unset($line[1]);

        //Replace some content we don't need to know
        $line['text'] = str_replace(" from -censored(ip/cid)- ",' ',$line[2]);
        unset($line[2]);

        //Set defaults for line coordinates
        $line['x'] = null;
        $line['y'] = null;
        $line['z'] = null;

        //If we have coordinates, set them to the named keys
        if(isset($line[3])){
          $coords = explode(',', $line[3]);
          $line['x'] = (int) $coords[0];
          $line['y'] = (int) $coords[1];
          $line['z'] = (int) $coords[2];
          unset($line[3]);
        }
        if('GAME' == $line['type']
          && strpos($line['text'], 'Explosion with size') !== FALSE){
          $this->parseExplosion($line);
        }
        //Append to the lines array
        $lines[] = (object) $line;
      }
      fclose($handle);
    }

    //Attack.txt
    $handle = fopen("phar://".$this->zipCache."/attack.txt",'r');
    if($handle){
      while (($line = fgets($handle)) !== false) {
        //Remove leading and trailing whitespace
        $line = trim(rtrim($line));

        //Drop any lines that don't have a timestamp
        if (!preg_match("/(\[)(\d{2}:\d{2}:\d{2})(])/", $line)) continue;

        //Prep lines to explode() into three parts:
        //  The Date
        //  The Type 
        //  The Content
        $line = preg_replace("/(\[)(\d{2}:\d{2}:\d{2})(])([\w\S]*)(: )([\w\W]*)/", "$2#-#$4#-#$6", $line);

        //If the content has coordinates, we're gonna split those out too
        $line = preg_replace("/([ ][(])([\d]{1,3}[,][\d]{1,3}[,][\d]{1,3})([)])/","#-#$2", $line);
        //Kaboom
        $line = explode('#-#',$line);

        //Convert the line time to an actual date
        $line['timestamp'] = $date.' '.$line[0];
        unset($line[0]);

        //Turn the type into a named key
        $line['type'] = $line[1];
        unset($line[1]);

        //Converts ckeyless mobs into just the name of the mob
        $line['text'] = str_replace('*no key*/', '', $line[2]);
        $line['text'] = trim(rtrim($line['text']));
        unset($line[2]);

        //Set defaults for line coordinates
        $line['x'] = null;
        $line['y'] = null;
        $line['z'] = null;

        //If we have coordinates, set them to the named keys
        if(isset($line[3])){
          $coords = explode(',', $line[3]);
          $line['x'] = (int) $coords[0];
          $line['y'] = (int) $coords[1];
          $line['z'] = (int) $coords[2];
          unset($line[3]);
        }
        //Append to the lines array
        $lines[] = (object) $line;
      }
      fclose($handle);
    }
    usort($lines, function($a, $b) {
      return $a->timestamp <=> $b->timestamp;
    });

    $this->logs = $lines;
    unset($lines);
  }

  public function commitLogsToDB(){
    $db = new database(TRUE);
    $db->query("INSERT INTO round_logs
      (round, `timestamp`, type, `text`, x, y, z, added)
      VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $db->bind(1, $this->round->id);
    foreach($this->logs as $log){
      $db->bind(2, $log->timestamp);
      $db->bind(3, $log->type);
      $db->bind(4, $log->text);
      $db->bind(5, $log->x);
      $db->bind(6, $log->y);
      $db->bind(7, $log->z);
      try {
        $db->execute();
      } catch (Exception $e) {
        return returnError("Database error: ".$e->getMessage());
      }
    }
  }

  public function commitExplosionsToDB(){
    $db = new database(TRUE);
    $db->query("INSERT IGNORE INTO explosion_log
      (round, `time`, devestation, heavy, light, flash, area, x, y, z, added)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    foreach($this->explosions as $e){
      $db->bind(1, $this->round->id);
      $db->bind(2, $e->time);
      $db->bind(3, $e->devestation);
      $db->bind(4, $e->heavy);
      $db->bind(5, $e->light);
      $db->bind(6, $e->flash);
      $db->bind(7, $e->area);
      $db->bind(8, $e->x);
      $db->bind(9, $e->y);
      $db->bind(10,$e->z);
      try {
        $db->execute();
      } catch (Exception $e) {
        return returnError("Database error: ".$e->getMessage());
      }
    }
  }

  public function resetLogs(){
    $db = new database(TRUE);
    $db->query("DELETE FROM round_logs WHERE round = ?");
    $db->bind(1, $this->round->id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $db->query("DELETE FROM explosion_log WHERE round = ?");
    $db->bind(1, $this->round->id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    echo parseReturn(returnSuccess("Logs reset for ".$this->round->id));
  }

  public function getStatus($round){
    $return = new stdclass;
    $return->status = false;
    $return->message = "Logs for round $round not yet generated";

    if (!$round) {
      $return->message = "No round ID specified";
      return $return;
    }

    $db = new database(TRUE);
    $db->query("SELECT added FROM round_logs WHERE round = ? LIMIT 0,1");
    $db->bind(1, $round);
    try {
      if(!$added = $db->single()){
        return $return;
      } else {
        $return->status = true;
        $return->message = "Generated at $added->added";
        return $return;
      }
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

  public function generateRoundLogs($round){
    $return = new stdclass;
    $this->round = new round($round);
    $this->generateURLs();
    if($this->getStatus($this->round->id)->status){
      $return->status = true;
      $return->message = "Logs already exist for round ".$this->round->id;
      return $return;
    }
    $this->getLogs();
    $count = count($this->logs);
    $return->status = true;
    $return->message = "Generated $count lines of logs";
    return $return;
  }

  public function parseExplosion($line){
    $e = new stdclass;
    $e->time = $line['timestamp'];
    $e->x = $line['x'];
    $e->y = $line['y'];
    $e->z = $line['z'];
    $line['text'] = str_replace('Explosion with size (', '', $line['text']);
    $line['text'] = explode(") in area ", $line['text']);
    $e->area = $line['text'][1];
    $dam = explode(',',$line['text'][0]);
    $e->devestation = (int) $dam[0];
    $e->heavy       = (int) $dam[1];
    $e->light       = (int) $dam[2];
    $e->flash       = (int) $dam[3];
    $this->explosions[] = $e;
  }

  public function query(array $query, $page = 0, $count=PER_PAGE){
    // if(!$query['context'] || 100 < $query['context']){
    //   $query['context'] = 10;
    // }
    $count = 100; //Bypassing DEFINE
    $page = $page * $count;
    $and = null;
    if($query['type']) {
      $and = "AND `type` = ?";
    }
    $db = new database(TRUE);
    $db->query("SELECT * FROM round_logs
    WHERE `text` LIKE ?
    $and
    LIMIT $page, $count");
    $db->bind(1, '%'.$query['content'].'%');
    if($and){
      $db->bind(2, $query['type']);
    }
    try {
      return $db->resultset();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

}