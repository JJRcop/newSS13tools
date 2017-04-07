<?php
require_once('header.php');
$stat = new newStat();
$stat = $stat->getAggregatedFeedback('traitor_uplink_items_bought');
include(ROOTPATH."/stats/statspages/$stat->include.php");

require_once('footer.php');