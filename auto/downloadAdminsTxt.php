<?php
require __DIR__ . '/../config.php';
PHP_Timer::start();
$app = new app();
if($app->downloadAdminsTxt()){
  echo date('[r]')." Generated in ".PHP_Timer::resourceUsage()."\n\r";
}