<?php
require_once('../config.php');
PHP_Timer::start();
if (!isset($_GET['month'])) die("No month specified!");
$month = filter_input(INPUT_GET, 'month', FILTER_SANITIZE_NUMBER_INT);

if (!isset($_GET['year'])) die("No year specified!");
$year = filter_input(INPUT_GET, 'year', FILTER_SANITIZE_NUMBER_INT);

$date = new dateTime("$month/01/$year");

$stat = new stat();
$stats = $stat->generateMonthlyStats($date->format("Y"),$date->format("m"));

echo $stats." ".PHP_Timer::resourceUsage();