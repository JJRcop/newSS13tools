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
      WHERE ss13messages.type != 'memo'
      ORDER BY id DESC LIMIT ?, ?");
    $db->bind(1, $page);
    $db->bind(2, $count);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    foreach ($messages = $db->resultSet() as &$message){
      $message = $this->parseMessage($message);
    }
    return $messages;
  }

  public function getMessage($id) {
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT * FROM tbl_messages
      WHERE ss13messages.id = ?");
    $db->bind(1, $id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }

  public function getPlayerMessages($ckey) {
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT * FROM tbl_messages
      WHERE ss13messages.targetckey = ?");
    $db->bind(1, $ckey);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    foreach ($messages = $db->resultSet() as &$message){
      $message = $this->parseMessage($message);
    }
    return $messages;
  }

  public function parseMessage(&$message){

    $message->text = auto_link_text($message->text);


    $message->ckeylink = "<a href='viewPlayer.php?ckey=$message->targetckey'>";
    $message->ckeylink.= "$message->targetckey</a>";

    $message->permalink = "<a href='viewMessage.php?message=$message->id'>";
    $message->permalink.= "#$message->id</a>";

    $message->timeStamp = timeStamp($message->timestamp);

    $message->icon = "<i class='fa fa-sticky-note'></i>";
    $message->class = 'warning';

    $message->privacy = '';

    if ($message->secret && 'note' == $message->type) {
      $message->privacy = "(This is a secret $message->type)";
    } elseif ('note' == $message->type){
      $message->privacy = "(This is a visible $message->type)";
    } else {
      $message->privacy = '';
    }

    switch ($message->type){
      case 'note':
      default:
        //Inherit defaults
      break;

      case 'message':
        $message->icon = "<i class='fa fa-bullhorn'></i>";
        $message->class = 'success';
      break;

      case 'message sent':
        $message->icon = "<i class='fa fa-check'></i> ";
        $message->icon.= "<i class='fa fa-bullhorn'></i>";
        $message->class = 'success';
      break;

      case 'watchlist entry':
        $message->icon = "<i class='fa fa-eye'></i>";
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
      WHERE ss13messages.type = 'watchlist entry'
      ORDER BY id DESC LIMIT ?, ?");
    $db->bind(1, $page);
    $db->bind(2, $count);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    foreach ($messages = $db->resultSet() as &$message){
      $message = $this->parseMessage($message);
    }
    return $messages;
  }

}