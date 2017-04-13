<?php

//
// Add this line to your crontab file:
//
// * * * * * cd /path/to/project && php jobby.php 1>> /dev/null 2>&1
//

require __DIR__ . '/config.php';
$jobby = new Jobby\Jobby();
$jobby->add('AcquireAdminsTxt', array(
    'command' => "php ".ROOTPATH."/auto/downloadAdminsTxt.php",
    'schedule' => '*/10 * * * *', //Every 10 minutes
    'output' => ROOTPATH."/logs/admintxt.log",
    'enabled' => TRUE,
));

$jobby->run();


