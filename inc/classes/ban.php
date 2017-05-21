<?php 

class ban {

  public $id = FALSE;
  public $bantime = FALSE;
  public $serverip = FALSE;
  public $bantype = FALSE;
  public $reason = FALSE;
  public $job = FALSE;
  public $duration = FALSE;
  public $rounds = FALSE;
  public $expiration_time = FALSE;
  public $ckey = FALSE;
  public $computerid = FALSE;
  public $ip = FALSE;
  public $a_ckey = FALSE;
  public $a_computerid = FALSE;
  public $a_ip = FALSE;
  public $who = FALSE;
  public $adminwho = FALSE;
  public $edits = FALSE;
  public $unbanned = FALSE;
  public $unbanned_datetime = FALSE;
  public $unbanned_ckey = FALSE;
  public $unbanned_computerid = FALSE;
  public $unbanned_ip = FALSE;
  public $next = FALSE;
  public $prev = FALSE;
  public $status = FALSE;
  public $statusClass = FALSE;
  public $bantimestamp = FALSE;
  public $expirationtimestamp = FALSE;
  public $type = FALSE;
  public $class = FALSE;
  public $icon = FALSE;
  public $scope = FALSE;
  public $minutes = FALSE;

  public function __construct($id=null) {
    if ($id){
      $ban = $this->getBan($id);
      $ban = $this->parseBan($ban);
      foreach ($ban as $k => $v){
        $this->$k = $v;
      }
      return $ban;
    }
  }

  public function getBan($id) {
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT tbl_ban.*,
      MAX(next.id) AS `next`,
      MIN(prev.id) AS `prev`,
      TIMESTAMPDIFF(MINUTE, ss13ban.bantime, ss13ban.expiration_time) AS minutes
      FROM tbl_ban
      LEFT JOIN tbl_ban AS `next` ON next.id = tbl_ban.id + 1
      LEFT JOIN tbl_ban AS `prev` ON prev.id = tbl_ban.id - 1
      WHERE tbl_ban.id = ?");
    $db->bind(1, $id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }

  public function getBanList($page = 0, $count=100,$filterby=null,$filter=null) {
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $where = '';
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
    $db->query("SELECT *,
    TIMESTAMPDIFF(MINUTE, ss13ban.bantime, ss13ban.expiration_time) AS minutes
    FROM tbl_ban
    $where
    ORDER BY id DESC LIMIT ?, ?");
    if($where){
      $db->bind(2, $page);
      $db->bind(3, $count);
      $db->bind(1,$filter);
    } else {
      $db->bind(1, $page);
      $db->bind(2, $count);
    }
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $bans = $db->resultSet();
    foreach ($bans as &$ban){
      $ban = $this->parseBan($ban);
    }
    return $bans;
  }

  public function parseBan(&$ban) {
    $ban->rules = array();
    $ban= $this->parseBanReason($ban);
    $round = new round();
    $ban->serverip = $round->mapServer(long2ip($ban->server_ip).":".$ban->server_port);

    //The ban status should be done on MySQL, this is a crutch and I don't like
    //it :(

    $ban->status = 'Active';
    $ban->statusClass = 'danger';

    $ban->bantimestamp = timeStamp($ban->bantime);
    $ban->expirationtimestamp = timeStamp($ban->expiration_time);

    $ban->duration = $ban->minutes;

    if (is_int($ban->ip)){
      $ban->ip = long2ip($ban->ip);
    }

    switch ($ban->bantype){
      case 'TEMPBAN':
      default:
        $ban->type = 'Server (Temporary)';
        $ban->class = 'danger';
        $ban->icon = 'clock-o';
        $ban->duration = "$ban->duration minutes";
        $ban->scope = 'Server';
        if (strtotime($ban->expiration_time) < time()){
          // $ban->expirationtimestamp = timeStamp($ban->expiration_time,'Expired');
          $ban->status = 'Expired';
          $ban->statusClass = 'success';
        }
      break;

      case 'JOB_TEMPBAN':
        $ban->type = 'Job (Temporary)';
        $ban->class = 'warning';
        $ban->icon = 'briefcase';
        $ban->duration = "$ban->duration minutes";
        $ban->scope = $ban->job;
        if (strtotime($ban->expiration_time) < time()){
          // $ban->expirationtimestamp = timeStamp($ban->expiration_time,'Expired');
          $ban->status = 'Expired';
          $ban->statusClass = 'success';
        }
      break;

      case 'JOB_PERMABAN':
        $ban->type = 'Job (Permanent)';
        $ban->class = 'perma';
        $ban->icon = 'briefcase';
        $ban->duration = 'Permanent';
        $ban->scope = $ban->job;
        $ban->expirationtimestamp = 'Never';
      break;

      case 'PERMA':
      case 'PERMABAN':
        $ban->type = 'Server (Permanent)';
        $ban->class = 'perma';
        $ban->icon = 'ban text-danger';
        $ban->duration = 'Permanent';
        $ban->scope = 'Server';
        $ban->expirationtimestamp = 'Never';
      break;
    }

    if ($ban->unbanned){
      $ban->status = "Unbanned by $ban->unbanned_ckey ".timeStamp($ban->unbanned_datetime);
      $ban->statusClass = 'success';
    }

    $badmins = array();
    foreach (explode(', ', $ban->adminwho) as $admin){
      $badmin['ckey'] = $admin;
      $badmin['ref'] = strtolower(preg_replace('~[^a-zA-Z0-9]+~', '', $admin));
      $badmins[] = $badmin;
    }
    asort($badmins);
    $ban->adminwho = $badmins;

    $players = array();
    foreach (explode(', ', $ban->who) as $ckey){
      $player['ckey'] = $ckey;
      $player['ref'] = strtolower(preg_replace('~[^a-zA-Z0-9]+~', '', $ckey));
      $players[] = $player;
    }
    asort($players);
    $ban->who = $players;

    if ($ban->edits){
      $ban->edits = str_replace("<cite>", "<p><cite>", $ban->edits);
      $ban->edits = str_replace("</cite>", "</cite></p>", $ban->edits);
    }

    $ban->ipql = "<div class='ql'>(";
    $ban->ipql.= "<a href='".APP_URL."tgdb/bans.php?ip=$ban->ip'>";
    $ban->ipql.= "<i class='fa fa-ban'></i></a>)";
    $ban->ipql.= "(<a href='".APP_URL."tgdb/players.php?ip=$ban->ip'>";
    $ban->ipql.= "<i class='fa fa-user'></i></a>)";
    $ban->ipql.= "(<a href='".APP_URL."tgdb/conn.php?ip=$ban->ip'>";
    $ban->ipql.= "<i class='fa fa-plug'></i></a>)";
    $ban->ipql.= "</div>";

    $ban->cidql = "<div class='ql'>(";
    $ban->cidql.= "<a href='".APP_URL."tgdb/bans.php?cid=$ban->computerid'>";
    $ban->cidql.= "<i class='fa fa-ban'></i></a>)";
    $ban->cidql.= "(<a href='".APP_URL."tgdb/players.php?cid=$ban->computerid'>";
    $ban->cidql.= "<i class='fa fa-user'></i></a>)";
    $ban->cidql.= "(<a href='".APP_URL."tgdb/conn.php?cid=$ban->computerid'>";
    $ban->cidql.= "<i class='fa fa-plug'></i></a>)";
    $ban->cidql.= "</div>";

    return $ban;
  }

  public function parseBanReason(&$ban){
    $ban->reason = auto_link_text($ban->reason);

    //Probable rule 1
    if(str_contains($ban->reason, "a non-antag")
      || str_contains($ban->reason, "nonantag")
      || str_contains($ban->reason, "Rule 1")){
      $rules['number'] = 1;
      $rules['text'] = "Don't be a dick";
      $ban->rules[] = $rules;
    }

    //Probable rule 2
    if(str_contains($ban->reason, "meta")
      || str_contains($ban->reason, "Rule 2")){
      $rules['number'] = 2;
      $rules['text'] = "Do not use information gained outside of in character means.";
      $ban->rules[] = $rules;
    }

    //Probable rule 3
    if(str_contains($ban->reason, "IC in")
      || str_contains($ban->reason, "OOC in")
      || str_contains($ban->reason, "in OOC")
      || str_contains($ban->reason, "Rule 3"){
      $rules['number'] = 3;
      $rules['text'] = "IC in OOC";
      $ban->rules[] = $rules;
    }

    //Probable rule 7
    if(str_contains($ban->reason, "bait")
      || str_contains($ban->reason, "Rule 7"){
      $rules['number'] = 7;
      $rules['text'] = "If you regularly come close to breaking the rules without actually breaking them, it will be treated as the rules being broken.";
      $ban->rules[] = $rules;
    }

    return $ban;
  }

  public function adminBanCounts() {
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT count(*) AS bans,
      bantype,
      a_ckey AS admin
      FROM ss13ban
      GROUP BY bantype, a_ckey
      ORDER BY bans DESC;");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }

  public function getPlayerBans($ckey){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT *,
      TIMESTAMPDIFF(MINUTE, ss13ban.bantime, ss13ban.expiration_time) AS minutes
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