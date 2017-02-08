<?php

class stat {

  public $name;
  public $value;
  public $details;
  public $rounds;

  public function __construct($stat=false, $server=false, $start=false, $end=false){
    if ($stat){
      if(!$this->isStat($stat)) return false;
      $stat = $this->getStat($stat);
      $stat = $this->parseStat($stat);
      return $stat;
    }
    return false;
  }

  public function parseStat(&$stat){
    $this->last = $stat[0]->time;
    $this->first = $stat[count($stat)-1]->time;
    $fb = new stdClass;
    $fb->details = '';
    $fb->rounds = '';
    $fb->value = 0;
    $fb->var_name = $stat[0]->var_name;
    $this->name = $stat[0]->var_name;
    foreach ($stat as $s){
      if($s->var_value) $fb->value+= $s->var_value;
      if($s->details) $fb->details.= $s->details.", ";
      if($s->round_id) $fb->rounds.= $s->round_id." "; 
    }
    $fb->details = trim($fb->details);
    $fb->rounds = trim($fb->rounds);
    $fb = $this->parseFeedback($fb);
    $this->value = $fb->value;
    $this->details = $fb->details;
    $this->rounds = count(explode(' ',$fb->rounds));
    return $stat;
  }

  public function getStat($stat){
    $db = new database();
    $db->query("SELECT ss13feedback.*,
      server.details as `server`
      FROM ss13feedback
      LEFT JOIN ss13feedback AS `server` ON ss13feedback.round_id = server.round_id AND server.var_name = 'server_ip'
      WHERE DATE(ss13feedback.time) BETWEEN (NOW() - INTERVAL 30 DAY) AND NOW()
      AND ss13feedback.var_name = ?
      ORDER BY `time` DESC;
    ");
    $db->bind(1,$stat);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultSet();
  }

  public function isStat($stat){
    $db = new database();
    $db->query("SELECT round_id FROM tbl_feedback WHERE var_name = ? LIMIT 0,1");
    $db->bind(1,$stat);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }

    public function parseFeedback(&$data){
    if ('object' != gettype($data)) return false;
    switch($data->var_name){
      case 'traitor_objective':
      case 'wizard_objective':
      case 'changeling_objective':
        $data->details = array_count_values(explode(' ',$data->details));
        $objs = array();
        foreach ($data->details as $obj => $count){
          $obj = explode('|',$obj);
          $objective = str_replace('/datum/objective/', '',$obj[0]);
          $status = str_replace(',', '', $obj[1]);
          if (array_key_exists($objective, $objs)){
            @$objs[$objective][$status]+= $count;
          } else {
            @$objs[$objective][$status]+= $count;
          }
        }
        $data->details = $objs;
        
      break;

      case 'ban_job':
        $data->details = explode('-_',trim($data->details));
      break;

      case 'radio_usage':
        $data->details = explode(' ',$data->details);
        $radio = array();
        foreach ($data->details as &$channel){
          $channel = explode('-',$channel);
        }
        foreach ($data->details as $c) {
          if(isset($radio[$c[0]])){
            $radio[$c[0]]+= $c[1]+0;
          } else {
            $radio[$c[0]]= $c[1]+0;
          }
        }
        arsort($radio);
        $data->details = $radio;
      break;

      case 'job_preferences':
        $data->details = str_replace(',', '', $data->details);
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
          @$prefs[$job[0]]['HIGH']  += $job[1][1];
          @$prefs[$job[0]]['MEDIUM']+= $job[2][1];
          @$prefs[$job[0]]['LOW']   += $job[3][1];
          @$prefs[$job[0]]['NEVER'] += $job[4][1];
          @$prefs[$job[0]]['BANNED']+= $job[5][1];
          @$prefs[$job[0]]['YOUNG'] += $job[6][1];
        }
        $data->details = $prefs;
      break;

      case 'slime_core_harvested':
      case 'handcuffs':
      case 'zone_targeted':
      case 'admin_verb':
      case 'traitor_success':
      case 'cargo_imports':
      case 'gun_fired':
      // case 'food_harvested':
      case 'item_used_for_combat':
      case 'slime_babies_born':
      // case 'ore_mined':
      // case 'chemical_reaction':
      case 'cell_used':
      case 'mobs_killed_mining':
      case 'slime_cores_used':
      case 'object_crafted':
      case 'food_made':
      case 'traitor_uplink_items_bought':
      // case 'item_printed':
      case 'mining_equipment_bought':
      case 'cult_runes_scribed':
      case 'changeling_success':
      case 'changeling_powers':
      case 'wizard_success':
      case 'event_ran':
      case 'wizard_spell_learned':
      case 'admin_secrets_fun_used':
      case 'item_deconstructed':
      case 'mining_voucher_redeemed':
      case 'export_sold_amount':
      // case 'export_sold_cost':
      case 'pick_used_mining':
      case 'clockcult_scripture_recited':
      case 'hivelord_core':
      case 'circuit_printed':
      case 'high_research_level':
      case 'engine_started':
      case 'assembly_made':
      case 'religion_book':
      case 'religion':
      case 'chaplain_weapon':
        $data->details = str_replace(', ', ' ', $data->details);
        $data->details = str_replace(',', '', $data->details);
        $data->details = array_count_values(explode(' ',$data->details)); 
      break;

      case 'round_end_result':
      case 'game_mode':
      case 'religion_name':
      case 'religion_deity':
        $data->details = rtrim($data->details,",");
        $data->details = array_count_values(explode(', ',$data->details));
      break;

      case 'ore_mined':
      case 'chemical_reaction':
      case 'item_printed':
      case 'export_sold_cost':
      case 'food_harvested':
        $data->details = str_replace(', ', ' ', $data->details);
        $data->details = rtrim($data->details,",");
        $data->details = array_count_values(explode(' ',$data->details));
        $clean = [];
        foreach ($data->details as $key => $val){
          $key = explode('|',$key);
          @$newVal = $val * $key[1];
            @$clean[$key[0]]+=$newVal;
        }
        $data->details = $clean;
      break;
    }
    if (is_array($data->details)) arsort($data->details);
    return $data;
  }

  public function getModeData($mode){
    return $mode;
  }

}