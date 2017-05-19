<?php class round {

  public $start = false;
  public $end = false;
  public $duration = false;
  public $round_id = false;
  public $game_mode = false;
  public $server = false;
  public $logs = false;
  public $logURL = false;
  public $status = false;
  public $next = false;
  public $prev = false;
  public $hasObjectives = false;
  public $fromCache = false;
  public $data = false;

  public function __construct($round=null,$data=false) {
    if($round){
      $round = $this->getRound($round);
      $round = $this->parseRound($round);
      // var_dump($round);
      if(is_array($data)){
        foreach ($data as $get){
          switch ($get){

            //Getting and parsing round logs
            case 'logs':
              if ($round->logs){
                $round = $this->getRoundLogs($round);
              } else {
                $round->logs = false;
              }
            break;

            //Get and parse round stats(feedback)
            case 'data':
              $data = $this->getRoundFeedback($round->round_id);
              $data = $this->parseRoundFeedback($data);
              $round->data = new StdClass();
              foreach ($data as $d){
                $round->data->{$d->var_name} = $d;
              }
            break;

          }
        }
      }
      foreach ($round as $k => $v){
        $this->$k = $v;
      }
      // var_dump($round);
      return $round;
    } else {
      return false;
    }
  }

  public function parseRound(&$round){
    if ($round->start) $round->start = date("Y-m-d H:i:s",strtotime($round->start));
    $round->end = date("Y-m-d H:i:s",strtotime($round->end));
    $round->server = $this->mapServer($round->server);
    if ($round->start && $round->end && $round->server){
      $round->logs = true; //Round has logs we can find
      $round->logURL = REMOTE_LOG_SRC.strtolower($round->server)."/logs/";
      $round->logURL.= date('Y/m-F/d-l',strtotime($round->end)).".txt";
      $round->logCache = ROOTPATH."/tmp/".$round->round_id."-".$round->server."-logs.json";
      //End state doesn't get called if the map changes or something, so we can
      //set this manually if all the conditions are met
      if (!$round->status) $round->status = 'proper completion';
    } else {
      if (!$round->status) $round->status = false;
    }
    if (!$round->result) {
      $round->result = ucfirst($round->status);
    }
    $round->modeIcon = json_decode(file_get_contents(ROOTPATH."/inc/mode.json"),TRUE)[$round->game_mode];
    $round->modeIcon = "<i class='fa fa-fw fa-$round->modeIcon'></i> ";

    $round->status = ucfirst($round->status);
    $round->permalink = APP_URL."rounds/viewRound.php?round=$round->round_id";
    $round->link = "<a href='$round->permalink'>#$round->round_id</a>";
    return $round;
  }

  public function mapStatus($status = null){
    if(!$status) return false;
    if(strpos($status, 'admin reboot - ')!==FALSE) return false;
    switch ($status){
      case 'proper completion':
      default:
        return TRUE;
      break;

      case 'nuke':
        return TRUE;
      break;

      case 'restart vote':
        return FALSE;
      break;
    }
  }

  public function getRound($id){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT ss13feedback.round_id,
      server.details AS `server`,
      mode.details AS game_mode,
      STR_TO_DATE(end.details,'%a %b %d %H:%i:%s %Y') AS `end`,
      STR_TO_DATE(start.details,'%a %b %d %H:%i:%s %Y') AS `start`,
      TIMEDIFF(STR_TO_DATE(end.details,'%a %b %d %H:%i:%s %Y'),STR_TO_DATE(start.details,'%a %b %d %H:%i:%s %Y')) AS duration,
      IF (proper.details IS NULL, error.details, proper.details) AS `status`,
      MAX(next.round_id) AS `next`,
      MIN(prev.round_id) AS `prev`,
      result.details AS result,
      name.details AS name,
      map.details AS `map`
      FROM ss13feedback
      LEFT JOIN ss13feedback AS `server` ON ss13feedback.round_id = server.round_id AND server.var_name = 'server_ip'
      LEFT JOIN ss13feedback AS `mode` ON ss13feedback.round_id = mode.round_id AND mode.var_name = 'game_mode'
      LEFT JOIN ss13feedback AS `end` ON ss13feedback.round_id = end.round_id AND end.var_name = 'round_end'
      LEFT JOIN ss13feedback AS `start` ON ss13feedback.round_id = start.round_id AND start.var_name = 'round_start'
      LEFT JOIN ss13feedback AS `error` ON ss13feedback.round_id = error.round_id AND error.var_name = 'end_error'
      LEFT JOIN ss13feedback AS `proper` ON ss13feedback.round_id = proper.round_id AND proper.var_name = 'end_proper'
      LEFT JOIN ss13feedback AS `result` ON ss13feedback.round_id = result.round_id AND result.var_name = 'round_end_result'
      LEFT JOIN ss13feedback AS `name` ON ss13feedback.round_id = name.round_id AND name.var_name = 'station_renames'
      LEFT JOIN ss13feedback AS `next` ON next.round_id = ss13feedback.round_id + 1
      LEFT JOIN ss13feedback AS `prev` ON prev.round_id = ss13feedback.round_id - 1
      LEFT JOIN ss13feedback AS `map` ON ss13feedback.round_id = map.round_id AND map.var_name = 'map_name'
      WHERE ss13feedback.var_name='round_end'
      AND ss13feedback.round_id = ?");
    $db->bind(1, $id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }

  public function getRoundFeedback($round){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT var_name, var_value, details
      FROM tbl_feedback WHERE round_id = ?");
    $db->bind(1,$round);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return  $db->resultset();
  }

  public function getFeedback(){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT var_name, var_value, details
      FROM tbl_feedback WHERE round_id = ?");
    $db->bind(1,$round);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return  $db->resultset();
  }

    public function getRoundLogs(&$round){
    if(file_exists($round->logCache)){
      $round->logs = $this->getCachedLogs($round->logCache);
      $round->fromCache = TRUE;
    } else {
      $round->logs = $this->getRemoteLogs($round->logURL);
      $round = $this->extractRoundLogs($round);
      $round->fromCache = FALSE;
    }
    return $round;
  }

  public function getCachedLogs($cache){
    $logs = file_get_contents($cache);
    return json_decode($logs);
  }

  public function getRemoteLogs($url){
    $logs = $this->fetchRemoteLogs($url);
    $logs = $this->cleanUpLogs($logs);
    return $logs;
  }

  public function cleanUpLogs($logs){
    //This method is ONLY for cleaning out useless lines from the logs
    
    //For the sake of transparency, this method is heavily documented
    //
    //-censored lines have no value in public logs, and are removed entirely
    $logs = str_replace("-censored(misc)-\r\n",'',$logs);
    $logs = str_replace("-censored(sql logs)-\r\n",'',$logs);
    $logs = str_replace("-censored(asay/apm/ahelp/notes/etc)-\r\n",'',$logs);
    $logs = str_replace(" from -censored(ip/cid)- ",' ',$logs);
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
    $logs = preg_replace("/(\[)(\d{2}:\d{2}:\d{2})(])(GAME|ACCESS|SAY|OOC|ADMIN|EMOTE|WHISPER|PDA|CHAT|LAW|PRAY|COMMENT|VOTE|GHOST)(:\s)/","$2#-#$4#-#", $logs);

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

    return $logs;
  }

  public function fetchRemoteLogs($url){
    $file = str_replace(REMOTE_LOG_SRC, '', $url);
    $file = str_replace("/", '-', $file);
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_URL => $url,
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

  public function extractRoundLogs($round){
    $bounds = $this->getRoundBounds($round);
    $start = $bounds['start']['pos'];
    $end = $bounds['end']['pos'];
    $end = $end-$start+1;
    $round->logs = array_slice($round->logs, $start, $end);
    $this->cacheParsedLogs($round);
    return $round;
  }

  public function getRoundBounds($round){
    function diffSort($a, $b) {
      return ($a['diff'] < $b['diff']) ? -1 : 1;
    }

    $starts = array();
    $ends = array();
    $startTime = new DateTime($round->start);
    $endTime = new DateTime($round->end);
    $i = -1;
    foreach ($round->logs as $log){
      $i++;
      if ($log[1] === 'ADMIN' && $log[2] === 'Loading Banlist'){
        $log['time'] = $startTime->format("Y-m-d ").$log[0];
        $log['diff'] = abs(strtotime($log['time']) - strtotime($round->start));
        $log['pos'] = $i;
        $starts[] = $log;
      }
      if ($log[1] === 'GAME' && strpos($log[2],'Rebooting World.') !== FALSE){
        $log['time'] = $endTime->format("Y-m-d ").$log[0];
        $log['diff'] = abs(strtotime($log['time']) - strtotime($round->end));
        $log['pos'] = $i;
        $ends[] = $log;
      }
    }
    usort($starts,'diffsort');
    usort($ends,'diffsort');
    $return['start'] = $starts[0];
    $return['end'] = $ends[0];
    return $return;
  }

  public function cacheParsedLogs($round){
    $logsavefile = fopen($round->logCache,"w+");
    fwrite($logsavefile,json_encode($round->logs,JSON_UNESCAPED_UNICODE));
    fclose($logsavefile);
  }

  public function attachStationNameToRoundID($log, $round){
    $log = str_replace(' has renamed the station as ', '', $log);
    $log = str_replace(')', '/', $log);
    $log = str_replace('(', '', $log);
    $db = new database(TRUE);
    $db->query("INSERT INTO round_logs (round, `start`, `end`, server, station_name) VALUES(?,?,?,?,?)");
    $db->bind(1,$round->round_id);
    $db->bind(2,$round->start);
    $db->bind(3,$round->end);
    $db->bind(4,$round->server);
    $db->bind(5,explode('/',$log)[2]);
    try {
      //$db->execute();
    } catch (Exception $e) {
      return "Database error: ".$e->getMessage();
    }
  }

  public function listRounds($page=1, $count=30){
    $page = ($page*$count) - $count;
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $database = $db->query("SELECT ss13feedback.round_id,
      server.details AS `server`,
      mode.details AS game_mode,
      STR_TO_DATE(end.details,'%a %b %d %H:%i:%s %Y') AS `end`,
      STR_TO_DATE(start.details,'%a %b %d %H:%i:%s %Y') AS `start`,
      TIMEDIFF(STR_TO_DATE(end.details,'%a %b %d %H:%i:%s %Y'),STR_TO_DATE(start.details,'%a %b %d %H:%i:%s %Y')) AS duration,
      IF (proper.details IS NULL, error.details, proper.details) AS `status`,
      result.details AS result,
      map.details AS `map`
      FROM ss13feedback
      LEFT JOIN ss13feedback AS `server` ON ss13feedback.round_id = server.round_id AND server.var_name = 'server_ip'
      LEFT JOIN ss13feedback AS `mode` ON ss13feedback.round_id = mode.round_id AND mode.var_name = 'game_mode'
      LEFT JOIN ss13feedback AS `end` ON ss13feedback.round_id = end.round_id AND end.var_name = 'round_end'
      LEFT JOIN ss13feedback AS `start` ON ss13feedback.round_id = start.round_id AND start.var_name = 'round_start'
      LEFT JOIN ss13feedback AS `error` ON ss13feedback.round_id = error.round_id AND error.var_name = 'end_error'
      LEFT JOIN ss13feedback AS `proper` ON ss13feedback.round_id = proper.round_id AND proper.var_name = 'end_proper'
      LEFT JOIN ss13feedback AS `result` ON ss13feedback.round_id = result.round_id AND result.var_name = 'round_end_result'
      LEFT JOIN ss13feedback AS `map` ON ss13feedback.round_id = map.round_id AND map.var_name = 'map_name'
      WHERE ss13feedback.var_name='round_end'
      ORDER BY ss13feedback.time DESC
      LIMIT ?,?;");
    $db->bind(1,$page);
    $db->bind(2,$count);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $rounds = $db->resultset();
    foreach ($rounds as $round){
      $this->parseRound($round);
    }
    return $rounds;
  }

  public function countRounds() {
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT count(DISTINCT round_id) AS total FROM ss13feedback;");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single()->total;
  }

  public function getRoundsByMonth() {
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT count(DISTINCT round_id) AS rounds,
      concat(MONTH(ss13feedback.time),'-',YEAR(ss13feedback.time)) AS `date`,
      MIN(round_id) AS firstround,
      MAX(round_id) AS lastround
      FROM ss13feedback
      WHERE ss13feedback.time BETWEEN '2011-01-01' AND NOW()
      GROUP BY YEAR(ss13feedback.time), MONTH(ss13feedback.time) ASC;");
  }

  public function mapServer($ip) {
    if(':' == $ip{0}){
      $ip = explode(':',$ip);
      if (!isset($ip[1])) return 'Unknown';
      $ip = $ip[1];
    } else {
      $ip = str_replace(':', '', $ip);
    }

    //Per MSO, we should be looking at the port #s.
    switch ($ip){
      case '2337':
        return 'Basil';
      break;

      case '1337':
        return 'Sybil';
      break;

      default: 
        return 'Unknown';
      break;
    }
  }

  public function parseRoundFeedback(&$feedback){
    $stat = new stat();
    foreach($feedback as &$data){
      $data = $stat->parseFeedback($data);
    }
    return $feedback;
  }

  public function getRoundStat($round, $stat){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT * FROM tbl_feedback
      WHERE round_id = ? AND var_name = ?");
    $db->bind(1,$round);
    $db->bind(2,$stat);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $stat = new stat();
    $data = $db->single();
    $stat = $stat->parseFeedback($data);
    return $stat;
  }

}