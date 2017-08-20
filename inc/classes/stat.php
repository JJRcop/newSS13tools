<?php

class stat {

  public function getAggregatedFeedback($stat){
    //This is a very expensive method to call. Please try to avoid it.
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SET SESSION group_concat_max_len = 1000000;"); //HONK
    $db->execute();
    $db->query("SELECT tbl_feedback.var_name,
      count(distinct tbl_feedback.round_id) as rounds,
      SUM(tbl_feedback.var_value) AS `var_value`,
      IF (tbl_feedback.details = '', NULL, GROUP_CONCAT(tbl_feedback.details SEPARATOR '#-#')) AS details
      FROM tbl_feedback
      WHERE var_name = ?
      ORDER BY tbl_feedback.time desc");
    $db->bind(1,$stat);
    try {
      $f = $db->single();
      return $this->parseFeedback($f,TRUE);
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

  public function aggreateStatForMonth($stat, $month, $year) {
    if ($month && $year) {
      $date = new DateTime("Midnight $month/01/$year");
    } else {
      $date = new DateTime("Midnight ".date('m')."/01/".date('Y'));
    }
    $start = $date->format("Y-m-01 H:i:s");
    $end = $date->format('Y-m-t 23:59:59');
    //This is a very expensive method to call. Please try to avoid it.
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SET SESSION group_concat_max_len = 1000000;"); //HONK
    $db->execute();
    $db->query("SELECT var_name,
      count(distinct tbl_feedback.round_id) as rounds,
      SUM(tbl_feedback.var_value) AS `var_value`,
      IF (tbl_feedback.details = '', NULL, GROUP_CONCAT(tbl_feedback.details SEPARATOR '#-#')) AS details
      FROM tbl_feedback WHERE var_name = ?");
    $db->bind(1,$stat);
    $db->bind(2,$start);
    $db->bind(3,$end);
    try {
      $f = $db->single();
      return $this->parseFeedback($f,TRUE);
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

  public function getMonthsWithStats(){
    $db = new database(TRUE);
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT * FROM tracked_months ORDER BY year DESC, month DESC");
    try {
      return $db->resultSet();
    } catch (Exception $e) {
      var_dump("Database error: ".$e->getMessage());
    }

  }

  public function getMonthlyStat($year, $month, $stat=false){
    $db = new database(TRUE);
    if($db->abort){
      return FALSE;
    }
    $findStat = '';
    $select = 'var_name';

    if($stat){
      $findStat = "AND var_name = ?";
      $select = '*';
    }
    $db->query("SELECT $select
      FROM monthly_stats
      WHERE month = ? AND year = ?
      $findStat;");
    $db->bind(1,$month);
    $db->bind(2,$year);
    if($stat) $db->bind(3, $stat);
    try {
      if($stat){
        $result = $db->single();
        $result->details = json_decode($result->details,TRUE);
        return $this->parseFeedback($result,FALSE,TRUE);
      } else {
        return $db->resultset();
      }
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    
  }

  public function getRoundsForMonth($start, $end){
    $db = new database();
    $db->query("SELECT count(id) AS rounds,
      MIN(id) AS firstround,
      MAX(id) AS lastround
      FROM tbl_round
      WHERE tbl_round.end_datetime BETWEEN ? AND ?
      AND tbl_round.start_datetime IS NOT NULL
      AND tbl_round.end_datetime IS NOT NULL");
    $db->bind(1,$start);
    $db->bind(2,$end);
    try {
      return $db->single();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }

  }

  public function regenerateMonthlyStats($month, $year){
    $db = new database(TRUE);
    if($db->abort){
      return FALSE;
    }
    $db->query("DELETE FROM monthly_stats WHERE month = ? AND year = ?");
    $db->bind(1,$month);
    $db->bind(2,$year);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }

    $db->query("DELETE FROM tracked_months WHERE month = ? AND year = ?");
    $db->bind(1,$month);
    $db->bind(2,$year);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $return = "Cleared stats for $month, $year ";
    $return.= $this->generateMonthlyStats($month,$year);
    return $return;
  }

  public function generateMonthlyStats($month=null, $year=null){
    if (!$month && !$year){
      $date = new DateTime("Previous month");
    } else {
      $date = new DateTime("$year-$month-01 00:00:00");
    }
    $start = $date->format("Y-m-01 00:00:00");
    $end = $date->format("Y-m-t 23:59:59");
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SET SESSION group_concat_max_len = 1000000;"); //HONK
    $db->execute();
    $db->query("SELECT tbl_feedback.var_name,
      count(distinct tbl_feedback.round_id) as rounds,
      SUM(tbl_feedback.var_value) AS `var_value`,
      IF (tbl_feedback.details = '', NULL, GROUP_CONCAT(tbl_feedback.details SEPARATOR '#-#')) AS details
      FROM tbl_feedback
      WHERE DATE(tbl_feedback.time) BETWEEN ? AND ?
      AND tbl_feedback.var_name != ''
      GROUP BY tbl_feedback.var_name
      ORDER BY tbl_feedback.var_name ASC;");
    $db->bind(1,$start);
    $db->bind(2,$end);
    try {
      $results = $db->resultSet();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    
    $db = new database(TRUE);
    if($db->abort){
      return FALSE;
    }
    $db->query("INSERT INTO monthly_stats
      (rounds, var_name, details, var_value, month, year, `timestamp`)
      VALUES(?, ?, ?, ?, ?, ?, NOW())");
    $i = 0;
    foreach ($results as &$r){
      $r = $this->parseFeedback($r,TRUE);
      $db->bind(1,$r->rounds);
      $db->bind(2,$r->var_name);
      $db->bind(3,json_encode($r->details));
      $db->bind(4,$r->var_value);
      $db->bind(5,$date->format("m"));
      $db->bind(6,$date->format("Y"));
      try {
        $db->execute();
      } catch (Exception $e) {
        var_dump("Database error: ".$e->getMessage());
      }
      $i++;
    }
    $roundCount = $this->getRoundsForMonth($start, $end);
    $db->query("INSERT INTO tracked_months
      (year, month, stats, rounds, firstround, lastround, `timestamp`)
      VALUES(?, ?, ?, ?, ?, ?, NOW())");
    $db->bind(1,$date->format("Y"));
    $db->bind(2,$date->format("m"));
    $db->bind(3,$i);
    $db->bind(4,$roundCount->rounds);
    $db->bind(5,$roundCount->firstround);
    $db->bind(6,$roundCount->lastround);
    try {
      $db->execute();
    } catch (Exception $e) {
      return "Database error: ".$e->getMessage();
    }
    return "Added $i stats for ".$date->format("F, Y");
  }

  public function parseFeedback(&$stat, $aggregate = FALSE, $skip = FALSE){

    $stat->key = 'Key';
    $stat->value = 'Value';
    $stat->total = 'Total';

    $stat->splain = null;
    $stat->details = trim(rtrim($stat->details));
    $stat->details = trim($stat->details,'"');
    $stat->details = rtrim($stat->details,'"');

    if($aggregate){
      $stat->details = str_replace('#-#', '" | "', $stat->details);
    }
    $stat->details = explode('" | "', $stat->details);

    if(is_array($stat->details)){
      $stat->details = array_count_values($stat->details);
      if(count($stat->details) == 1) {
        $key = array_keys($stat->details)[0];
        $stat->details = $key;
      }
    }
    if('' === $stat->details) $stat->details = null;

    $stat->include = 'singleString';

    if (!is_array($stat->details) && !$stat->var_value){
      $stat->include = 'bigText';
    } else if (!$stat->details) {
      $stat->include = 'bigNum';
    } else {
      $stat->include = 'singleString';
      $stat->var_value = array_sum($stat->details);
    }

    switch($stat->var_name){
      //Special cases below this point
      case 'test_testmerged_prs':
      case 'testmerged_prs':
        $keys = array_keys($stat->details);
        unset($stat->details);
        foreach($keys as &$s){
          $s = explode('|',$s);
          $stat->details[$s[0]] = $s[1];
        }
        $stat->include = 'PRs';
      break;

      case 'test_radio_usage':
      case 'radio_usage':
        $tmp = array_keys($stat->details);
        unset($stat->details);
        foreach ($tmp as $s){
          $s = explode('-',$s);
          $stat->details[$s[0]] = (int) $s[1];
        }
        arsort($stat->details);
        $stat->var_value = array_sum($stat->details);
        $stat->include = 'singleString';
      break;

      case 'test_food_harvested':
      case 'test_chemical_reaction':
      case 'food_harvested':
      case 'chemical_reaction':
      case 'export_sold_amount':
      case 'export_sold_cost':
        $tmp = array_keys($stat->details);
        unset($stat->details);
        foreach ($tmp as $s){
          $s = explode('|',$s);
          if(isset($stat->details[$s[0]])){
            $stat->details[$s[0]] += $s[1];
          } else {
            $stat->details[$s[0]] = $s[1];
          }
        }
        arsort($stat->details);
        $stat->include = 'singleString';
        if('chemical_reaction' === $stat->var_name){
          $stat->key = 'Chemical';
          $stat->value = 'Number of units created';
          $stat->total = 'Total units of chemicals created';
        }
        
      break;

      case 'test_item_used_for_combat':
      case 'item_used_for_combat':
        $stat->include = 'combat';
        $stat->splain = '/obj/item was removed from item names for'; 
        $stat->splain.= ' readability purposes.';
      break;

      case 'test_admin_verb':
      case 'admin_verb':
        $stat->key = 'Admin Verb';
        $stat->value = 'Times Used';
        $stat->total = 'Total verbs used';
      break;

      case 'circuit_printed':
        $stat->key = 'Circuit board';
        $stat->value = 'Printed';
        $stat->total = 'Total circuit boards printed';
      break;

      case 'event_ran':
        $stat->key = 'Event';
        $stat->value = 'Number of times run';
        $stat->total = 'Total random events run';
      break;

    }
    return $stat;
  }

  public function parseFeedbackDEPRECATED(&$stat,$aggregate=FALSE,$skip=FALSE){
    //AT THE MINIMUM, `stat` needs to be an object with properties:
    //`var_name`
    //`var_value`
    //`details`
    
    $stat->splain = null;

    switch ($stat->var_name){

      //Single string stats
      //These can just be reported. When aggregated, they need to be
      //concatenated carefully
      case 'chaplain_weapon': //Spaces
      case 'emergency_shuttle':
      case 'end_error': //Spaces
      case 'end_proper': //Spaces
      case 'game_mode': //Spaces
      case 'map_name':
      case 'megafauna_kills': //Spaces
      case 'religion_book': //Spaces
      case 'religion_deity': //Spaces
      case 'religion_name': //Spaces
      case 'revision':
      case 'round_end_result':
      case 'shuttle_fasttravel':
      case 'shuttle_manipulator':
      case 'shuttle_purchase':
      case 'station_renames':
      case 'shuttle_reason':
        if($aggregate){
          $stat->details = explode('#-#',$stat->details);
          $stat->details = array_count_values($stat->details);
          arsort($stat->details);
        }
        if(is_array($stat->details)) $stat->var_value = array_sum($stat->details);
        $stat->include = 'singleString';
      break;

      //Long string stats
      //Need to be exploded and grouped. This does NOT handle | value appended
      //stats
      case 'admin_secrets_fun_used':
      case 'admin_verb':
      case 'assembly_made':
      case 'cell_used':
      case 'changeling_powers':
      case 'changeling_power_purchase':
      case 'changeling_success':
      case 'circuit_printed':
      case 'clockcult_scripture_recited':
      case 'colonies_dropped':
      case 'cult_runes_scribed':
      case 'engine_started':
      case 'event_ran':
      case 'event_admin_cancelled':
      case 'food_made':
      case 'gun_fired':
      case 'handcuffs':
      case 'hivelord_core':
      case 'immortality_talisman':
      case 'item_deconstructed':
      case 'item_used_for_combat':
      case 'jaunter':
      case 'lazarus_injector':
      case 'mining_equipment_bought':
      case 'mining_voucher_redeemed':
      case 'mobs_killed_mining':
      case 'object_crafted':
      case 'pick_used_mining':
      case 'shuttle_gib':
      case 'shuttle_gib_unintelligent':
      case 'slime_babies_born':
      case 'slime_cores_used':
      case 'slime_core_harvested':
      case 'surgeries_completed':
      case 'traitor_success':
      case 'traitor_uplink_items_bought':
      case 'traitor_random_uplink_items_gott':
      case 'vending_machine_usage':
      case 'warp_cube':
      case 'wisp_lantern':
      case 'wizard_spell_learned':
      case 'wizard_success':
      case 'zone_targeted':
        if(!$skip){
          if($aggregate){
            $stat->details = str_replace('#-#', ' ', $stat->details);
          }
          $stat->details = str_replace(',', '', $stat->details);
          $stat->details = array_count_values(explode(' ',$stat->details));
        }
        $stat->var_value = array_sum($stat->details);
        arsort($stat->details);
        $stat->include = 'singleString';

        switch($stat->var_name){
          case 'item_used_for_combat':
            $stat->splain = "Showing item name and damage after the | character.";
          break;
          case 'traitor_uplink_items_bought':
            $stat->splain = "Item cost after the | character";
          break;
          case 'vending_machine_usage':
            $stat->splain = "Machine used | item vended";
        }

      break;

      //Long string stats with values
      //Strings that have a value piped (|) onto the end of the string.
      //We need to group and add these values

      case 'admin_toggle':
      case 'cargo_imports':
      case 'chemical_reaction':
      case 'export_sold_amount':
      case 'export_sold_cost':
      case 'food_harvested':
      case 'item_printed':
      case 'ore_mined':
      case 'preferences_verb':
      case 'wizard_spell_improved':
        if(!$skip){
          if($aggregate){
            $stat->details = str_replace('#-#', ' ', $stat->details);
          }
          $stat->details = array_count_values(explode(' ',$stat->details));
          $tmp = array();
          foreach ($stat->details as $k => $v){
            $a = explode('|',$k);
            $k = $a[0];
            @$a = $a[1];
            if(isset($tmp[$k])) {
              @$tmp[$k] += $v * $a;
            } else {
              @$tmp[$k] = $v * $a;
            }
          }
          $stat->details = $tmp;
        }
        $stat->var_value = array_sum($stat->details);
        arsort($stat->details);
        $stat->include = 'singleString';
      break;

      //Edge cases
      //Stats that have to be specially handled

      //Job bans
      case 'ban_job':
      case 'ban_job_tmp':
      case 'ban_job_unban':
        if($aggregate){
          $stat->details = str_replace('#-#', ' ', $stat->details);
        }
        $stat->details = trim(rtrim($stat->details));
        $stat->details = str_replace(' ', '', $stat->details);
        $stat->details = str_replace('-_', '##', $stat->details);
        $stat->details = str_replace('_', ' ', $stat->details);
        $stat->details = explode('##', $stat->details);
        array_filter($stat->details);
        $stat->details = array_count_values($stat->details);
        $stat->var_value = array_sum($stat->details);
        $stat->include = 'singleString';
      break;

      //Job preferences
      case 'job_preferences':
        if($aggregate){
          $stat->details = str_replace('#-#', '', $stat->details);
          // $stat->details = str_replace('', '', $stat->details);
        }
        if (!$skip){
          $stat->details = explode('|- ',$stat->details);
          $stat->details = array_filter($stat->details);
          $tmp = array();
          foreach ($stat->details as $s){
            $s = str_replace(' |', '', $s);
            $s = explode('|',$s);
            $s = array_filter($s);
            $job = str_replace('_',' ', $s[1]);
            unset($s[1]);
            foreach ($s as &$j){
              $j = explode('=',$j);
            }
            @$tmp[$job]['HIGH']   += (int) $s[2][1];
            @$tmp[$job]['MEDIUM'] += (int) $s[3][1];
            @$tmp[$job]['LOW']    += (int) $s[4][1];
            @$tmp[$job]['NEVER']  += (int) $s[5][1];
            @$tmp[$job]['BANNED'] += (int) $s[6][1];
            @$tmp[$job]['YOUNG']  += (int) $s[7][1];
          }
          $stat->details = $tmp;
        }
        $stat->include = 'jobPrefs';
      break;

      //Radio usage
      case 'radio_usage':
        if($aggregate){
          $stat->details = str_replace('#-#', ' ', $stat->details);
        }
        if (!$skip){
          $stat->details = explode(' ',$stat->details);
          $tmp = array();
          foreach ($stat->details as $s){
            $s = explode('-',$s);
            if($aggregate){
              @$tmp[$s[0]]+= (int) $s[1];
            } else {
              @$tmp[$s[0]] = (int) $s[1];
            }
          }
          $stat->details = $tmp;
        }
        arsort($stat->details);
        $stat->var_value = array_sum($stat->details);
        $stat->include = 'singleString';
      break;

      case 'changeling_objective':
      case 'traitor_objective':
      case 'wizard_objective':
      case 'cult_objective':
        if($aggregate){
          $stat->details = str_replace('#-#', ' ', $stat->details);
        }
        if(!is_array($stat->details)){
          $stat->details = array_count_values(explode(' ',$stat->details));
          $objs = array();
          foreach ($stat->details as $obj => $count){
            $obj = explode('|',$obj);
            $objective = str_replace('/datum/objective/', '',$obj[0]);
            $status = str_replace(',', '', $obj[1]);
            if (array_key_exists($objective, $objs)){
              @$objs[$objective][$status]+= $count;
            } else {
              @$objs[$objective][$status]+= $count;
            }
          }
          $stat->details = $objs;
        }
        $stat->splain = "/datum/objective are custom objectives";
        $stat->include = 'objs';
      break;

      case 'round_end':
      case 'round_start':
        if($aggregate){
          $stat->details = explode('#-#',$stat->details);
          $stat->details = array_count_values($stat->details);
        }
        if(!$skip){
          $stat->details = date('H',strtotime($stat->details));
        }
        $stat->include = 'bigNum';
      break;

      case 'server_ip':
        if($aggregate){
          $stat->details = explode('#-#',$stat->details);
          $stat->details = array_count_values($stat->details);
          arsort($stat->details);
        }
        if(is_array($stat->details)) $stat->var_value = array_sum($stat->details);
        $stat->include = 'singleString';
      break;

      case 'testmerged_prs':
        if($aggregate){
          $stat->details = str_replace('#-#',' ',$stat->details);
        }
        if(!$skip){
          $stat->details = explode(' ',$stat->details);
          $stat->details = array_count_values($stat->details);
        }
        if(is_array($stat->details)){
          $stat->var_value = array_sum($stat->details);
          arsort($stat->details);
        }
        $stat->include = 'PRs';
      break;

      case 'commendation':
        if($aggregate){
          $stat->details = str_replace('#-#',',',$stat->details);
        }
        if(!$skip){
          $stat->details = str_replace("_", " ", $stat->details);
          $stat->details = str_replace("} {", "},{", $stat->details);
          $stat->details = json_decode("[$stat->details]");
        }
        foreach ($stat->details as &$d){
          $d = (object) $d;
          switch ($d->medal){
            default: 
              $d->graphic = 'bronze';
            break;

            case 'The robust security award': 
              $d->graphic = 'silver';
            break;

            case 'The medal of valor': 
              $d->graphic = 'gold';
            break;

            case 'The nobel sciences award': 
              $d->graphic = 'plasma';
            break;
          }
          $d->id = substr(sha1(json_encode($d)), 0, 7);
        }
        $stat->include = "commendation";
        // var_dump($stat);
      break;

      //Value stats
      //Where the value just needs to be displayed
      //Or summed when aggregating
      case 'admin_cookies_spawned':
      case 'ahelp_close':
      case 'ahelp_icissue':
      case 'ahelp_reject':
      case 'ahelp_reopen':
      case 'ahelp_resolve':
      case 'ahelp_unresolved':
      case 'alert_comms_blue':
      case 'alert_comms_green':
      case 'alert_keycard_auth_maint':
      case 'alert_keycard_auth_red':
      case 'arcade_loss_hp_emagged':
      case 'arcade_loss_hp_normal':
      case 'arcade_loss_mana_emagged':
      case 'arcade_loss_mana_normal':
      case 'arcade_win_emagged':
      case 'arcade_win_normal':
      case 'ban_appearance':
      case 'ban_appearance_unban':
      case 'ban_edit':
      case 'ban_perma':
      case 'ban_tmp':
      case 'ban_tmp_mins':
      case 'ban_unban':
      case 'ban_warn':
      case 'benchmark':
      case 'comment':
      case 'cyborg_ais_created':
      case 'cyborg_birth':
      case 'cyborg_engineering':
      case 'cyborg_frames_built':
      case 'cyborg_janitor':
      case 'cyborg_medical':
      case 'cyborg_miner':
      case 'cyborg_mmis_filled':
      case 'cyborg_peacekeeper':
      case 'cyborg_security':
      case 'cyborg_service':
      case 'cyborg_standard':
      case 'disposal_auto_flush':
      case 'Engi_equipment_bought':
      case 'escaped_human':
      case 'escaped_on_pod_1':
      case 'escaped_on_pod_2':
      case 'escaped_on_pod_3':
      case 'escaped_on_pod_5':
      case 'escaped_on_shuttle':
      case 'escaped_total':
      case 'god_objective':
      case 'god_success':
      case 'high_research_level':
      case 'mecha_durand_created':
      case 'mecha_firefighter_created':
      case 'mecha_gygax_created':
      case 'mecha_honker_created':
      case 'mecha_odysseus_created':
      case 'mecha_phazon_created':
      case 'mecha_ripley_created':
      case 'mining_adamantine_produced':
      case 'mining_clown_produced':
      case 'mining_diamond_produced':
      case 'mining_glass_produced':
      case 'mining_gold_produced':
      case 'mining_iron_produced':
      case 'mining_plasma_produced':
      case 'mining_rglass_produced':
      case 'mining_silver_produced':
      case 'mining_steel_produced':
      case 'mining_uranium_produced':
      case 'newscaster_channels':
      case 'newscaster_newspapers_printed':
      case 'newscaster_stories':
      case 'nuclear_challenge_mode':
      case 'round_end_clients':
      case 'round_end_ghosts':
      case 'supply_mech_collection_redeemed':
      case 'surgery_initiated':
      case 'surgery_step_failed':
      case 'surgery_step_success':
      case 'survived_human':
      case 'survived_total':
        $stat->var_value = (int) $stat->var_value;
        $stat->include = 'bigNum';
      break;

      default:
        echo alert("<strong>ERROR 501:</strong> $stat->var_name is untracked. Tell Ned. TELL NED!!",'danger');
      break;
    }
    if('' === $stat->details) $stat->details = null;
    if (!is_array($stat->details) && !$stat->var_value){
      $stat->include = 'bigText';
    }
    return $stat;
  }

  public function getRoundStatsForMonth($year=null, $month=null){
    if (!$month && !$year){
      $date = new DateTime("Previous month");
    } else {
      $date = new DateTime("$year-$month-01 00:00:00");
    }
    $start = $date->format("Y-m-01 00:00:00");
    $end = $date->format("Y-m-t 23:59:59");
    $db = new database();
    $db->query("SELECT count(id) AS rounds,
      min(tbl_round.start_datetime) AS earliest_start,
      max(tbl_round.end_datetime) AS latest_end,
      min(tbl_round.id) AS `first`,
      max(tbl_round.id) AS `last`,
      floor(AVG(TIME_TO_SEC(TIMEDIFF(tbl_round.end_datetime,tbl_round.start_datetime)))) / 60 AS avgduration,
      tbl_round.game_mode,
      tbl_round.game_mode_result AS result
      FROM tbl_round
      WHERE tbl_round.end_datetime BETWEEN ? AND ?
      AND tbl_round.game_mode IS NOT NULL
      GROUP BY tbl_round.game_mode, tbl_round.game_mode_result;");
    $db->bind(1,$start);
    $db->bind(2,$end);
    try {
      return $db->resultset();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }

  }
}
