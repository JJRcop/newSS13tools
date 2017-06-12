<?php class round {

  public $start = false;
  public $end = false;
  public $duration = false;
  public $round_id = false;
  public $game_mode = false;
  public $server = false;
  public $logs = false;
  public $logURL = false;
  public $status = false;
  public $next = false;
  public $prev = false;
  public $hasObjectives = false;
  public $fromCache = false;
  public $data = false;
  public $commit_href = null;
  public $commit_link = "Not found";

  public $explosions = false;

  public function __construct($round=null,$data=false) {
    if($round){
      $round = $this->getRound($round);
      $round = $this->parseRound($round);
      // var_dump($round);
      if(is_array($data)){
        foreach ($data as $get){
          switch ($get){
            //Getting and parsing round logs
            case 'logs':
              if ($round->logs){
                $gameLogs = new GameLogs($round);
                $round = $gameLogs->getGameLogs();
              } else {
                $round->logs = false;
              }
            break;

            //Get and parse round stats(feedback)
            case 'data':
              $data = $this->getRoundFeedback($round->id);
              $data = $this->parseRoundFeedback($data);
              $round->data = new StdClass();
              foreach ($data as $d){
                $round->data->{$d->var_name} = $d;
              }
            break;

            //Deaths that occurred during this round
            case 'deaths':
            case 'death':
              $death = new death();
              $round->deaths = $death->getDeathsForRound($round->id);
            break;

            //Explosions extracted from roung logs (only available after a
            //rounds logs have been parsed)
            case 'explosion':
            case 'explosions':
              $round->explosions = $this->getExplosions($round->id);
            break;

            //Antagonists extracted from roung logs (only available after a
            //rounds logs have been parsed)
            case 'antag':
            case 'antags':
              $round->antags = $this->getAntags($round->id);
            break;
          }
        }
      }
      foreach ($round as $k => $v){
        $this->$k = $v;
      }
      // var_dump($round);
      return $round;
    } else {
      return false;
    }
  }

  public function parseRound(&$round){
    //Links
    $round->href = APP_URL."round.php?round=$round->id";
    $round->link = "<a href='$round->href'>#$round->id</a>";

    //Mode
    $round = $this->modeIcon($round);
    $round->game_mode = ucfirst($round->game_mode);

    //Map
    $round->map = str_replace('_', ' ', $round->map_name);

    //Shuttle
    $round->shuttle = str_replace('_', ' ', $round->shuttle_name);
    if(!$round->shuttle){
      $round->shuttle = "<em>Round ended without an evacuation</em>";
    }

    //Status
    $round->rare = false;
    $round = $this->mapStatus($round);

    //Times
    $round->start = date("Y-m-d H:i:s",strtotime($round->start_datetime));
    $round->end = date("Y-m-d H:i:s",strtotime($round->end_datetime));

    //Server
    $round->server = $this->mapServer($round->server_port);

    //Revision
    if($round->commit_hash){
      $round->commit_href = "https://github.com/".PROJECT_GITHUB."/commit/$round->commit_hash";
      $round->commit_link = "<a href='$round->commit_href' target='_blank'>";
      $round->commit_link.= strtoupper(substr($round->commit_hash, 0, 6))."</a>";
    }

    //Station name
    if(!$round->station_name){
      $round->station_name = '<em>Not specified</em>';
    }

    //Can we even get logs?
    if(defined('REMOTE_LOG_SRC')){
      $round->logs = TRUE;
    } else {
      $round->logs = FALSE;
    }

    //Log URL
    $server = strtolower($round->server);
    $date = new DateTime($round->start);
    $year = $date->format('Y');
    $month = $date->format('m');
    $day = $date->format('d');
    $round->logsURL = REMOTE_LOG_SRC."$server/data/logs/$year/$month/$day/";
    $round->logsURL.= "round-$round->id";

    //Log cache file
    $round->logCache = TMPDIR."/$round->id-$round->server-logs.json";

    return $round;
  }

  public function modeIcon(&$round){
    $round->modeIcon = "<i class='fa fa-fw fa-question-mark'></i> ";
    @$round->modeIcon = json_decode(file_get_contents(ROOTPATH."/inc/mode.json"),TRUE)[$round->game_mode];
    $round->modeIcon = "<i class='fa fa-fw fa-$round->modeIcon'></i> ";
    return $round;
  }

  public function mapStatus(&$round){
  if(null == $round->game_mode_result
    || 'undefined' == $round->game_mode_result){
      $round->result = ucfirst($round->end_state);
    } else {
      $round->result = ucfirst($round->game_mode_result);
    }

    $round->end_state = ucfirst($round->end_state);

    $round->statusIcon = null;
    $round->statusClass = null;

    if(strpos($round->result, 'Win - ') !== FALSE){
      $round->statusIcon = "<i class='fa fa-fw fa-trophy'></i>";
      $round->statusClass = "success";
    }

    if(strpos($round->result, 'Loss - ') !== FALSE){
      $round->statusIcon = "<i class='fa fa-fw fa-times'></i>";
      $round->statusClass = "danger";
    }

    if(strpos($round->result, 'Halfwin - ') !== FALSE){
      $round->statusIcon = "<i class='fa fa-fw fa-minus'></i>";
      $round->statusClass = "warning";
    }

    if(strpos($round->result, 'End - ') !== FALSE){
      $round->statusIcon = "<i class='fa fa-fw fa-minus'></i>";
      $round->statusClass = "warning";
    }

    if('Proper completion' == $round->result){
      $round->statusIcon = "<i class='fa fa-fw fa-check'></i>";
      $round->statusClass = "info";
    }

    if(strpos($round->result, 'Admin reboot - by') !== FALSE){
      $round->statusIcon = "<i class='fa fa-fw fa-refresh'></i>";
      $round->statusClass = "info";
    }

    if('Nuke' == $round->result && 'Nuclear emergency' != $round->game_mode){
      $round->statusIcon = "<i class='fa fa-fw fa-exclamation-triangle'></i>";
      $round->statusClass = "inverse";
      $round->rare = TRUE;
    }

    if(!$round->result){
      $round->result = "[No round result found]";
      $round->statusIcon = "<i class='fa fa-fw fa-question'></i>";
      $round->statusClass = "";
    }
    if (!$round->game_mode && !$round->server_port){
      $round->statusClass = "bad-round";
    }
    return $round;
  }

  public function mapServer($ip) {
    if(':' == $ip{0} || (strpos($ip,':') !== FALSE)){
      $ip = explode(':',$ip);
      if (!isset($ip[1])) return 'Unknown';
      $ip = $ip[1];
    } else {
      $ip = str_replace(':', '', $ip);
    }

    //Per MSO, we should be looking at the port #s.
    switch ($ip){
      case '2337':
        return 'Basil';
      break;

      case '1337':
        return 'Sybil';
      break;

      default:
        return 'Unknown';
      break;
    }
  }

  public function listRounds($page=1, $count=30){
    $page = ($page*$count) - $count;
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $database = $db->query("SELECT tbl_round.*,
    TIMEDIFF(tbl_round.end_datetime, tbl_round.start_datetime) AS duration
      FROM tbl_round
      ORDER BY tbl_round.end_datetime DESC
      LIMIT ?,?;");
    $db->bind(1,$page);
    $db->bind(2,$count);
    try {
      $rounds = $db->resultset();
      foreach ($rounds as $round){
        $this->parseRound($round);
      }
      return $rounds;
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }

  }

  public function getRound($id){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("
      SELECT tbl_round.*,
        TIMEDIFF(tbl_round.end_datetime, tbl_round.start_datetime) AS duration,
        MAX(next.id) AS next,
        MAX(prev.id) AS prev
        FROM tbl_round
        LEFT JOIN tbl_round AS next ON next.id = tbl_round.id + 1
        LEFT JOIN tbl_round AS prev ON prev.id = tbl_round.id - 1 
        WHERE tbl_round.id = ?");
    $db->bind(1, $id);
    try {
      return $db->single();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }

  }

  public function getRoundFeedback($round){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT var_name, var_value, details
      FROM tbl_feedback WHERE round_id = ?");
    $db->bind(1,$round);
    try {
      return  $db->resultset();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }

  }

  public function parseRoundFeedback(&$feedback){
    $stat = new stat();
    foreach($feedback as &$data){
      $data = $stat->parseFeedback($data);
    }
    return $feedback;
  }

  public function getRoundStat($round, $stat){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT * FROM tbl_feedback
      WHERE round_id = ? AND var_name = ?");
    $db->bind(1,$round);
    $db->bind(2,$stat);
    try {
      $stat = new stat();
      $data = $db->single();
      $stat = $stat->parseFeedback($data);
      return $stat;
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }

  }

  public function countRounds() {
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT count(id) AS total FROM tbl_round;");
    try {
      return $db->single()->total;
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }

  }

  public function getExplosions($round){
    $db = new database(TRUE);
    $db->query("SELECT explosion_log.* FROM explosion_log
      WHERE explosion_log.round = ?
      ORDER BY explosion_log.time DESC");
    $db->bind(1,$round);
    try {
      return $db->resultset();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }

  }

  public function getAntags($round){
    $db = new database(TRUE);
    $db->query("SELECT antag_log.role,
      group_concat(concat(antag_log.name,'(',antag_log.ckey,')') SEPARATOR ', ') AS antags
      FROM antag_log
      WHERE antag_log.round = ?
      GROUP BY antag_log.role;");
    $db->bind(1,$round);
    try {
      return $db->resultset();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }

  }

  public function getRoundComments($round){
    $where = "flagged = 'A'";
    $user = new user();
    if(2 <= $user->level) {
      $where.= " OR flagged = 'P' OR flagged = 'R'";
    }
    $db = new database(TRUE);
    $db->query("SELECT * FROM round_comments WHERE round = ? AND ($where) ");
    $db->bind(1, $round);
    try {
      $comments = $db->resultset();
      foreach ($comments as &$comment){
        $comment = $this->parseComment($comment);
      }
      return $comments;
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }

  }

  public function getAllRoundComments(){
    $db = new database(TRUE);
    $db->query("SELECT * FROM round_comments ORDER BY `timestamp` DESC");
    try {
      $comments = $db->resultset();
      foreach ($comments as &$comment){
        $comment = $this->parseComment($comment);
      }
      return $comments;
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }

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
        $comment->class = "default row-hidden";
      break;
    }

    //Round link
    $comment->round_href = APP_URL."round.php?round=$comment->round";
    $comment->round_link = "<a href='$comment->round_href'>#$comment->round</a>";

    //Text
    $comment->rawtext = $comment->text;
    $comment->text = $Parsedown->text($comment->text);

    //Author link
    $comment->author_href = APP_URL."tgdb/viewPlayer.php?ckey=$comment->author";
    $comment->author_link = "<a href='$comment->author_href'>$comment->author</a>";

    return $comment;
  }

  public function addComment($round, $text) {
    $flag = 'P';
    $user = new user();
    if(!$user->legit){
      die("You must be a known user in order to submit comments");
    }
    if (2 >= $user->level){
      $flag = 'A'; //Admin comments auto-approve!
    }
    $db = new database(TRUE);
    $db->query("INSERT INTO round_comments
      (round, `text`, texthash, author, `timestamp`, flagged)
      VALUES (?, ?, sha1(?), ?, NOW(), ?)");
    $db->bind(1, $round);
    $db->bind(2, $text);
    $db->bind(3, $text);
    $db->bind(4, $user->ckey);
    $db->bind(5, $flag);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
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
        $db->query("UPDATE round_comments
          SET
          round_comments.reporter = ?,
          round_comments.reported_time = NOW()
          WHERE round_comments.id = ?");
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
    $db->query("UPDATE round_comments
      SET
      round_comments.flagged = ?,
      round_comments.flag_changer = ?,
      round_comments.flag_change = NOW()
      WHERE round_comments.id = ?");
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
