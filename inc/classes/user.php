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
    if($db->abort){
      return FALSE;
    }
    if($db->abort){
      return FALSE;
    }
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
        $user->label = "<span class='label' style='background: $user->color'";
        $user->label.= "title='$user->lastadminrank'>";
        $user->label.= "<i class='fa fa-balance-scale'></i> $user->ckey </span>";
        $user->legit = TRUE;
      break;

      case 'Headmin': 
        $user->color = '#A00';
        $user->label = "<span class='label' style='background: $user->color'";
        $user->label.= "title='$user->lastadminrank'>";
        $user->label.= "<i class='fa fa-star'></i> $user->ckey </span>";
        $user->legit = TRUE;
      break;

      case 'Barista':
        $user->color = '#6b4711';
        $user->label = "<span class='label' style='background: $user->color'";
        $user->label.= "title='$user->lastadminrank'>";
        $user->label.= "<i class='fa fa-coffee'></i> $user->ckey </span>";
        $user->legit = TRUE;
      break;

      case 'coder':
      $user->color = '#009900';
      $user->label = "<span class='label' style='background: $user->color'";
        $user->label.= "title='$user->lastadminrank'>";
      $user->label.= "<i class='fa fa-code'></i> $user->ckey</span>";
      $user->legit = FALSE; //Change to TRUE to allow access to bans etc
      break;

      case 'Host': 
        $user->color = '#A00';
        $user->label = "<span class='label' style='background: $user->color'";
        $user->label.= "title='$user->lastadminrank'>";
        $user->label.= "<i class='fa fa-server'></i> $user->ckey</span>";
        $user->legit = TRUE;
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
    if($db->abort){
      return FALSE;
    }
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