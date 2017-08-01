<?php

if(empty($_GET) || isset($_GET['page'])){
  $include = 'rounds/listRounds.php';
} elseif (isset($_GET['round']) && isset($_GET['stat'])){
  $round = filter_input(INPUT_GET, 'round', FILTER_SANITIZE_NUMBER_INT);
  $include = 'rounds/viewRoundStat.php';
} elseif (isset($_GET['round']) && isset($_GET['logs'])){
  $round = filter_input(INPUT_GET, 'round', FILTER_SANITIZE_NUMBER_INT);
  $logs = filter_input(INPUT_GET, 'logs', FILTER_VALIDATE_BOOLEAN);
  $include = 'rounds/viewRoundLogs.php';
  $wide = true;
} elseif (isset($_GET['round'])){
  $include = 'rounds/viewRound.php';
}

require_once('header.php');


include($include);



require_once('footer.php');
