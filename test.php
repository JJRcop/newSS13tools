<?php require_once('header.php');?>

<?php $skip = true;
require_once('../header.php');

$list = array(
  'repukan'
);

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
  $candidates = array();
  foreach ($result as &$r){
    if(in_array($r->ckey,$list)){
      $candidates[] = $this->parseUser($r);
    }
  }
} catch (Exception $e) {
  return returnError("Database error: ".$e->getMessage());
}

var_dump($candidates);

require_once('footer.php');?>
