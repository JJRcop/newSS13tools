<?php class round {

  public $start = false;
  public $end = false;
  public $server = false;
  public $round_id = false;
  public $logURL = false;
  public $logs = false;
  public $deaths = false;

  public function __construct($round=null,$data=false,$logs=false){
    if ($round) {
      $round = $this->getRound($round);
      $round = $this->parseRound($round);
      foreach ($round as $key => $value){
        $this->$key = $value;
      }
      if ($this->start && $this->server){
        $this->logs = true;
      }
      if ($data){
        $this->data = new stdClass;
        $feedback = $this->getRoundFeedback($this->round_id);
        $feedback = $this->parseRoundFeedback($feedback);
        foreach($feedback as $data){
          $name = $data->var_name;
          $this->data->$name['value'] = $data->var_value;
          $this->data->$name['details'] = $data->details;
        }
        //Commented out. Right now, this polls deaths from both servers,
        //which isn't super useful.
        // if ($this->start){
        //   $death = new death();
        //   $this->deaths = $death->getDeathsInRange($round->start, $round->end);
        // }
      }
      if ($logs){
        if (!$this->start && !$this->server){
          $this->logs = false;
        } else {
          $this->logs = $this->getLogs($round);
          $this->logs = $this->parseLogs($this->logs);
        }
      }
    }
    return $round;
  }

  public function parseRound(&$round){
    $round->server = $this->mapServer($round->server);
    if ($round->start) $round->start = date("Y-m-d H:i:s",strtotime($round->start));
    $round->end = date("Y-m-d H:i:s",strtotime($round->end));
    $round->logURL = REMOTE_LOG_SRC.strtolower($round->server)."/logs/".date('Y/m-F/d-l',strtotime($round->end)).".txt";
    return $round;
  }

  public function getRound($round){
    $db = new database();
    $db->query("SELECT ss13feedback.details AS `end`,
        start.details AS `start`,
        server.details AS `server`,
        ss13feedback.round_id
        FROM ss13feedback
        LEFT JOIN ss13feedback AS `server` ON ss13feedback.round_id = server.round_id AND server.var_name = 'server_ip'
        LEFT JOIN ss13feedback AS `start` ON ss13feedback.round_id = start.round_id AND start.var_name = 'round_start'
        WHERE ss13feedback.var_name = 'round_end'
        AND ss13feedback. round_id = $round");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }

  public function getRoundFeedback($round){
    $db = new database();
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

  public function getLogs($round){
    $logs = $this->getRemoteLogs($round->logURL);
    $lines = $this->findRoundBounds($logs,$round);
    $logs = $this->getLinesFromLogs($logs,$lines);
    return $logs;
  }

  public function getRemoteLogs($url){
    $file = str_replace(REMOTE_LOG_SRC, '', $url);
    $file = str_replace("/", '-', $file);
    if (!is_file("../".TMPDIR."/".$file.".json")){
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

    $logs = str_replace("-censored(misc)-\r\n",'',$logs);
    $logs = str_replace("-censored(asay/apm/ahelp/notes/etc)-\r\n",'',$logs);
    $logs = str_replace("-\r\n", '', $logs);
    $logs = str_replace("<span class='boldannounce'>",'',$logs);
    $logs = str_replace('</spam>', '', $logs);
    $logs = str_replace('*no key*/', '', $logs);
    $logs = str_replace(')) : ',') : ',$logs);
    $logs = preg_replace("/(\[)(\d{2}:\d{2}:\d{2})(])(GAME|ACCESS|SAY|OOC|ADMIN|EMOTE|WHISPER|PDA|CHAT|LAW|PRAY|COMMENT)(:\s)/","$2#-#$4#-#", $logs);
    $logs = utf8_encode($logs);
    $logs = explode("\r\n",$logs);
    array_filter($logs);

    $file = fopen("../".TMPDIR."/".$file.".json","w+");
    fwrite($file,json_encode($logs,JSON_UNESCAPED_UNICODE));
    fclose($file);
    } else {
      $logs = json_decode(file_get_contents("../".TMPDIR."/".$file.".json"));
    }
    return $logs;
  }

  public function findRoundBounds($logs,$round){
    function diffSort($a, $b) {
      return ($a[3] < $b[3]) ? -1 : 1;
    }
    
    $startTime = strtotime(explode(' ', $round->start)[1]);
    $endTime = strtotime(explode(' ', $round->end)[1]);
    $i = -1;
    $bounds = array();
    foreach ($logs as $log){
      $i++;
      if (strpos($log,'Loading Banlist')) {
        $bounds['start'][$i] = explode('#-#',$log);
      }
      if (strpos($log,'Rebooting World. Round ended.')) {
        $bounds['end'][$i] = explode('#-#',$log);
      }
    }

    foreach ($bounds['start'] as $line => &$start){
      $start[3] = abs($startTime - strtotime($start[0]));
      $start[4] = $line;
    }

    foreach ($bounds['end'] as $line => &$end){
      $end[3] = abs($endTime - strtotime($end[0]));
      $end[4] = $line;
    }

    usort($bounds['start'],'diffsort');
    usort($bounds['end'],'diffsort');

    $return = array();
    $return['start'] = $bounds['start'][0][4];
    $return['end'] = $bounds['end'][0][4];
    return $return;
  }

  public function getLinesFromLogs($logs, $lines){
    $logs = array_slice($logs, $lines['start']+1,$lines['end']-$lines['start']-1);
    return $logs;
  }

  public function parseLogs(&$logs){
    $i = 0;
    foreach ($logs as &$log){
      $i++;
      $ld = explode('#-#',$log);
      @$log = "<tr id='L-$i' class='".$ld[1]."'><td class='ln'>#$i</td><td class='ts'>[".$ld[0]."]";
      @$log.= "</td><td class='lt'>".$ld[1].": </td><td>";
      @$log.= $ld[2];
      @$log.="</td></tr>";
    }
    return $logs;
  }

  public function listRounds($offset=0, $count=30){
    $db = new database();
    $database = $db->query("SELECT ss13feedback.round_id,
      server.details AS `server`,
      mode.details AS game_mode,
      end.details AS `end`
      FROM ss13feedback
      LEFT JOIN ss13feedback AS `server` ON ss13feedback.round_id = server.round_id AND server.var_name = 'server_ip'
      LEFT JOIN ss13feedback AS `mode` ON ss13feedback.round_id = mode.round_id AND mode.var_name = 'game_mode'
      LEFT JOIN ss13feedback AS `end` ON ss13feedback.round_id = end.round_id AND end.var_name = 'round_end'
      WHERE ss13feedback.var_name='round_end'
      ORDER BY ss13feedback.time DESC
      LIMIT $offset, $count;");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }

  public function getLogRange($roundend,$server,$offset=0,$count=10000) {
    $db = new database(TRUE);
    //Finding the round's roundend starting logline
    $db->query("SELECT id FROM tglogs
      WHERE logtype = 'game'
      AND content LIKE '%Rebooting World. Round ended.%'
      AND `timestamp` > DATE_SUB('$roundend',INTERVAL 10 MINUTE) LIMIT 1;");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $roundend = $db->single()->id;
    //Finding the round's roundstart line
    $db->query("SELECT MAX(id) AS start
      FROM tglogs
      WHERE `id` < ?
      AND logtype = 'ADMIN'
      AND content LIKE '%Loading Banlist%'
      LIMIT 1");
    $db->bind(1,$roundend);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $roundstart = $db->single()->start;

    //Pull down logs
    $db->query("SELECT * FROM tglogs WHERE `id` <= $roundend AND `id` >= $roundstart LIMIT $offset, $count");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }

  public function mapServer($ip) {
    switch ($ip){
      case '172.93.110.246:2337':
        return 'Basil';
      break;

      case '172.93.110.246:1337':
        return 'Sybil';
      break;

      default: 
        return $ip;
      break;
    }
  }

  public function parseRoundFeedback(&$feedback){
    foreach($feedback as &$data){
      switch($data->var_name){
        case 'slime_core_harvested':
        case 'handcuffs':
        case 'zone_targeted':
        case 'admin_verb':
        case 'traitor_success':
        case 'traitor_objective':
        case 'cargo_imports':
        case 'gun_fired':
        case 'food_harvested':
        case 'item_used_for_combat':
        case 'slime_babies_born':
        case 'ore_mined':
        case 'chemical_reaction':
        case 'cell_used':
        case 'mobs_killed_mining':
        case 'slime_cores_used':
        case 'object_crafted':
        case 'food_made':
        case 'traitor_uplink_items_bought':
        case 'item_printed':
        case 'mining_equipment_bought':
        case 'cult_runes_scribed':
        case 'changeling_objective':
        case 'changeling_success':
        case 'changeling_powers':
        case 'wizard_success':
        case 'wizard_objective':
        case 'event_ran':
        case 'wizard_spell_learned':
        case 'admin_secrets_fun_used':
        case 'item_deconstructed':
        case 'mining_voucher_redeemed':
        case 'export_sold_amount':
        case 'export_sold_cost':
          $data->details = array_count_values(explode(' ',$data->details));
        break;

        case 'ban_job':
          $data->details = explode('-_',trim($data->details));
        break;

        case 'job_preferences':
          $data->details = explode('|-'," ".$data->details);
          array_pop($data->details);
          $prefs = array();
          foreach ($data->details as &$job){
            $job = str_replace(' |','',$job);
            $job = str_replace('_',' ',$job);
            if ($job{0} == '|') $job{0} = '';
            $job = explode('|',$job);
            foreach ($job as &$stat){
              if (strpos($stat,'=')){
                $stat = explode('=',$stat);
              }
            }
            $prefs[$job[0]]['HIGH'] = $job[1][1];
            $prefs[$job[0]]['MEDIUM'] = $job[2][1];
            $prefs[$job[0]]['LOW'] = $job[3][1];
            $prefs[$job[0]]['NEVER'] = $job[4][1];
            $prefs[$job[0]]['BANNED'] = $job[5][1];
            $prefs[$job[0]]['YOUNG'] = $job[6][1];
          }
          $data->details = $prefs;
          
        break;
      }
    }
    return $feedback;
  }

}