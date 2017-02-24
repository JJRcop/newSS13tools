<?php
$stat = false;
if(isset($_GET['stat'])) $stat = filter_input(INPUT_GET, 'stat', FILTER_SANITIZE_STRING, array(FILTER_FLAG_STRIP_HIGH));

require_once('../header.php'); ?>

<?php if(!$stat):?>
  <div class="alert alert-danger">No stat specified</div>
<?php die(); endif;?>

<?php $stat = new stat($stat); ?>

<div class="page-header">
  <h1>Single statistic: <code> <?php echo $stat->var_name;?></code></h1>
</div>
<p class="lead">Across <?php echo $stat->total;?> rounds (where this statistic was tracked) between <?php echo "$stat->first and $stat->last";?>:</p>
<?php if (!$stat->details && $stat->value) {
  include('statspages/bigNum.php');
} else {
  switch ($stat->var_name){
    case 'radio_usage':
      $radio = $stat->details;
      include('../rounds/statspages/radio.php');
    break;

    case 'job_preferences':
      $prefs = $stat->details;
      include('../rounds/statspages/jobprefs.php');
    break;

    case 'traitor_success':
    case 'wizard_success':
    case 'changeling_success':
      $details = $stat->details;
      include('../rounds/statspages/success.php');
    break;

    case 'traitor_objective':
    case 'wizard_objective':
    case 'changeling_objective':
      $details = $stat->details;
      include('../rounds/statspages/objs.php');
    break;

    case 'round_end_result':
      include('statspages/generic.php');
    break;

    // case 'game_mode':
    //   include('statspages/modestats.php');
    // break;

    default: 
      if(0 == $stat->value){
        include('statspages/generic.php');
      } else {
        var_dump($stat);
      }
    break;
  }
} 
?>

<?php require_once('../footer.php'); ?>