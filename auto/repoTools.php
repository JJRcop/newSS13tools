<?php
require __DIR__ . '/../config.php';
PHP_Timer::start();
$app = new app();
if(!$app->isCLI()){
  die("This script can only be executed on the command line.");
}
$app->updateLocalRepo();
shell_exec('/bin/bash '.ROOTPATH.'/auto/mapmerge.sh '.DMEDIR);

echo date('[r]')." Repo updated and maps merged in ".PHP_Timer::resourceUsage()."\n\r";