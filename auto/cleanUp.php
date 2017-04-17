<?php
require __DIR__ . '/../config.php';
PHP_Timer::start();
$app = new app();
if(!$app->isCLI()){
  die("This script can only be executed on the command line.");
}
if($cleanup = $app->cleanUp()){
  echo date('[r]')." $cleanup in ".PHP_Timer::resourceUsage()."\n\r";
}