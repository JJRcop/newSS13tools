<?php
require_once('../config.php');
PHP_Timer::start();

$month = date('m');
$year = date('Y');

$date = new dateTime("$month/01/$year");

$stat = new stat();
// $stats = $stat->generateMonthlyStats($month,$year);

echo $stats." ".PHP_Timer::resourceUsage();