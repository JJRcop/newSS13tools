<?php
require_once('header.php');
$stat = new newStat();
$stat = $stat->getAggregatedFeedback('game_mode');
include(ROOTPATH."/stats/statspages/$stat->include.php");

require_once('footer.php');