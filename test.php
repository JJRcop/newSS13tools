<?php
require_once('header.php');

$stat = new stat();
var_dump($stat->generateMonthlyStats());
$stat = $stat->getMonthlyStat(2017, 03,'handcuffs');
include(ROOTPATH.'/stats/statspages/'.$stat->include.'.php');

require_once('footer.php');
