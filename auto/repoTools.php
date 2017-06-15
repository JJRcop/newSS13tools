<?php
require __DIR__ . '/../config.php';
PHP_Timer::start();
$app = new app();
if(!$app->isCLI()){
  die("This script can only be executed on the command line.");
}

//Attempt to update the local repo
$codebase = new codebase(array('remote'));

//And mapmerge, just for funsies.
shell_exec('/bin/bash '.ROOTPATH.'/auto/mapmerge.sh '.DMEDIR);

echo date('[r]')." $codebase->message || Repo updated and maps merged in ".PHP_Timer::resourceUsage()."\n\r";