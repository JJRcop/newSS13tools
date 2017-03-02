<?php require_once('config.php');

$stat = new stat();
var_dump($stat->generateMonthlyStats(2017,02));
