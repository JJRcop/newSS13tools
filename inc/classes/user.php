<?php

class user {

  public $legit;
  public $label;
  public $color;
  public $level = 0;

  public function __construct(){
    $user = $this->whodis();
    if (!$user){
      $this->legit = FALSE;
      $this->label = FALSE;
      $this->valid = FALSE;
      return $user;
    }
    $user->bans = FALSE;
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
    $user->color = "#FFF";
    $user->colorFore = "#000";
    $user->standing = 'Not banned';
    switch($user->lastadminrank) {
      default:
      case 'Player':
        $user->label = "$user->ckey";
        $user->legit = FALSE;
      break;

      case 'Coder':
      $user->color = '#009900';
      $user->label = "<span class='label' style='background: $user->color'";
        $user->label.= "title='$user->lastadminrank'>";
      $user->label.= "<i class='fa fa-code'></i> $user->ckey</span>";
      $user->legit = FALSE; //Change to TRUE to allow access to bans etc
      $user->level = 1;
      break;

      case 'GameAdmin': 
      case 'Game Admin':
      case 'TrialAdmin':
      case 'CoderMin':
      case 'GameMaster':
      case 'ClockcultEmpress':
        $user->color = '#9570c0';
        $user->colorFore = '#FFF';
        $user->label = "<span class='label' style='background: $user->color'";
        $user->label.= "title='$user->lastadminrank'>";
        $user->label.= "<i class='fa fa-eye'></i> $user->ckey </span>";
        $user->legit = TRUE;
        $user->level = 2;
      break;

      case 'Barista':
        $user->color = '#6b4711';
        $user->label = "<span class='label' style='background: $user->color'";
        $user->label.= "title='$user->lastadminrank'>";
        $user->label.= "<i class='fa fa-coffee'></i> $user->ckey </span>";
        $user->legit = TRUE;
        $user->level = 2;
      break;

      case 'GameMaster': 
        $user->color = '#A00';
        $user->label = "<span class='label' style='background: $user->color'";
        $user->label.= "title='$user->lastadminrank'>";
        $user->label.= "<i class='fa fa-star'></i> $user->ckey </span>";
        $user->legit = TRUE;
        $user->level = 3;
      break;

      case 'HeadAdmin':
      case 'Headmin':
        $user->color = '#A00';
        $user->label = "<span class='label' style='background: $user->color'";
        $user->label.= "title='$user->lastadminrank'>";
        $user->label.= "<i class='fa fa-star'></i> $user->ckey </span>";
        $user->legit = TRUE;
        $user->level = 3;
      break;

      case 'Host': 
        $user->color = '#A00';
        $user->label = "<span class='label' style='background: $user->color'";
        $user->label.= "title='$user->lastadminrank'>";
        $user->label.= "<i class='fa fa-server'></i> $user->ckey</span>";
        $user->legit = TRUE;
        $user->level = 3;
      break;
    }
    if (24 <= $user->hoursAgo){
      $user->legit = FALSE;
      $user->label = FALSE;
    }
    $user->firstSeenTimeStamp = timeStamp($user->firstseen);
    $user->lastSeenTimeStamp = timeStamp($user->lastseen);
    if (!$user->bans){
      $user->standing = 'Not banned';
    } else {
      foreach ($user->bans as $b) {
        if ($b->status == 'Active'){
          $user->standing = 'Banned';
        }
      }
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

  public function getPlayerByCkey($ckey){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT ss13player.*,
      TIMESTAMPDIFF(HOUR, lastseen, NOW()) AS hoursAgo,
      count(DISTINCT ss13connection_log.id) AS connections,
      count(DISTINCT ss13connection_log.ip) AS IPs,
      count(DISTINCT ss13connection_log.computerid) AS CIDs
      FROM ss13player
      LEFT JOIN ss13connection_log ON ss13connection_log.ckey = ss13player.ckey
      WHERE ss13player.ckey = ?");
    $db->bind(1,strtolower(preg_replace('~[^a-zA-Z0-9]+~', '', $ckey)));
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $player = $db->single();
    $player->bans = $this->getPlayerBans($player->ckey);
    $player = $this->parseUser($player);
    return $player;
  }

  public function getPlayerBans($ckey){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT *
      FROM ss13ban WHERE ckey=? ORDER BY bantime DESC");
    $db->bind(1,strtolower(preg_replace('~[^a-zA-Z0-9]+~', '', $ckey)));
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $ban = new ban();
    if(!$bans = $db->resultSet()) return false;
    foreach ($bans as &$b){
      $b = $ban->parseBan($b);
    }
    return $bans;
  }
}