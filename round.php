<?php
$json = false;
if (isset($_GET['json'])) $json = filter_input(INPUT_GET, 'json', FILTER_VALIDATE_BOOLEAN);
if(!$json){
  require_once('header.php');
} else{
  require_once('config.php');
}

if(empty($_GET) || isset($_GET['page'])){
  $round = new round();
  include('rounds/listRounds.php');
} elseif (isset($_GET['round']) && $json){
  $round = filter_input(INPUT_GET, 'round', FILTER_SANITIZE_NUMBER_INT);
  header('Content-Type: application/json');
  echo json_encode(new round($round,array('data','deaths')));
  die();
} elseif (isset($_GET['round']) && isset($_GET['stat'])){
  $round = filter_input(INPUT_GET, 'round', FILTER_SANITIZE_NUMBER_INT);
  include('rounds/viewRoundStat.php');
} elseif (isset($_GET['round'])){
  include('rounds/viewRound.php');
}

require_once('footer.php');
