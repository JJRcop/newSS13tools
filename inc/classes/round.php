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
                $round = $this->getRoundLogs($round);
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

            case 'deaths':
            case 'death':
              $death = new death();
              $round->deaths = $death->getDeathsForRound($round->id);
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

    $round->logs = "<em>Forthcoming</em>";

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
    $database = $db->query("SELECT *,
    TIMEDIFF(tbl_round.end_datetime, tbl_round.start_datetime) AS duration
      FROM tbl_round
      ORDER BY tbl_round.end_datetime DESC
      LIMIT ?,?;");
    $db->bind(1,$page);
    $db->bind(2,$count);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $rounds = $db->resultset();
    foreach ($rounds as $round){
      $this->parseRound($round);
    }
    return $rounds;
  }

  public function getRound($id){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("
      SELECT tbl_round.*,
      TIMEDIFF(tbl_round.end_datetime, tbl_round.start_datetime) AS duration,
      next.round_id AS next,
      prev.round_id AS prev
      FROM tbl_round
      LEFT JOIN tbl_feedback AS next ON next.id = tbl_round.id + 1
      LEFT JOIN tbl_feedback AS prev ON prev.id = tbl_round.id - 1
      WHERE tbl_round.id = ?");
    $db->bind(1, $id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
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
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return  $db->resultset();
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
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $stat = new stat();
    $data = $db->single();
    $stat = $stat->parseFeedback($data);
    return $stat;
  }

  public function countRounds() {
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT count(id) AS total FROM tbl_round;");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single()->total;
  }

  public function getRoundLogs(&$round){
    if(file_exists($round->logCache)){
      $round->logs = $this->getCachedLogs($round->logCache);
      $round->fromCache = TRUE;
    } else {
      $round->logs = $this->getRemoteLogs($round->logURL);
      $round = $this->extractRoundLogs($round);
      $round->fromCache = FALSE;
    }
    return $round;
  }

  public function getCachedLogs($cache){
    $logs = file_get_contents($cache);
    return json_decode($logs);
  }

  public function getRemoteLogs($url){
    $logs = $this->fetchRemoteLogs($url);
    $logs = $this->cleanUpLogs($logs);
    return $logs;
  }

  public function cleanUpLogs($logs){
    //This method is ONLY for cleaning out useless lines from the logs

    //For the sake of transparency, this method is heavily documented
    //
    //-censored lines have no value in public logs, and are removed entirely
    $logs = str_replace("-censored(misc)-\r\n",'',$logs);
    $logs = str_replace("-censored(sql logs)-\r\n",'',$logs);
    $logs = str_replace("-censored(asay/apm/ahelp/notes/etc)-\r\n",'',$logs);
    $logs = str_replace(" from -censored(ip/cid)- ",' ',$logs);
    $logs = str_replace("-censored(private logtype)-\r\n", '', $logs);

    //For some reason, some lines are a single - and a return. These lines are
    //removed
    $logs = str_replace("-\r\n", '', $logs);

    //[hh:mm:ss]SAY: [mob name]/[ckey] : Something said
    //This line removes the spaces:   ^ ^
    $logs = str_replace(" : ",': ',$logs);

    //HTML spans are superfluous and removed
    $logs = str_replace("<span class='notice'>",'',$logs);
    $logs = str_replace("<span class='boldannounce'>",'',$logs);
    $logs = str_replace('</span>', '', $logs);

    //*no key* are mob emotes from mobs that aren't player controlled
    $logs = str_replace('*no key*/', '', $logs);

    //Deadchat(dsay) lines are prefixed with ghost/, this changes that to GHOST
    $logs = str_replace("SAY: Ghost/","GHOST: ", $logs);

    //This line splits the line into three parts:
    // Timestamp
    // Type
    // Content
    //The #-# is a line that I can easily break on in another method
    //
    //TODO: Randomize the #-# to stop people from (un)intentionally breaking
    //the parser
    $logs = preg_replace("/(\[)(\d{2}:\d{2}:\d{2})(])(GAME|ACCESS|SAY|OOC|ADMIN|EMOTE|WHISPER|PDA|CHAT|LAW|PRAY|COMMENT|VOTE|GHOST)(:\s)/","$2#-#$4#-#", $logs);

    //UTF8 encode (hey look who was ahead of the curve)
    $logs = utf8_encode($logs);

    //And split everything into a nice big array that we can iterate over
    $logs = explode("\r\n",$logs);

    //Remove empty lines
    array_filter($logs);

    //Remove the last line
    array_pop($logs);

    foreach ($logs as &$log){
      $log = explode('#-#',$log);
    }

    return $logs;
  }

  public function fetchRemoteLogs($url){
    $file = str_replace(REMOTE_LOG_SRC, '', $url);
    $file = str_replace("/", '-', $file);
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_URL => $url,
      CURLOPT_USERAGENT => "atlantaned.space log parser",
      CURLOPT_SSL_VERIFYPEER => FALSE,
      CURLOPT_SSL_VERIFYHOST => FALSE,
      CURLOPT_FOLLOWLOCATION => TRUE,
      CURLOPT_REFERER => "atlantaned.space",
      CURLOPT_ENCODING => 'gzip',
    ));
    $logs = curl_exec($curl);
    curl_close($curl);
    return $logs;
  }

  public function extractRoundLogs($round){
    $bounds = $this->getRoundBounds($round);
    $start = $bounds['start']['pos'];
    $end = $bounds['end']['pos'];
    $end = $end-$start+1;
    $round->logs = array_slice($round->logs, $start, $end);
    $this->cacheParsedLogs($round);
    return $round;
  }

  public function getRoundBounds($round){
    function diffSort($a, $b) {
      return ($a['diff'] < $b['diff']) ? -1 : 1;
    }
    $starts = array();
    $ends = array();
    $startTime = new DateTime($round->start);
    $endTime = new DateTime($round->end);
    $i = -1;
    foreach ($round->logs as $log){
      $i++;
      if ($log[1] === 'ADMIN' && $log[2] === 'Loading Banlist'){
        $log['time'] = $startTime->format("Y-m-d ").$log[0];
        $log['diff'] = abs(strtotime($log['time']) - strtotime($round->start));
        $log['pos'] = $i;
        $starts[] = $log;
      }
      if ($log[1] === 'GAME' && strpos($log[2],'Rebooting World.') !== FALSE){
        $log['time'] = $endTime->format("Y-m-d ").$log[0];
        $log['diff'] = abs(strtotime($log['time']) - strtotime($round->end));
        $log['pos'] = $i;
        $ends[] = $log;
      }
    }
    usort($starts,'diffsort');
    usort($ends,'diffsort');
    $return['start'] = $starts[0];
    $return['end'] = $ends[0];
    return $return;
  }

  public function cacheParsedLogs($round){
    $logsavefile = fopen($round->logCache,"w+");
    fwrite($logsavefile,json_encode($round->logs,JSON_UNESCAPED_UNICODE));
    fclose($logsavefile);
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

  public function getAllRoundComments(){
    $db = new database(TRUE);
    $db->query("SELECT * FROM round_comments ORDER BY `timestamp` DESC");
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

    //Round link
    $comment->round_href = APP_URL."round.php?round=$comment->round";
    $comment->round_link = "<a href='$comment->round_href'>#$comment->round</a>";

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
    if (2 <= $user->level){
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
        if(2 < $user->level){
          return returnError("You do not have permission to approve comments");
        }
        $flagText = 'Comment approved';
      break;

      case 'H':
        if(2 < $user->level){
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
