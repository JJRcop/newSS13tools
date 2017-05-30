<?php
require __DIR__ . '/../config.php';
$app = new app();
$user = new user();
// var_dump($user);
if(!$app->isCLI() && $user->level < 1){
  die("This script can only be executed on the command line.");
}

PHP_Timer::start();
$stat = new stat();

$month = date('m');
$year = date('Y');
$regen = false;

if (isset($_GET['month'])){
  $month = filter_input(INPUT_GET,'month',FILTER_VALIDATE_INT,array(
    'min_range' => 1,
    'max_range' => 12,
    'flags' => FILTER_FLAG_ALLOW_OCTAL
  ));
}

if (isset($_GET['year'])){
  $year = filter_input(INPUT_GET,'year',FILTER_VALIDATE_INT,array(
    'min_range' => 2011
  ));
}

if (isset($_GET['regen'])) $regen = filter_input(INPUT_GET, 'regen', FILTER_VALIDATE_BOOLEAN);


if($regen){
  $stat = $stat->regenerateMonthlyStats($month,$year);
} else {
  $stat = $stat->generateMonthlyStats($month,$year);
}
echo $stat." ".PHP_Timer::resourceUsage();