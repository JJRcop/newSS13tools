<?php class round {

  public $data;
  public $roundid;
  public $roundend;
  public $roundendsql;
  public $logrange;
  public $rawdata;

  public function __construct($round=null,$logs=false){
    $this->stats = new stdClass;
    if ($round) {
      $round = $this->getRound($round);
      $round = $this->parseRound($round);
      foreach ($round as $data){
        $name = $data->var_name;
        $this->data->$name['value'] = $data->var_value;
        $this->data->$name['details'] = $data->details;
      }
      if ($logs){
        $this->logrange = $this->getLogRange($this->roundendsql);
      }
    }
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

  public function getRound($round){
    $db = new database();
    $db->query("SELECT * FROM tbl_feedback WHERE round_id = ?");
    $db->bind(1,$round);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return  $db->resultset();
  }

  public function getLogRange($roundend,$offset=0,$count=1000) {
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

  public function parseRound(&$round){
    foreach($round as &$data){

      switch($data->var_name){

        case 'round_end':
          $this->roundend = $data->details;
          $this->roundid = $data->round_id;
          $this->roundendsql = date("Y-m-d H:i:s",strtotime($data->details));
        break;

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

        case 'round_end_result':
          $this->round_end_result = $data->details;
        break;

        case 'game_mode':
          $this->game_mode = $data->details;
        break;

        case 'job_preferences':
          $data->details = explode('|-'," ".$data->details);
          array_pop($data->details);
          foreach ($data->details as &$job){
            $job = str_replace(' |','',$job);
            if ($job{0} == '|') $job{0} = '';
            $job = explode('|',$job);
            foreach ($job as &$stat){
              if (strpos($stat,'=')){
                $stat = explode('=',$stat);
              }
            }
          }
        break;

        case 'server_ip':
          switch ($data->details){
            case '172.93.110.246:2337':
              $this->server = 'Basil';
            break;

            case '172.93.110.246:1337':
              $this->server = 'Sybil';
            break;

            default: 
              $this->server = $data->details;
            break;
          }
      }
    }
    return $round;
  }

}