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
      MIN(prev.id) AS `prev`
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

  public function getBanList($page = 0, $count=100) {
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT * FROM tbl_ban ORDER BY id DESC LIMIT ?, ?");
    $db->bind(1, $page);
    $db->bind(2, $count);
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
    //The ban status should be done on MySQL, this is a crutch and I don't like
    //it :(
    
    $round = new round();
    $ban->serverip = $round->mapServer($ban->serverip);

    $ban->status = 'Active';
    $ban->statusClass = 'danger';

    $ban->bantimestamp = timeStamp($ban->bantime);
    $ban->expirationtimestamp = timeStamp($ban->expiration_time);

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

    return $ban;
  }

}