<?php

require __DIR__ . '/config.php';
$app = new app();
if(!$app->isCLI()){
  echo"This script can only be executed on the command line. Please ensure ";
  echo "that the following line is in the crontab for the <code>".$_SERVER['USER'];
  echo "</code> user: <br><br><code>* * * * * cd ".ROOTPATH." && php jobby.php  1>> /dev/null 2>&1</code>";
  die();
}
$jobby = new Jobby\Jobby();
$jobby->add('AcquireAdminsTxt', array(
    'command' => "php ".ROOTPATH."/auto/downloadAdminsTxt.php",
    'schedule' => '*/10 * * * *', //Every 10 minutes
    'output' => ROOTPATH."/logs/admintxt.log",
    'enabled' => TRUE,
));

$jobby->add('CleanUp', array(
    'command' => "php ".ROOTPATH."/auto/cleanUp.php",
    'schedule' => '0 0 * * 0', //Weekly
    'output' => ROOTPATH."/logs/cleanUp.log",
    'enabled' => TRUE,
));

$jobby->run();

