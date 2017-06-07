<?php class poll {

  public function __construct($id=null){
    if($id){
      $poll = $this->getPoll($id);
      $poll->options = $this->getPollOptions($poll->id);
      $poll->results = $this->getPollResults($poll->id);
      if('TEXT' === $poll->polltype){
        $poll->results = $this->getTextPollResults($poll->id);
      }
      if('NUMVAL' === $poll->polltype){
        $poll->results = $this->getRatingResults($poll->id);
      }
      if('IRV' === $poll->polltype){
        $poll->results = $this->getIRVPollResults($poll->id);
      }
      $poll = $this->parsePoll($poll);
      foreach ($poll as $k => $v){
        $this->$k = $v;
      }
      return $poll;
    }
  }

  public function getPoll($id=null){
    $and = null;
    if($id){
      $and = "AND tbl_poll_question.id = ?";
    }
    $db = new database();
    $db->query("SELECT tbl_poll_question.*,
    TIMEDIFF(tbl_poll_question.endtime, tbl_poll_question.starttime) AS duration,
    IF(tbl_poll_question.endtime < NOW(), 1, 0) AS ended,
    count(tbl_poll_vote.id) + count(tbl_poll_textreply.id)  as totalVotes
    FROM tbl_poll_question
    LEFT JOIN tbl_poll_vote ON tbl_poll_question.id = tbl_poll_vote.pollid
    LEFT JOIN tbl_poll_textreply ON tbl_poll_question.id = tbl_poll_textreply.pollid
    WHERE tbl_poll_question.dontshow = 0 AND tbl_poll_question.adminonly = 0
    $and
    GROUP BY tbl_poll_question.id
    ORDER BY tbl_poll_question.id DESC");
    if($id){
      $db->bind(1,$id);
    }
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    if($id){
      return $db->single();
    } else{
      return $db->resultset();
    }
  }

  public function getPollResults($id=null){
    $db = new database();
    $db->query("SELECT COUNT(tbl_poll_vote.id) AS votes,
      tbl_poll_option.text AS `option`
      FROM tbl_poll_vote
      LEFT JOIN tbl_poll_option ON tbl_poll_vote.optionid = tbl_poll_option.id
      WHERE tbl_poll_vote.pollid = ?
      GROUP BY tbl_poll_vote.optionid
      ORDER BY votes DESC;");
    $db->bind(1,$id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }

  public function getRatingResults($id=null){
    $db = new database();
    $db->query("SELECT COUNT(tbl_poll_vote.id) AS votes,
      tbl_poll_vote.rating AS `option`
      FROM tbl_poll_vote
      LEFT JOIN tbl_poll_option ON tbl_poll_vote.optionid = tbl_poll_option.id
      WHERE tbl_poll_vote.pollid = ?
      GROUP BY tbl_poll_vote.rating
      ORDER BY votes DESC;");
    $db->bind(1,$id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }

  public function getTextPollResults($id=null){
    $db = new database();
    $db->query("SELECT
      tbl_poll_textreply.id,
      tbl_poll_textreply.datetime,
      tbl_poll_textreply.pollid,
      tbl_poll_textreply.replytext,
      tbl_poll_textreply.adminrank,
      0 as hidden
    FROM tbl_poll_textreply
    WHERE pollid = ?
    ORDER BY tbl_poll_textreply.datetime DESC ");
    $db->bind(1,$id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }

  public function getIRVPollResults($id=null){
    $db = new database();
    $db->query("SELECT tbl_poll_textreply.id,
      tbl_poll_textreply.datetime,
      tbl_poll_textreply.pollid,
      tbl_poll_textreply.replytext,
      tbl_poll_textreply.adminrank
    FROM tbl_poll_textreply
    WHERE pollid = ?
    ORDER BY tbl_poll_textreply.datetime DESC ");
    $db->bind(1,$id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }

  public function getPollOptions($id=null){
    $db = new database();
    $db->query("SELECT tbl_poll_option.*
    FROM tbl_poll_option
    WHERE pollid = ?
    ORDER BY tbl_poll_option.id");
    $db->bind(1,$id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }

  public function parsePoll(&$poll){

    //Type
    $poll->type = $this->mapPollType($poll->polltype);

    //Percentages
    if('IRV' != $poll->polltype && 'TEXT' != $poll->polltype){
      foreach($poll->results as $r){
        $r->percent = floor(($r->votes/$poll->totalVotes)*100);
      }
    }

    //Hidden text replies
    if($poll->polltype == 'TEXT'){
      $hidden = $this->getHiddenReplies($poll->id);
      foreach ($poll->results as &$r){
        foreach ($hidden as $h){
          if ($r->id == $h->replyid){
            $r->hidden = true;
            $r->hide = $h;
          }
        }
      }
    }

    return $poll;
  }

  public function mapPollType($type){
    switch($type){
      case 'OPTION':
        return "Option";
      break;

      case 'TEXT':
        return "Text Reply";
      break;

      case 'NUMVAL':
        return "Numerical Rating";
      break;

      case 'MULTICHOICE':
        return "Multiple Choice";
      break;

      case 'IRV':
        return "Instant Runoff Voting";
      break;
    }
  }

  public function getHiddenReplies($poll){
    $db = new database(true);
    $db->query("SELECT * FROM hidden_poll_results WHERE pollid = ?");
    $db->bind(1, $poll);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }

  public function hidePollResult($poll, $reply){
    $user = new user();
    if(2 < $user->level){
      return returnError("You must be an administrator to perform this action.");
    }
    $db = new database(true);
    $db->query("INSERT INTO hidden_poll_results
      (pollid, replyid, hiddenby, hidden, hide)
      VALUES (?, ?, ?, NOW(), 1)");
    $db->bind(1, $poll);
    $db->bind(2, $reply);
    $db->bind(3, $user->ckey);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return returnSuccess("Reply hidden");
  }
}
