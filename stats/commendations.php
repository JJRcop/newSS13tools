<?php require_once('../header.php') ;?>

<?php $activity = $user->doAdminsPlay();?>
<div class="page-header">
  <h1>Global Commendations</h1>
</div>

<?php $stat = new stat();
$stat = $stat->getAggregatedFeedback('commendation');
$smol = true;
include(ROOTPATH.'/stats/statspages/'.$stat->include.'.php');?>


<?php require_once('../footer.php') ;?>