<?php class user{

  //BUT ALSO PLAYERS!!

  public $ckey = null; //Ckey
  public $byond = null; //Their byond key, unsanitized

  public $legit = false; //Whether or not the user is recognized as a player
  public $rank = 'Player'; //Their rank (defualts to player, unprivileged)
  public $level = 0; //Their access level for this application

  public $foreColor = '#FFF'; //Foreground(text) color
  public $backColor = '#DDD'; //Background color
  public $label = null;

  public $firstSeenTimeStamp = null;
  public $lastSeenTimeStamp = null;

  public $txtVerify = FALSE;

  public $tgui = FALSE;

  public function __construct(){
    $app = new app();
    $user = false;
    if('remote' == $app->auth_method && isset($_COOKIE['byond_ckey'])){
      $user = $this->getUser($_COOKIE['byond_ckey']);
      if(!$user) return false;
      $user = $this->parseUser($user);
      //Cookie dependent info
      $user->legit = false;
      if('OK' == $_COOKIE['status']) $user->legit = TRUE;
      $user->ckey = $_COOKIE['byond_ckey'];
      $user->byond = $_COOKIE['byond_key'];
      $user->auth = 'Remote';
    } elseif ('text' == $app->auth_method){
      $user = $this->getUserByIP();
      $user = $this->parseUser($user);
      $user->auth = 'Text';
    } elseif (!$app->auth_method){
      $user = $this->getUserByIP();
      $user = $this->parseUser($user);
      if($user){
        $user->auth = 'IP';
      }
    } else {
      return false;
    }
    if(isset($_COOKIE['tgui'])){
      $this->tgui = $_COOKIE['tgui'];
    }
    if($user && $user->ckey){
      $user->legit = TRUE;
    }
    if($user){
      foreach ($user as $k => $v){
        $this->$k = $v;
      }
    }
  }

  public function parseUser(&$user,$data=false){
    if(!$user) return false;
    $app = new app();

    //Stuff we can set from the DB
    $user->rank = $user->lastadminrank;
    $user->firstSeenTimeStamp = timeStamp($user->firstseen);
    $user->lastSeenTimeStamp = timeStamp($user->lastseen);
    if(is_int($user->ip)){
      $user->ip = long2ip($user->ip);
    }

    if('remote'== $app->auth_method || 'text' == $app->auth_method){
      $user->rank = $this->verifyAdminRank($user->ckey);
      $user->permissions = $this->getAdminPermissions($user->rank);
      $user->txtVerify = TRUE;
    }

    //Ok, time to get their and set up display variables

    //Defaults
    $user->backColor = "#EEE";
    $user->foreColor = "#444";
    $user->icon = '';
    $user->level = 0;

    switch ($user->rank) {
      case 'Coder':
      case 'Codermin':
        $user->backColor = '#009900';
        $user->foreColor = "#FFF";
        $user->icon = "<i class='fa fa-code'></i>";
        $user->level = 1;
      break;

      case 'HeadCoder':
        $user->backColor = '#009900';
        $user->foreColor = "#FFF";
        $user->icon = "<i class='fa fa-code-fork'></i>";
        $user->level = 1;
      break;

      case 'Badmin':
        $user->backColor = "#000";
        $user->foreColor = "#FFF";
        $user->icon = "<i class='fa fa-snowflake-o'></i>";
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
      case 'GameAdmIn':
      case 'Game Admin':
      case 'CoderMin':
      case 'GameAdmln':
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
        $pjb = 0; $jtb = 0; $tb = 0; $pb = 0;
        foreach ($user->bans as $b) {
          if ($b->status == 'Active'){
            if('JOB_PERMABAN' == $b->bantype && $pjb == 0){
              $user->standing[] = "Permanently Job Banned ($b->permalink)";
              $pjb++;
            }
            if('JOB_TEMPBAN' == $b->bantype && $tjb == 0){
              $user->standing[] = "Temporarily Job Banned ($b->permalink)";
              $tjb++;
            }
            if('TEMPBAN' == $b->bantype && $tb == 0){
              $user->standing[] = "Temporarily Banned ($b->permalink)";
              $tb++;
            }
            if('PERMABAN' == $b->bantype && $pb == 0){
              $user->standing[] = "Permanently Banned ($b->permalink)";
              $pb++;
            }
          }
        }
      }
      if(empty($user->standing)) $user->standing[] = 'Not banned';
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
    $db->query("SELECT tbl_player.*,
      count(DISTINCT tbl_connection_log.id) AS connections
      FROM tbl_player
      LEFT JOIN tbl_connection_log ON tbl_connection_log.ckey = tbl_player.ckey
      WHERE tbl_player.ckey = ? LIMIT 1");
    $db->bind(1,$ckey);
    try {
      return $db->single();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

  public function getUserByIP(){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT tbl_player.*,
      count(DISTINCT tbl_connection_log.id) AS connections,
      IF(tbl_player.lastseen >= NOW() - INTERVAL 1 DAY, tbl_player.lastadminrank,'Player') as lastadminrank
      FROM tbl_player
      LEFT JOIN tbl_connection_log ON tbl_connection_log.ckey = tbl_player.ckey
      WHERE tbl_player.ip = ?
      -- AND tbl_player.lastseen >= NOW() - INTERVAL 1 DAY
      LIMIT 1");
    $db->bind(1,ip2long($_SERVER['REMOTE_ADDR']));
    try {
      return $db->single();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }

  }

  public function getPlayerByCkey($ckey){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT tbl_player.*,
      TIMESTAMPDIFF(HOUR, lastseen, NOW()) AS hoursAgo,
      count(DISTINCT tbl_connection_log.id) AS connections,
      count(DISTINCT tbl_connection_log.ip) AS IPs,
      count(DISTINCT tbl_connection_log.computerid) AS CIDs
      FROM tbl_player
      LEFT JOIN tbl_connection_log ON tbl_connection_log.ckey = tbl_player.ckey
      WHERE tbl_player.ckey = ?");
    $db->bind(1,strtolower(preg_replace('~[^a-zA-Z0-9]+~', '', $ckey)));
    try {
      $player = $db->single();
      $ban = new ban();
      $player->bans = $ban->getPlayerBans($player->ckey);
      $message = new message();
      $player->messages = $message->getPlayerMessages($player->ckey);
      $player = $this->parseUser($player,TRUE);
      return $player;
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

  public function getConnectionLog($filterby, $filter){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $where = "WHERE `datetime` > (SELECT tbl_feedback.time
      FROM tbl_feedback
      WHERE var_name = 'round_end'
      ORDER BY tbl_feedback.time DESC
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
    $db->query("SELECT * FROM tbl_connection_log
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
    $where = "WHERE lastseen > (SELECT tbl_feedback.time
      FROM tbl_feedback
      WHERE var_name = 'round_end'
      ORDER BY tbl_feedback.time DESC
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
    $db->query("SELECT * FROM tbl_player
      $where
      ORDER BY lastseen DESC;");
    if($where){
      $db->bind(1, $filter);
    }
    try {
      foreach ($players = $db->resultSet() as &$player){
        $player = $this->parseUser($player);
      }
      return $players;
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }

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
    $db->query("SELECT tbl_player.*,
      count(DISTINCT tbl_connection_log.id) AS connections
      FROM tbl_connection_log
      LEFT JOIN tbl_player ON tbl_connection_log.ckey = tbl_player.ckey
      WHERE tbl_connection_log.datetime >= DATE(NOW()) - INTERVAL 30 DAY
      GROUP BY tbl_player.ckey
      ORDER BY connections DESC;");
    try {
      $result = $db->resultset();
      $admins = array();
      foreach ($result as &$r){
        if('Player' == $r->lastadminrank) continue;
        $admins[] = $this->parseUser($r);
      }
      return $admins;
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

  public function verifyAdminRank($ckey){
    if(!file_exists(ROOTPATH.'/tmp/admins.json')){
      $app = new app();
      $app->downloadAdminsTxt();
    }
    $ranks = json_decode(file_get_contents(ROOTPATH.'/tmp/admins.json'));
    if(property_exists($ranks, $ckey)){
      return $ranks->{$ckey};
    } else {
      return false;
    }
  }

  public function getAdminPermissions($rank){
    if(!file_exists(ROOTPATH.'/tmp/adminranks.json')){
      $app = new app();
      $app->downloadAdminRanks();
    }
    $ranks = json_decode(file_get_contents(ROOTPATH.'/tmp/adminranks.json', 'w+'))->ranks;
    if(property_exists($ranks, $rank)){
      return $ranks->{$rank};
    } else {
      return false;
    }
  }

  public function getActiveHours($ckey=null) {
    $where = null;
    if ($ckey){
      $where = "WHERE ckey = ?";
    }
    $db = new database();
    $db->query("SELECT HOUR(`datetime`) AS `hour`,
      IF (count(DISTINCT id) IS NULL, 0, count(DISTINCT id)) AS connections
      FROM tbl_connection_log
      $where
      GROUP BY HOUR(`datetime`);");
    if($ckey) {
      $db->bind(1, $ckey);
    }
    try {
      return $db->resultset();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

  public function getActiveRoles($ckey){
    $db = new database();
    $db->query("SELECT job, minutes FROM tbl_role_time WHERE ckey = ?");
    $db->bind(1, $ckey);
    try {
      return $db->resultset();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

  public function getActiveAdminHours(){
    $db = new database();
    $db->query("SELECT count(DISTINCT tbl_connection_log.id) AS connections, HOUR(tbl_connection_log.datetime) AS `hour` FROM tbl_connection_log
        LEFT JOIN tbl_player ON tbl_connection_log.ckey = tbl_player.ckey
        WHERE tbl_player.lastadminrank != 'Player'
        GROUP BY HOUR(tbl_connection_log.datetime);");
    try {
      return $db->resultset();
      $result = array();
      foreach ($db->resultset() as $r){
        $result[$r->hour] = $r->connections;
      }
      return $result;
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

}
