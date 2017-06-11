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
  public $round_id = null;
  public $round_id_href = null;
  public $round_id_link = null;

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
      TIMESTAMPDIFF(MINUTE, tbl_ban.bantime, tbl_ban.expiration_time) AS minutes
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
    TIMESTAMPDIFF(MINUTE, tbl_ban.bantime, tbl_ban.expiration_time) AS minutes
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
    //Extract rules found in the ban
    $ban->rules = array();
    $ban = $this->parseBanReason($ban);

    //Get server ban was applied on
    $round = new round();
    $ban->serverip = $round->mapServer(long2ip($ban->server_ip).":".$ban->server_port);

    //The ban status should be done on MySQL, this is a crutch and I don't like
    //it :(
    //Defaults
    $ban->status = 'Active';
    $ban->statusClass = 'danger';

    //Times to a clearer format
    $ban->bantimestamp = timeStamp($ban->bantime);
    $ban->expirationtimestamp = timeStamp($ban->expiration_time);
    $ban->duration = $ban->minutes;

    //IP to clearer format
    if (is_int($ban->ip)){
      $ban->ip = long2ip($ban->ip);
    }

    //Link to round 
    $ban->round_id_link = null;
    if(isset($ban->round_id)){
      $ban->round_id_href = APP_URL."round.php?round=$ban->round_id";
      $ban->round_id_link = " (Round <a href='$ban->round_id_href'>";
      $ban->round_id_link.= "#$ban->round_id</a>)";
    }

    //Set ban type and associated data
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

    //Unbanned information
    if ($ban->unbanned){
      $ban->status = "Unbanned by $ban->unbanned_ckey ".timeStamp($ban->unbanned_datetime);
      $ban->statusClass = 'success';
    }

    //Admins online
    $badmins = array();
    foreach (explode(', ', $ban->adminwho) as $admin){
      $badmin['ckey'] = $admin;
      $badmin['ref'] = strtolower(preg_replace('~[^a-zA-Z0-9]+~', '', $admin));
      $badmins[] = $badmin;
    }
    asort($badmins);
    $ban->adminwho = $badmins;

    //Players online
    $players = array();
    foreach (explode(', ', $ban->who) as $ckey){
      $player['ckey'] = $ckey;
      $player['ref'] = strtolower(preg_replace('~[^a-zA-Z0-9]+~', '', $ckey));
      $players[] = $player;
    }
    asort($players);
    $ban->who = $players;

    //Any edits
    if ($ban->edits){
      $ban->edits = str_replace("<cite>", "<p><cite>", $ban->edits);
      $ban->edits = str_replace("</cite>", "</cite></p>", $ban->edits);
    }

    //Quicklinks for IP associated with the ban
    $ban->ipql = "<div class='ql'>(";
    $ban->ipql.= "<a href='".APP_URL."tgdb/bans.php?ip=$ban->ip'>";
    $ban->ipql.= "<i class='fa fa-ban'></i></a>)";
    $ban->ipql.= "(<a href='".APP_URL."tgdb/players.php?ip=$ban->ip'>";
    $ban->ipql.= "<i class='fa fa-user'></i></a>)";
    $ban->ipql.= "(<a href='".APP_URL."tgdb/conn.php?ip=$ban->ip'>";
    $ban->ipql.= "<i class='fa fa-plug'></i></a>)";
    $ban->ipql.= "</div>";

    //Quicklinks for CID associated with the ban
    $ban->cidql = "<div class='ql'>(";
    $ban->cidql.= "<a href='".APP_URL."tgdb/bans.php?cid=$ban->computerid'>";
    $ban->cidql.= "<i class='fa fa-ban'></i></a>)";
    $ban->cidql.= "(<a href='".APP_URL."tgdb/players.php?cid=$ban->computerid'>";
    $ban->cidql.= "<i class='fa fa-user'></i></a>)";
    $ban->cidql.= "(<a href='".APP_URL."tgdb/conn.php?cid=$ban->computerid'>";
    $ban->cidql.= "<i class='fa fa-plug'></i></a>)";
    $ban->cidql.= "</div>";

    //Permalinks for the ban
    $ban->link = APP_URL."/tgdb/viewBan.php?ban=$ban->id";
    $ban->permalink = "<a href='$ban->link'>#$ban->id</a>";

    return $ban;
  }

  public function parseBanReason(&$ban){
    $ban->reason_raw = $ban->reason;
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
      || str_contains($ban->reason, "Rule 3")){
      $rules['number'] = 3;
      $rules['text'] = "IC in OOC";
      $ban->rules[] = $rules;
    }

    //Probable rule 7
    if(str_contains($ban->reason, "bait")
      || str_contains($ban->reason, "Rule 7")){
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
      FROM tbl_ban
      GROUP BY bantype, a_ckey
      ORDER BY bans DESC;");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }

  public function getPlayerBans($ckey,$active=false){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $where = null;
    if($active){
      $where = "AND tbl_ban.expiration_time > NOW() OR tbl_ban.unbanned IS NULL";
    }
    $db->query("SELECT *,
      TIMESTAMPDIFF(MINUTE, tbl_ban.bantime, tbl_ban.expiration_time) AS minutes
      FROM tbl_ban
      WHERE ckey = ?
      $where
      ORDER BY bantime DESC");
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

  public function getBanComments($ban){
    $where = "flagged = 'A'";
    $user = new user();
    if(2 <= $user->level) {
      $where.= " OR flagged = 'P' OR flagged = 'R'";
    }
    $db = new database(TRUE);
    $db->query("SELECT * FROM ban_comment WHERE ban = ? AND ($where) ");
    $db->bind(1, $ban);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $comments = $db->resultset();
    foreach ($comments as &$comment){
      $comment = $this->parseComment($comment);
    }
    return $comments;
  }

  public function getAllBanComments(){
    $db = new database(TRUE);
    $db->query("SELECT * FROM ban_comment ORDER BY `timestamp` DESC");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $comments = $db->resultset();
    foreach ($comments as &$comment){
      $comment = $this->parseComment($comment);
    }
    return $comments;
  }


  public function parseComment(&$comment){
    $user = new user();
    $Parsedown = new safeDown();

    //Flag
    switch ($comment->flagged){
      default: //Pending
        $comment->flag = "Pending";
        $comment->class = "primary";
      break;

      case 'A':
        $comment->flag = "Approved";
        $comment->class = "success";
      break;

      case 'R':
        if (2 <= $user->level){
          $comment->flag = "Reported";
          $comment->class = "danger";
        } else {
          $comment->flag = "Approved";
          $comment->class = "success";
        }
      break;

      case 'H':
        $comment->flag = "Hidden";
        $comment->class = "default";
      break;
    }

    //Ban link
    $comment->ban_href = APP_URL."/tgdb/viewBan.php?ban=$comment->ban";
    $comment->ban_link = "<a href='$comment->ban_href'>#$comment->ban</a>";

    //Report link
    $comment->reportHref = $comment->ban_href."&reportComment&id=$comment->id";

    //Approve link
    $comment->approveHref = $comment->ban_href."&approveComment&id=$comment->id";

    //Hide link
    $comment->hideHref = $comment->ban_href."&hideComment&id=$comment->id";

    //Text
    $comment->text = $Parsedown->text($comment->text);

    //Author link
    $comment->author_href = APP_URL."tgdb/viewPlayer.php?ckey=$comment->author";
    $comment->author_link = "<a href='$comment->author_href'>$comment->author</a>";

    return $comment;
  }

  public function addComment($ban, $text) {
    $flag = 'P';
    $user = new user();
    if(!$user->legit){
      die("You must be a known user in order to submit comments");
    }
    if (2 <= $user->level){
      $flag = 'A'; //Admin comments auto-approve!
    }
    $db = new database(TRUE);
    $db->query("INSERT INTO ban_comment
      (ban, `text`, texthash, author, `timestamp`, flagged)
      VALUES (?, ?, sha1(?), ?, NOW(), ?)");
    $db->bind(1, $ban);
    $db->bind(2, $text);
    $db->bind(3, $text);
    $db->bind(4, $user->ckey);
    $db->bind(5, $flag);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    if (2 <= $user->level){
      return returnSuccess("Your comment has been submitted.");
    }
    return returnSuccess("Your comment has been submitted and is pending approval");
  }

  public function flipCommentFlag($id, $flag) {
    $user = new user();
    $db = new database(TRUE); //Alt DB
    if(!$user->legit){
      return returnError("You must be a known user in order to flag comments");
    }
    switch ($flag) {
      case 'A':
        if($user->level < 2){
          return returnError("You do not have permission to approve comments");
        }
        $flagText = 'Comment approved';
      break;

      case 'H':
        if($user->level < 2){
          return returnError("You cannot perform this action");
        }
        $flagText = 'Comment hidden';
      break;

      case 'R':
        $flagText = 'Comment reported';
        $db->query("UPDATE ban_comment
          SET
          ban_comment.reporter = ?,
          ban_comment.reported_time = NOW()
          WHERE ban_comment.id = ?");
        $db->bind(1, $user->ckey);
        $db->bind(2, $id);
        try {
          $db->execute();
        } catch (Exception $e) {
          return returnError("Database error: ".$e->getMessage());
        }
      break;

      default:
        return returnError("This is not a valid comment flag");
      break;
    }
    $db->query("UPDATE ban_comment
      SET
      ban_comment.flagged = ?,
      ban_comment.flag_changer = ?,
      ban_comment.flag_change = NOW()
      WHERE ban_comment.id = ?");
    $db->bind(1, $flag);
    $db->bind(2, $user->ckey);
    $db->bind(3, $id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return returnSuccess("$flagText");
  }

}