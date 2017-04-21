<?php

class newRound {

  public function __construct($round=null,$data=false) {
    if($round){
      $round = $this->getRound($round);
      $round = $this->parseRound($round);

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
          }
        }
      }

      foreach ($round as $k => $v){
        $this->$k = $v;
      }
      return $round;
    } else {
      return false;
    }
  }

  public function parseRound(&$round){
    if ($round->start) $round->start = date("Y-m-d H:i:s",strtotime($round->start));
    $round->end = date("Y-m-d H:i:s",strtotime($round->end));
    $round->server = $this->mapServer($round->server);
    if ($round->start && $round->end){
      $round->logs = true; //Round has logs
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
    $round->status = ucfirst($round->status);
    $round->permalink = APP_URL."rounds/viewRound.php?round=$round->round_id";
    return $round;
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
      result.details AS result
      FROM ss13feedback
      LEFT JOIN ss13feedback AS `server` ON ss13feedback.round_id = server.round_id AND server.var_name = 'server_ip'
      LEFT JOIN ss13feedback AS `mode` ON ss13feedback.round_id = mode.round_id AND mode.var_name = 'game_mode'
      LEFT JOIN ss13feedback AS `end` ON ss13feedback.round_id = end.round_id AND end.var_name = 'round_end'
      LEFT JOIN ss13feedback AS `start` ON ss13feedback.round_id = start.round_id AND start.var_name = 'round_start'
      LEFT JOIN ss13feedback AS `error` ON ss13feedback.round_id = error.round_id AND error.var_name = 'end_error'
      LEFT JOIN ss13feedback AS `proper` ON ss13feedback.round_id = proper.round_id AND proper.var_name = 'end_proper'
      LEFT JOIN ss13feedback AS `result` ON ss13feedback.round_id = result.round_id AND result.var_name = 'round_end_result'
      LEFT JOIN ss13feedback AS `next` ON next.round_id = ss13feedback.round_id + 1
      LEFT JOIN ss13feedback AS `prev` ON prev.round_id = ss13feedback.round_id - 1
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

  public function getRoundStats($round){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    
  }

  public function mapServer($ip) {
    $ip = explode(':',$ip);
    if (!isset($ip[1])) return 'Unknown';

    //Per MSO, we should be looking at the port #s.
    switch ($ip[1]){
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

  public function getRoundLogs(&$round){
    if(file_exists($round->logCache)){
      $round->logs = $this->getCachedLogs($round->logCache);
      $round->fromCache = TRUE;
    } else {
      $round->logs = $this->getRemoteLogs($round->logURL);
      $round->fromCache = FALSE;
    }
    return $round;
  }

  public function getCachedLogs($cache){
    $logs = file_get_contents($cache);
    return json_decode($logs);
  }

  public function getRemoteLogs($URL){
    $logs = $this->fetchRemoteLogs($URL);
    $logs = $this->getLogs($logs);
    return $logs;
  }

  public function fetchRemoteLogs($URL){
    $file = str_replace(REMOTE_LOG_SRC, '', $URL);
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

}