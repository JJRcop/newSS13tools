<?php
header('Content-Type: application/json');
require_once('../config.php');
$user = new user();
if(2 > $user->level){
 die(json_encode("You are not authorized to use this tool."));
}
$query = null;
if (isset($_GET['query'])){
  $query = filter_input(INPUT_GET, 'query',
    FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_HIGH);
}
if ($query){
  $db = new database();
  $db->query("SELECT ckey FROM tbl_player
    WHERE ckey = ? 
    OR ckey LIKE ?
    OR ckey LIKE ?
    ORDER BY lastseen DESC
    LIMIT 0, 15");
  $db->bind(1,$query);
  $db->bind(2,"$query");
  $db->bind(3,"%$query%");
  try {
    $db->execute();
  } catch (Exception $e) {
    return returnError("Database error: ".$e->getMessage());
  }
  echo json_encode($db->resultset());
}
