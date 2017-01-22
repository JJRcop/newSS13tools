<?php

class user {

  public $ckey;
  public $rank;
  public $legit;
  public $label;

  public function __construct(){
    $auth = $this->auth();
    if ($auth) {
      $this->ckey = $auth->ckey;
      $this->rank = $auth->lastadminrank;
      $this->legit = 1;
      switch($this->rank) {
        default:
        case 'admin': 
          $color = '#9570c0';
        break;

        case 'badmin': 
          $color = '#A00';
        break;
      }
      $this->label = "Identified as <span class='label'";
      $this->label.= "style='background-color: ".$color."'>$this->ckey";
      $this->label.= " ($this->rank)</span>";
    } else {
      $this->legit = 0;
    }
  }

  public function auth() {
    $db = new database();
    $db->query("SELECT ckey, lastadminrank
      FROM tbl_player
      WHERE lastadminrank != 'Player'
      AND lastseen > DATE_SUB(CURDATE(), INTERVAL 1 DAY)
      AND ip = ?
      LIMIT 0,1");
    $db->bind(1,$_SERVER['REMOTE_ADDR']);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }

}