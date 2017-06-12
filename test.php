<?php $skip = true;
$wide = true;
require_once('header.php');?>

<?php $stat = new stat('traitor_uplink_items_purchased');
var_dump($stat->getAggregatedFeedback('traitor_uplink_items_bought'));
?>

<?php require_once('footer.php');