<?php

class user {

  public function __construct(){
    $user = $this->whodis();
    if (!$user){
      $this->legit = FALSE;
      $this->label = FALSE;
      $this->valid = FALSE;
      return $user;
    }
    $user = $this->parseUser($user);
    foreach ($user as $key=>$value){
      $this->$key = $value;
    }
    $this->valid = TRUE;
  }

  public function whodis(){
    $db = new database();
    $db->query("SELECT ss13player.*, TIMESTAMPDIFF(HOUR, lastseen, NOW()) AS hoursAgo FROM ss13player WHERE ip = ? ORDER BY lastseen DESC LIMIT 1");
    $db->bind(1,$_SERVER['REMOTE_ADDR']);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }

  public function parseUser(&$user){
    $user->legit = FALSE;
    switch($user->lastadminrank) {
      default:
      case 'Player':
        $user->label = "";
        $user->legit = FALSE;
      break;

      case 'GameAdmin': 
      case 'Game Admin':
      case 'TrialAdmin':
        $user->color = '#9570c0';
        $user->label = "<span class='label' style='background: $user->color'>";
        $user->label.= "$user->ckey</span>";
        $user->legit = TRUE;
      break;

      case 'Barista':
        $user->color = '#6b4711';
        $user->label = "<span class='label' style='background: $user->color'>";
        $user->label.= "$user->ckey (WHERE'S MY FUCKIN COFFEE)</span>";
        $user->legit = TRUE;
      break;


      case 'Host': 
        $color = '#A00';
        $user->label = "<span class='label' style='background: #A00'>";
        $user->label.= "$user->ckey</span>";
      break;
    }
    if (24 <= $user->hoursAgo){
      $user->legit = FALSE;
      $user->label = FALSE;
    }
    return $user;
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