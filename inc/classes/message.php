<?php

class message {

  public function __construct($id=null){
    if ($id){
      $message = $this->getMessage($id);
      $message = $this->parseMessage($message);
      foreach ($message as $k => $v){
        $this->$k = $v;
      }
      return $message;
    }
    return false;
  }

  public function getMessageList($page = 0, $count=100){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT * FROM tbl_messages
      WHERE tbl_messages.type != 'memo'
      AND tbl_messages.deleted = 0
      ORDER BY id DESC LIMIT ?, ?");
    $db->bind(1, $page);
    $db->bind(2, $count);
    try {
      foreach ($messages = $db->resultSet() as &$message){
        $message = $this->parseMessage($message);
      }
      return $messages;
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

  public function getMessage($id) {
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT * FROM tbl_messages
      WHERE tbl_messages.id = ?");
    $db->bind(1, $id);
    try {
      return $db->single();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

  public function getPlayerMessages($ckey,$secret=false) {
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $and = "AND tbl_messages.secret = 0";
    if($secret){
      $and = null;
    }

    $db->query("SELECT * FROM tbl_messages
      WHERE tbl_messages.targetckey = ?
      AND tbl_messages.deleted = 0
      $and
      ORDER BY `timestamp` DESC");
    $db->bind(1, $ckey);
    try {
      foreach ($messages = $db->resultSet() as &$message){
        $message = $this->parseMessage($message);
      }
      return $messages;
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

  public function parseMessage(&$message){

    $message->text = auto_link_text($message->text);


    $message->ckeylink = "<a href='viewPlayer.php?ckey=$message->targetckey'>";
    $message->ckeylink.= "$message->targetckey</a>";

    $message->permalink = "<a href='viewMessage.php?message=$message->id'>";
    $message->permalink.= "#$message->id</a>";

    $message->timeStamp = timeStamp($message->timestamp);

    $message->icon = "<i class='fa fa-sticky-note' title='Note'";
    $message->icon.= " data-toggle='tooltip'></i>";
    $message->class = 'warning';

    $message->privacy = '';

    if ($message->secret && 'note' == $message->type) {
      $message->privacy = "<i class='fa fa-eye-slash' title='Secret note'";
      $message->privacy.= "data-toggle='tooltip'></i>";
    } elseif ('note' == $message->type){
      $message->privacy = "<i class='fa fa-eye' title='Visible note'";
      $message->privacy.= "data-toggle='tooltip'></i>";
    } else {
      $message->privacy = '';
    }

    switch ($message->type){
      case 'note':
      default:
        //Inherit defaults
      break;

      case 'message':
        $message->icon = "<i class='fa fa-bullhorn' title='Message'";
        $message->icon.= " data-toggle='tooltip'></i>";
        $message->icon.= "<i class='fa fa-clock' title='Undelivered'";
        $message->icon.= " data-toggle='tooltip'></i>";
        $message->class = 'success';
      break;

      case 'message sent':
        $message->icon = "<i class='fa fa-bullhorn' title='Message'";
        $message->icon.= " data-toggle='tooltip'></i> ";
        $message->icon.= "<i class='fa fa-check' title='Delivered'";
        $message->icon.= " data-toggle='tooltip'></i>";
        $message->class = 'success';
      break;

      case 'watchlist entry':
        $message->icon = "<i class='fa fa-exclamation'";
        $message->icon.= "title='Watchlist Entry' data-toggle='tooltip'></i>";
        $message->class = 'danger';
      break;
    }
    $message->type = ucfirst($message->type);

    if ($message->edits){
      $message->edits = str_replace("<cite>", "<p><cite>", $message->edits);
      $message->edits = str_replace("</cite>", "</cite></p>", $message->edits);
    }
    return $message;
  }

  public function activeWatchlist($page = 0, $count=100){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT * FROM tbl_messages
      WHERE tbl_messages.type = 'watchlist entry'
      AND tbl_messages.deleted = 0
      ORDER BY id DESC LIMIT ?, ?");
    $db->bind(1, $page);
    $db->bind(2, $count);
    try {
      foreach ($messages = $db->resultSet() as &$message){
        $message = $this->parseMessage($message);
      }
      return $messages;
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }

  }

}