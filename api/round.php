<?php require_once('../config.php');
header('Content-Type: application/json');

$count = filter_input(INPUT_GET, 'count', FILTER_VALIDATE_INT);
$full = filter_input(INPUT_GET, 'full', FILTER_VALIDATE_BOOLEAN);

$rounds = new round();
$rounds = $rounds->listRounds(1,$count);
if($full){
  foreach($rounds as &$round){
    $round = new round($round->id);
  }
}
echo json_encode($rounds);