<?php

class stat {

  public $var_name;
  public $value;
  public $details;
  public $rounds;
  public $first;
  public $last;
  public $total;

  public function __construct($stat=false, $server=false, $start=false, $end=false){
    if ($stat){
      if(!$this->isStat($stat)) return false;
      $stat = $this->getStat($stat);
      $stat = $this->parseStat($stat);
      foreach ($stat as $k => $v){
        $this->$k = $v;
      }
      return $stat;
    }
    return false;
  }

  public function parseStat(&$stat){
    $data = new stdClass;
    $data->var_name = $stat[0]->var_name;
    $data->rounds = null;
    $data->details = null;
    $data->value = 0;
    $data->last = $stat[0]->time;
    $data->total = count($stat);
    $data->first = $stat[$data->total-1]->time;
    foreach ($stat as $s){
      $data->rounds .= "$s->round_id, ";
      if($s->details) $data->details.= "$s->details, ";
      $data->value  += $s->var_value;
    }
    $data->details = rtrim($data->details);
    $data->details = rtrim($data->details,',');
    $data = $this->parseFeedback($data,TRUE);
    if('' == $data->details) $data->details = FALSE;
    return $data;
  }

  public function getStat($stat){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT ss13feedback.*,
      server.details as `server`
      FROM ss13feedback
      LEFT JOIN ss13feedback AS `server` ON ss13feedback.round_id = server.round_id AND server.var_name = 'server_ip'
      WHERE DATE(ss13feedback.time) BETWEEN (NOW() - INTERVAL 30 DAY) AND NOW()
      AND ss13feedback.var_name = ?
      ORDER BY `time` DESC;");
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
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT round_id FROM tbl_feedback WHERE var_name = ? LIMIT 0,1");
    $db->bind(1,$stat);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }

  public function parseFeedback(&$data,$tally=false){
    if ('object' != gettype($data)) return false;
    if ('' == $data->details) $data->details = FALSE;
    if (!isset($data->var_name)) return false;
    switch($data->var_name){
      //Ok, let's break this down, stat by stat. This is a nasty mess of code
      //but things should be broken up and organized into a somewhat coherent
      //way.

      //Default case. Duh.
      default:
        $data->details = $data->details;
      break;      

      //First up, stats that have a | value separator, but for stacks/amounts
      //So we need to add 'like' stats to themselves.
      //IE: Miners can mine a stack of 4 metal ore, and a stack of 5 metal ore
      //We need to add that together to give us 9 metal ore.
      case 'chemical_reaction':
      case 'ore_mined':
      case 'food_harvested':
      case 'item_printed':
        $data->details = array_count_values(explode(' ',$data->details));
        $tmp = array();
        foreach ($data->details as $k => $v){
          $a = explode('|',$k);
          $k = $a[0];
          @$a = $a[1];
          if(isset($tmp[$k])) {
            $tmp[$k] += $v * $a;
          } else {
            $tmp[$k] = $v * $a;
          }
        }
        $data->details = $tmp;
      break;

      //Job preferences!
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
          if ($tally){
            @$prefs[$job[0]]['HIGH']  += (int) $job[1][1];
            @$prefs[$job[0]]['MEDIUM']+= (int) $job[2][1];
            @$prefs[$job[0]]['LOW']   += (int) $job[3][1];
            @$prefs[$job[0]]['NEVER'] += (int) $job[4][1];
            @$prefs[$job[0]]['BANNED']+= (int) $job[5][1];
            @$prefs[$job[0]]['YOUNG'] += (int) $job[6][1];
          } else {
            $prefs[$job[0]]['HIGH'] = (int) $job[1][1];
            $prefs[$job[0]]['MEDIUM'] = (int) $job[2][1];
            $prefs[$job[0]]['LOW'] = (int) $job[3][1];
            $prefs[$job[0]]['NEVER'] = (int) $job[4][1];
            $prefs[$job[0]]['BANNED'] = (int) $job[5][1];
            $prefs[$job[0]]['YOUNG'] = (int) $job[6][1];
          }
        }
        $data->details = $prefs;
      break;

      //Radio usage
      case 'radio_usage':
        $data->details = explode(' ',$data->details);
        $radio = array();
        foreach ($data->details as &$channel){
          $channel = explode('-',$channel);
        }
        $total = 0;
        foreach ($data->details as $c) {
          if ($tally){
            if(isset($radio[$c[0]])){
              $radio[$c[0]]+= $c[1]+0;
            } else {
              $radio[$c[0]]= $c[1]+0;
            }
          } else {
            $radio[$c[0]] = $c[1]+0;
          }
          $total+= $c[1];
        }
        // if ($tally) $radio['total'] = $total;
        $data->details = $radio;
      break;

      //Objectives
      case 'traitor_objective':
      case 'wizard_objective':
      case 'changeling_objective':
      case 'cult_objective':
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
      case 'ban_job_tmp':
      case 'ban_job_unban':
        $data->details = str_replace('-_', '#', rtrim($data->details,','));
        $data->details = str_replace('_', ' ', $data->details);
        $data->details = str_replace(',', '', rtrim($data->details,','));
        $data->details = array_count_values(explode('#',$data->details));
        array_filter($data->details);
      break;

      //Everything else
      //case 'admin_cookies_spawned':
      case 'admin_secrets_fun_used':
      case 'admin_verb':
      // case 'alert_comms_blue':
      // case 'alert_comms_green':
      // case 'alert_keycard_auth_maint':
      // case 'alert_keycard_auth_red':
      // case 'arcade_loss_hp_emagged':
      // case 'arcade_loss_hp_normal':
      // case 'arcade_loss_mana_emagged':
      // case 'arcade_loss_mana_normal':
      // case 'arcade_win_emagged':
      // case 'arcade_win_normal':
      case 'assembly_made':
      // case 'ban_appearance':
      // case 'ban_appearance_unban':
      // case 'ban_edit':
      // case 'ban_job':
      // case 'ban_job_tmp':
      // case 'ban_job_unban':
      // case 'ban_perma':
      // case 'ban_tmp':
      // case 'ban_tmp_mins':
      // case 'ban_unban':
      // case 'ban_warn':
      // case 'benchmark':
      case 'cargo_imports':
      case 'cell_used':
      case 'changeling_objective':
      case 'changeling_powers':
      case 'changeling_success':
      case 'chemical_reaction':
      case 'circuit_printed':
      case 'clockcult_scripture_recited':
      case 'colonies_dropped':
      case 'comment':
      case 'cult_objective':
      case 'cult_runes_scribed':
      // case 'cyborg_ais_created':
      // case 'cyborg_birth':
      // case 'cyborg_engineering':
      // case 'cyborg_frames_built':
      // case 'cyborg_janitor':
      // case 'cyborg_medical':
      // case 'cyborg_miner':
      // case 'cyborg_mmis_filled':
      // case 'cyborg_peacekeeper':
      // case 'cyborg_security':
      // case 'cyborg_service':
      // case 'cyborg_standard':
      // case 'disposal_auto_flush':
      // case 'emergency_shuttle':
      // case 'end_error':
      // case 'end_proper':
      case 'engine_started':
      // case 'escaped_human':
      // case 'escaped_on_pod_1':
      // case 'escaped_on_pod_2':
      // case 'escaped_on_pod_3':
      // case 'escaped_on_pod_5':
      // case 'escaped_on_shuttle':
      // case 'escaped_total':
      case 'event_ran':
      case 'export_sold_amount':
      case 'export_sold_cost':
      // case 'food_harvested':
      case 'food_made':
      // case 'game_mode':
      case 'god_objective':
      case 'god_success':
      case 'gun_fired':
      case 'handcuffs':
      case 'high_research_level':
      case 'hivelord_core':
      case 'immortality_talisman':
      case 'item_deconstructed':
      case 'item_printed':
      case 'item_used_for_combat':
      case 'jaunter':
      // case 'job_preferences':
      case 'lazarus_injector':
      // case 'mecha_durand_created':
      // case 'mecha_firefighter_created':
      // case 'mecha_gygax_created':
      // case 'mecha_honker_created':
      // case 'mecha_odysseus_created':
      // case 'mecha_phazon_created':
      // case 'mecha_ripley_created':
      case 'megafauna_kills':
      case 'mining_adamantine_produced':
      case 'mining_clown_produced':
      case 'mining_diamond_produced':
      case 'mining_equipment_bought':
      case 'mining_glass_produced':
      case 'mining_gold_produced':
      case 'mining_iron_produced':
      case 'mining_plasma_produced':
      case 'mining_rglass_produced':
      case 'mining_silver_produced':
      case 'mining_steel_produced':
      case 'mining_uranium_produced':
      case 'mining_voucher_redeemed':
      case 'mobs_killed_mining':
      // case 'newscaster_channels':
      // case 'newscaster_newspapers_printed':
      // case 'newscaster_stories':
      // case 'nuclear_challenge_mode':
      case 'object_crafted':
      // case 'ore_mined':
      case 'pick_used_mining':
      // case 'radio_usage':

      // case 'round_end':
      // case 'round_end_clients':
      // case 'round_end_ghosts':
      // case 'round_start':
      // case 'server_ip':
      // case 'shuttle_fasttravel':
      case 'slime_babies_born':
      case 'slime_cores_used':
      case 'slime_core_harvested':
      case 'supply_mech_collection_redeemed':
      // case 'surgeries_completed':
      // case 'surgery_initiated':
      case 'surgery_step_failed':
      case 'surgery_step_success':
      // case 'survived_human':
      // case 'survived_total':
      case 'traitor_objective':
      case 'traitor_success':
      case 'traitor_uplink_items_bought':
      case 'warp_cube':
      case 'wisp_lantern':
      case 'wizard_objective':
      case 'wizard_spell_learned':
      case 'wizard_success':
      case 'zone_targeted':
        $data->details = str_replace(',', '', $data->details);
        $data->details = array_count_values(explode(' ',$data->details));
      break;

      case 'revision':
      case 'religion_book':
      case 'religion_deity':
      case 'religion_name':
      case 'chaplain_weapon':
      case 'game_mode':
      case 'server_ip':
      case 'end_proper':
      case 'end_error':
        if ($tally) $data->details = array_count_values(explode(', ',$data->details));
      break;

      case 'round_end_result':
        if ($tally) $data->details = array_count_values(explode(', ',$data->details));
      break;

      case 'shuttle_manipulator':
      case 'shuttle_purchase':
      case 'emergency_shuttle':
      case 'shuttle_fasttravel':
        $data->details = str_replace(',', '', $data->details);
        $data->details = str_replace(' ', '; ', $data->details);
        $data->details = str_replace('_', ' ', $data->details);
        if ($tally) $data->details = array_count_values(explode('; ',$data->details));
      break;

      case 'round_end':
      case 'round_start':
        $hours = array();
        foreach (explode(', ',$data->details) as $d){
          $hour = date('H',strtotime($d));
          @$hours[$hour]+= 1;
        }
        $data->details = $hours;
      break;
    }
    if (is_array($data->details)) arsort($data->details);
    return $data;
  }

  public function getModeData($mode){

    return $mode;
  }
  public function getStatsForMonth($start,$end) {
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SET SESSION group_concat_max_len = 1000000;"); //HONK
    $db->execute();
    $db->query("SELECT ss13feedback.var_name,
      GROUP_CONCAT(ss13feedback.round_id SEPARATOR ', ') AS rounds,
      SUM(ss13feedback.var_value) AS `value`,
      IF (ss13feedback.details = '', NULL, GROUP_CONCAT(ss13feedback.details SEPARATOR ', ')) AS details
      FROM ss13feedback
      WHERE DATE(ss13feedback.time) BETWEEN ? AND ?
      AND ss13feedback.var_name != ''
      GROUP BY ss13feedback.var_name
      ORDER BY ss13feedback.var_name ASC;");
    $db->bind(1,$start);
    $db->bind(2,$end);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $parse = new stat();
    foreach ($results = $db->resultset() as &$stat){
      $stat->total = count(explode(', ',$stat->rounds));
      $stat = $parse->parseFeedback($stat, TRUE);
    }
    return $db->resultset();
  }

  public function generateMonthlyStats($year,$month) {
    $date = new DateTime("Midnight $month/01/$year");
    $start = $date->format("Y-m-01 H:i:s");
    $end = $date->format('Y-m-t 23:59:59');
    $results = $this->getStatsForMonth($start, $end);
    foreach ($results as &$stat){
      $stat->total = count(explode(', ',$stat->rounds));
      $stat = $this->parseFeedback($stat, TRUE);
    }
    return $this->saveMonthlyStats($results,$year,$month)." for ".$date->format("Y-m");
  }

  public function SaveMonthlyStats($stats,$year,$month){
    $db = new database(TRUE);
    if($db->abort){
      return FALSE;
    }
    $db->query("INSERT INTO monthly_stats
      (rounds, roundcount, var_name, data, value, month, year)
      VALUES(?, ?, ?, ?, ?, ?, ?)");
    $i = 0;
    foreach ($stats as &$d){
      if ('' == $d->var_name) continue;
      if ($d->details && is_array($d->details)){
        $d->details = json_encode($d->details);
      }
      $db->bind(1,$d->rounds);
      $db->bind(2,$d->total);
      $db->bind(3,$d->var_name);
      $db->bind(4,$d->details);
      $db->bind(5,$d->value);
      $db->bind(6,$month);
      $db->bind(7,$year);
      try {
        $db->execute();
      } catch (Exception $e) {
        var_dump("Database error: ".$e->getMessage());
      }
      $i++;
    }
    $db->query("INSERT INTO tracked_months
      (year, month, stats)
      VALUES(?, ?, ?)");
    $db->bind(1,$year);
    $db->bind(2,$month);
    $db->bind(3,$i);
    try {
      $db->execute();
    } catch (Exception $e) {
      return "Database error: ".$e->getMessage();
    }
    return "Added $i stats to the database";
  }

  public function getMonthsWithStats(){
    $db = new database(TRUE);
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT * FROM tracked_months");
    try {
      $db->execute();
    } catch (Exception $e) {
      var_dump("Database error: ".$e->getMessage());
    }
    return $db->resultSet();
  }

  public function getMonthlyStats($year, $month){
    $db = new database(TRUE);
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT * FROM monthly_stats WHERE month = ? AND year = ?");
    $db->bind(1,$month);
    $db->bind(2,$year);
    try {
      $db->execute();
    } catch (Exception $e) {
      var_dump("Database error: ".$e->getMessage());
    }
    return $db->resultSet();
  }
}