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

  public function __construct($id=null,$data=false,$logs=false,$json=false) {
    if ($id){
      $round = $this->getRound($id);

      //If we found the round, parse it...
      if(!$round) return false;
      $round = $this->parseRound($round);

      //Set properties from round parse
      foreach ($round as $k => $v){
        $this->$k = $v;
      }

      //Get round feedback
      if ($data){
        $this->data = new stdClass;
        $feedback = $this->getRoundFeedback($this->round_id);
        $feedback = $this->parseRoundFeedback($feedback);
        
        //This is ugly, but it prevents us from having to loop through every
        //statistic if we want to look up something
        //There is probably a better way to do this though
        foreach($feedback as $data){
          $name = $data->var_name;
          if (!$name) continue; //Fixes round #65941 which had an empty var
          $this->data->$name['value'] = $data->var_value;
          $this->data->$name['details'] = $data->details;
        }

        //Because I'm lazy
        if ( isset($this->data->traitor_objective)
          || isset($this->data->wizard_objective)
          || isset($this->data->changeling_objective)) {
          $this->hasObjectives = true;
        }
      }

      //Or we're pulling down logs
      if ($logs){
        if (!$this->status){
          $this->logs = false;
        } else {
            $logs = $this->getlogs($round,$json);
            if (!$json) {
              $this->logs = $this->parselogs($logs,$round);
            } else {
              $this->logs = $logs;
            }
          }
        }
      return $round;
    }
    return false;
  }

  public function parseRound(&$round){
    if ($round->start) $round->start = date("Y-m-d H:i:s",strtotime($round->start));
    $round->end = date("Y-m-d H:i:s",strtotime($round->end));
    $round->server = $this->mapServer($round->server);
    if ($round->start && $round->game_mode){
      $round->logs = true; //Round has logs
      $round->logURL = REMOTE_LOG_SRC.strtolower($round->server)."/logs/".date('Y/m-F/d-l',strtotime($round->end)).".txt";

      //End state doesn't get called if the map changes or something, so we can
      //set this manually if all the conditions are met
      if (!$round->status) $round->status = 'proper completion';
    } else {
      if (!$round->status) $round->status = false;
    }
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
    $db->query("SELECT ss13feedback.details AS `end`,
        start.details AS `start`,
        server.details AS `server`,
        ss13feedback.round_id,
         mode.details AS game_mode,
        TIMEDIFF(STR_TO_DATE(ss13feedback.details,'%a %b %d %H:%i:%s %Y'),STR_TO_DATE(start.details,'%a %b %d %H:%i:%s %Y')) AS duration,
        IF (proper.details IS NULL, error.details, proper.details) AS `status`,
        MAX(next.round_id) AS `next`,
        MIN(prev.round_id) AS `prev`
        FROM ss13feedback
        LEFT JOIN ss13feedback AS `server` ON ss13feedback.round_id = server.round_id AND server.var_name = 'server_ip'
        LEFT JOIN ss13feedback AS `start` ON ss13feedback.round_id = start.round_id AND start.var_name = 'round_start'
        LEFT JOIN ss13feedback AS `error` ON ss13feedback.round_id = error.round_id AND error.var_name = 'end_error'
        LEFT JOIN ss13feedback AS `proper` ON ss13feedback.round_id = proper.round_id AND proper.var_name = 'end_proper'
        LEFT JOIN ss13feedback AS `mode` ON ss13feedback.round_id = mode.round_id AND mode.var_name = 'game_mode'
        LEFT JOIN ss13feedback AS `next` ON next.round_id = ss13feedback.round_id + 1
        LEFT JOIN ss13feedback AS `prev` ON prev.round_id = ss13feedback.round_id - 1
        WHERE ss13feedback.var_name = 'round_end'
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

  public function getLogs($round,$json=false){
    if ($round->start && $round->end && $round->server){
      $round->start = date('H:i:s',strtotime($round->start));
      $round->end = date('H:i:s',strtotime($round->end));
    } else {
      return false;
    }
    $logsavefile = "../".TMPDIR."/".$round->round_id."-".$round->server."-logs.json";
    $this->fromCache = FALSE;
    //Check if we've already saved logs for this round
    if (file_exists($logsavefile)){
      $this->fromCache = TRUE;
      //If so, spew em out
      if($json){
        $logs = file_get_contents($logsavefile);
      } else {
        $logs = json_decode(file_get_contents($logsavefile));
      }
    } else {
      //If not, retrieve them
      $this->fromCache = FALSE;
      $logs = $this->getRemoteLogs($round->logURL);
      $logs = $this->cleanUpLogs($logs);
      $lines = $this->findRoundBounds($logs,$round);
      $logs = $this->getLinesFromLogs($logs,$lines);
      foreach ($logs as &$log){
        $log = explode('#-#',$log);
      }
      //Cache locally
      $logsavefile = fopen($logsavefile,"w+");
      fwrite($logsavefile,json_encode($logs,JSON_UNESCAPED_UNICODE));
      fclose($logsavefile);
    }
    return $logs;
  }

  public function getRemoteLogs($url){
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

  public function cleanUpLogs(&$logs){
    $logs = str_replace("-censored(misc)-\r\n",'',$logs);
    $logs = str_replace("-censored(asay/apm/ahelp/notes/etc)-\r\n",'',$logs);
    $logs = str_replace(" from -censored(ip/cid)- ",' ',$logs);
    $logs = str_replace(" : ",': ',$logs);
    $logs = str_replace("-\r\n", '', $logs);
    $logs = str_replace("<span class='boldannounce'>",'',$logs);
    $logs = str_replace('</span>', '', $logs);
    $logs = str_replace('*no key*/', '', $logs);
    $logs = str_replace(')) : ',') : ',$logs);
    $logs = preg_replace("/(\[)(\d{2}:\d{2}:\d{2})(])(GAME|ACCESS|SAY|OOC|ADMIN|EMOTE|WHISPER|PDA|CHAT|LAW|PRAY|COMMENT|VOTE)(:\s)/","$2#-#$4#-#", $logs);
    $logs = utf8_encode($logs);
    $logs = explode("\r\n",$logs);
    array_filter($logs);
    array_pop($logs);
    return $logs;
  }

  public function findRoundBounds($logs,$round){
    function diffSort($a, $b) {
      return ($a['diff'] < $b['diff']) ? -1 : 1;
    }
    $starts = array();
    $ends = array();
    $i = 0;
    foreach ($logs as $log){
      $i++;
      $log = explode('#-#',$log);
      $log['line'] = $i;
      $log[4] = strtotime($log[0]);
      if ('Loading Banlist' == $log[2]) {
        $starts[] = $log;
      }

      if (strpos($log[2],'Rebooting World. ') !== FALSE) {
        $ends[] = $log;
      }
    }

    $bounds['start'] = $starts;
    $bounds['end'] = $ends;


    foreach ($bounds['start'] as &$start){
      $start['diff'] = strtotime($round->start) - $start[4];
    }

    foreach ($bounds['end'] as &$end){
      $end['diff'] = abs(strtotime($round->end) - $end[4]);
    }

    usort($bounds['start'],'diffsort');
    usort($bounds['end'],'diffsort');

    $startline = 0;
    $endline = 0;
    foreach ($bounds['start'] as $start){
      if ($start['diff'] > 0) {
        $startline = $start['line'];
        break;
      }
    }

    foreach ($bounds['end'] as $end){
      $endline = $end['line'];
      break;
    }
    $return['start'] = $startline;
    $return['end'] = $endline-$startline;
    return $return;
  }

  public function getLinesFromLogs($logs, $lines){
    $logs = array_slice($logs, $lines['start']+1,$lines['end']);
    return $logs;
  }

  public function parseLogs(&$logs, $round){
    $i = 0;
    foreach ($logs as &$log){
      $i++;
      $ld = $log;
      // if (strpos($ld[2],' has renamed the station as ') !== FALSE){
      //   $this->attachStationNameToRoundID($ld[2],$round);
      // }
      @$log = "<tr id='L-$i' class='".$ld[1]."'><td class='ln'><a href='#L-$i'>#$i</a></td><td class='ts'>[".$ld[0]."]";
      @$log.= "</td><td class='lt'>".$ld[1].": </td><td>";
      @$log.= $ld[2];
      @$log.="</td></tr>";
    }
    return $logs;
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
      IF (proper.details IS NULL, error.details, proper.details) AS `status`
      FROM ss13feedback
      LEFT JOIN ss13feedback AS `server` ON ss13feedback.round_id = server.round_id AND server.var_name = 'server_ip'
      LEFT JOIN ss13feedback AS `mode` ON ss13feedback.round_id = mode.round_id AND mode.var_name = 'game_mode'
      LEFT JOIN ss13feedback AS `end` ON ss13feedback.round_id = end.round_id AND end.var_name = 'round_end'
      LEFT JOIN ss13feedback AS `start` ON ss13feedback.round_id = start.round_id AND start.var_name = 'round_start'
      LEFT JOIN ss13feedback AS `error` ON ss13feedback.round_id = error.round_id AND error.var_name = 'end_error'
      LEFT JOIN ss13feedback AS `proper` ON ss13feedback.round_id = proper.round_id AND proper.var_name = 'end_proper'
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

  public function parseRoundFeedback(&$feedback){
    foreach($feedback as &$data){
      $stat = new stat();
      $data = $stat->parseFeedback($data);
    }
    return $feedback;
  }

}