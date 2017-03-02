<?php class user{

  public $ckey; //Ckey
  public $byond; //Their byond key, unsanitized

  public $legit = false; //Whether or not the user is recognized as a player
  public $rank = 'Player'; //Their rank (defualts to player, unprivileged)
  public $level = 0; //Their access level for this application

  public $foreColor = '#FFF'; //Foreground(text) color
  public $backColor = '#DDD'; //Background color
  public $label;

  public $firstSeenTimeStamp;
  public $lastSeenTimeStamp;

  public function __construct(){
    if(isset($_COOKIE['byond_ckey'])){
      $user = $this->getUser($_COOKIE['byond_ckey']);
      if(!$user) return false;
      $user = $this->parseUser($user);
      //Cookie dependent info
      $user->legit = false;
      if('OK' == $_COOKIE['status']) $user->legit = TRUE;
      $user->ckey = $_COOKIE['byond_ckey'];
      $user->byond = $_COOKIE['byond_key'];

      foreach ($user as $k => $v){
        $this->$k = $v;
      }
    }
    return false;
  }

  public function parseUser(&$user,$data=false){
    //Stuff we can set from the DB
    $user->rank = $user->lastadminrank;
    $user->firstSeenTimeStamp = timeStamp($user->firstseen);
    $user->lastSeenTimeStamp = timeStamp($user->lastseen);
    if(is_int($user->ip)){
      $user->ip = long2ip($user->ip);
    }

    //Ok, time to get their and set up display variables
    
    //Defaults
    $user->backColor = "#DDD";
    $user->foreColor = "#444";
    $user->icon = '';
    $user->level = 0;

    switch ($user->rank) {
      case 'Coder':
      $user->backColor = '#009900';
      $user->icon = "<i class='fa fa-code'></i>";
      $user->level = 1;
      break;

      case 'GameAdmin': 
      case 'Game Admin':
      case 'TrialAdmin':
      case 'CoderMin':
      case 'GameMaster':
      case 'ClockcultEmpress':
        $user->backColor = "#9570c0";
        $user->foreColor = "#FFF";
        $user->icon = "<i class='fa fa-eye'></i>";
        $user->level = 2;
      break;

      case 'Barista':
        $user->backColor = '#6b4711';
        $user->icon = "<i class='fa fa-coffee'></i>";
        $user->level = 2;
      break;

      case 'GameMaster': 
        $user->backColor = '#A00';
        $user->icon = "<i class='fa fa-star'></i>";
        $user->level = 3;
      break;

      case 'HeadAdmin':
      case 'Headmin':
        $user->foreColor = '#A00';
        $user->icon  = "<i class='fa fa-star'></i>";
        $user->level = 3;
      break;

      case 'Host': 
        $user->foreColor = '#A00';
        $user->icon = "<i class='fa fa-server'></i>";
        $user->level = 3;
      break;
    }

    $user->label = "<span class='label' title='$user->rank' ";
    $user->label.= "style='background-color: $user->backColor;";
    $user->label.= "color: $user->foreColor;'>$user->icon $user->ckey";
    $user->label.= "</span>";
    if ($data) { //For viewing player info pages
      $user->standing = array();
      if (!$user->bans){
        $user->standing[] = 'Not banned';
      } else {
        foreach ($user->bans as $b) {
          if ($b->status == 'Active'){
            if('JOB_PERMABAN' == $b->bantype){
              $user->standing[] = 'Permanently Job Banned';
            }
            if('JOB_TEMPBAN' == $b->bantype){
              $user->standing[] = 'Temporarily Job Banned';
            }
            if('TEMPBAN' == $b->bantype){
              $user->standing[] = 'Temporarily Banned';
            }
            if('PERMABAN' == $b->bantype){
              $user->standing[] = 'Permanently Banned';
            }
          }
        }
        $user->standing = array_unique($user->standing);
      }
      $user->standing = implode(", ",$user->standing);
    }
    return $user;
  }

  public function getUser($ckey){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT * FROM tbl_player WHERE ckey = ? LIMIT 1");
    $db->bind(1,$ckey);
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
    $player = $this->parseUser($player,TRUE);
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