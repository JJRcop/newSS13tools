<?php class user{

  //BUT ALSO PLAYERS!!

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
    $user->backColor = "#EEE";
    $user->foreColor = "#444";
    $user->icon = '';
    $user->level = 0;

    switch ($user->rank) {
      case 'Coder':
        $user->backColor = '#009900';
        $user->foreColor = "#FFF";
        $user->icon = "<i class='fa fa-code'></i>";
        $user->level = 1;
      break;

      case 'Badmin':
        $user->backColor = "#000";
        $user->foreColor = "#FFF";
        $user->icon = "<i class='fa fa-asterisk'></i>";
        $user->level = 1;
      break;

      case 'AdminCandidate':
        $user->backColor = "#9570c0";
        $user->foreColor = "#FFF";
        $user->icon = "<i class='fa fa-university'></i>";
        $user->level = 1;
      break;

      case 'TrialAdmin':
        $user->backColor = "#9570c0";
        $user->foreColor = "#FFF";
        $user->icon = "<i class='fa fa-gavel'></i>";
        $user->level = 2;
      break;

      case 'GameAdmin': 
      case 'Game Admin':
      case 'CoderMin':
        $user->backColor = "#9570c0";
        $user->foreColor = "#FFF";
        $user->icon = "<i class='fa fa-dot-circle-o'></i>";
        $user->level = 2;
      break;

      case 'AdminTrainer':
        $user->backColor = "#9570c0";
        $user->foreColor = "#FFF";
        $user->icon = "<i class='fa fa-scales'></i>";
        $user->level = 2;
      break;

      case 'ClockcultEmpress':
        $user->backColor = "#e6a500";
        $user->foreColor = "#FFF";
        $user->icon = "<i class='fa fa-cog'></i>";
        $user->level = 2;
      break;

      case 'Barista':
        $user->backColor = '#6b4711';
        $user->foreColor = "#FFF";
        $user->icon = "<i class='fa fa-coffee'></i>";
        $user->level = 2;
      break;

      case 'GameMaster': 
        $user->backColor = '#A00';
        $user->foreColor = "#FFF";
        $user->icon = "<i class='fa fa-star-o'></i>";
        $user->level = 3;
      break;

      case 'HeadAdmin':
      case 'Headmin':
        $user->backColor = '#A00';
        $user->foreColor = "#FFF";
        $user->icon  = "<i class='fa fa-star'></i>";
        $user->level = 3;
      break;

      case 'Host': 
        $user->backColor = '#A00';
        $user->foreColor = "#FFF";
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
      if(empty($user->standing)) $user->standing = array('Not banned');
      $user->standing = implode(", ",$user->standing);
    }
    $user->href = APP_URL."tgdb/viewPlayer.php?ckey=$user->ckey";
    $user->link = "<a style='color: inherit;' href='$user->href'>$user->ckey</a>";

    $user->ipql = "<div class='ql'>(";
    $user->ipql.= "<a href='".APP_URL."tgdb/bans.php?ip=$user->ip'>";
    $user->ipql.= "<i class='fa fa-ban'></i></a>)";
    $user->ipql.= "(<a href='".APP_URL."tgdb/players.php?ip=$user->ip'>";
    $user->ipql.= "<i class='fa fa-user'></i></a>)";
    $user->ipql.= "(<a href='".APP_URL."tgdb/conn.php?ip=$user->ip'>";
    $user->ipql.= "<i class='fa fa-plug'></i></a>)";
    $user->ipql.= "</div>";

    $user->cidql = "<div class='ql'>(";
    $user->cidql.= "<a href='".APP_URL."tgdb/bans.php?cid=$user->computerid'>";
    $user->cidql.= "<i class='fa fa-ban'></i></a>)";
    $user->cidql.= "(<a href='".APP_URL."tgdb/players.php?cid=$user->computerid'>";
    $user->cidql.= "<i class='fa fa-user'></i></a>)";
    $user->cidql.= "(<a href='".APP_URL."tgdb/conn.php?cid=$user->computerid'>";
    $user->cidql.= "<i class='fa fa-plug'></i></a>)";
    $user->cidql.= "</div>";
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
    $ban = new ban();
    $player->bans = $ban->getPlayerBans($player->ckey);
    $message = new message();
    $player->messages = $message->getPlayerMessages($player->ckey);
    $player = $this->parseUser($player,TRUE);
    return $player;
  }

  public function getConnectionLog($filterby, $filter){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $where = "WHERE `datetime` > (SELECT ss13feedback.time
      FROM ss13feedback
      WHERE var_name = 'round_end'
      ORDER BY ss13feedback.time DESC
      LIMIT 0,1)";
    if ($filterby && $filter) {
      switch ($filterby){
        case 'IP':
        case 'ip':
          $where = "ip";
          $filter = ip2long($filter);
        break;

        case 'CID':
        case 'cid':
          $where = "computerid";
          $filter = $filter;
        break;
      }
      $where = "WHERE $where = ?";
    }
    $db->query("SELECT * FROM ss13connection_log
      $where;");
    if($where){
      $db->bind(1, $filter);
    }
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultSet();
  }

  public function getPlayerList($filterby=null,$filter=null){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $where = "WHERE lastseen > (SELECT ss13feedback.time
      FROM ss13feedback
      WHERE var_name = 'round_end'
      ORDER BY ss13feedback.time DESC
      LIMIT 0,1)";
    if ($filterby && $filter) {
      switch ($filterby){
        case 'IP':
        case 'ip':
          $where = "ip";
          $filter = ip2long($filter);
        break;

        case 'CID':
        case 'cid':
          $where = "computerid";
          $filter = $filter;
        break;
      }
      $where = "WHERE $where = ?";
    }
    $db->query("SELECT * FROM ss13player
      $where
      ORDER BY lastseen DESC;");
    if($where){
      $db->bind(1, $filter);
    }
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    foreach ($players = $db->resultSet() as &$player){
      $player = $this->parseUser($player);
    }
    return $players;
  }

  public function flagAuth(){
    if(DEFINED('LOG_AUTH')){
      $db = new database(TRUE);
      if($db->abort){
        return FALSE;
      }
      $db->query("INSERT INTO flagged_auth (ckey, remote_addr, db_addr, `timestamp`) VALUES (?, ?, ?, NOW())");
      $db->bind(1, $this->ckey);
      $db->bind(2, ip2long($_SERVER['REMOTE_ADDR']));
      $db->bind(3, ip2long($this->ip));
      try {
        $db->execute();
      } catch (Exception $e) {
        return returnError("Database error: ".$e->getMessage());
      }
      return true;
      }
    return false;
  }

  public function doAdminsPlay(){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT ss13player.*,
      count(DISTINCT ss13connection_log.id) AS connections
      FROM ss13connection_log
      LEFT JOIN ss13player ON ss13connection_log.ckey = ss13player.ckey
      WHERE ss13connection_log.datetime >= DATE(NOW()) - INTERVAL 30 DAY
      AND ss13player.lastadminrank != 'Player'
      GROUP BY ss13player.ckey
      ORDER BY connections DESC;");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    foreach ($result = $db->resultset() as &$r){
      $r = $this->parseUser($r);
    }
    return $result;
  }
  
}