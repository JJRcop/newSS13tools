<?php $skip = true;
$wide = true;
require_once('header.php');?>

<?php

$logs = new AltGameLogs();
var_dump($logs->getLogs("https://tgstation13.org/parsed-logs/sybil/data/logs/2017/07/02/round-72731/game.txt.gz"));


?>

<?php require_once('footer.php');
