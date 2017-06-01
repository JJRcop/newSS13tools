<?php require_once('header.php');

if(empty($_GET) || isset($_GET['page'])){
  $round = new round();
  include('rounds/listRounds.php');
} elseif (isset($_GET['round']) && isset($_GET['stat'])){
  include('rounds/viewRoundStat.php');
} elseif (isset($_GET['round'])){
  include('rounds/viewRound.php');
}

require_once('footer.php');
