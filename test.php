<?php require_once('header.php'); ?>

<div class="row">
<div class="col-md-10">

<?php

$db = new database();
$db->query("SET SESSION group_concat_max_len = 1000000;");
$db->execute();
$db->query("SELECT ss13feedback.var_name,
GROUP_CONCAT(ss13feedback.round_id SEPARATOR ', ') AS rounds,
SUM(ss13feedback.var_value) AS `value`,
IF (ss13feedback.details = '', NULL, GROUP_CONCAT(ss13feedback.details SEPARATOR ', ')) AS details
      FROM ss13feedback
      WHERE DATE(ss13feedback.time) BETWEEN (NOW() - INTERVAL 30 DAY) AND NOW()
      AND ss13feedback.var_name != ''
      GROUP BY ss13feedback.var_name
      ORDER BY ss13feedback.var_name ASC;");

$db->execute();

$parse = new stat();
$stats = $db->resultset();
foreach($stats as $stat){
  $stat->total = count(explode(', ',$stat->rounds));
  $stat = $parse->parseFeedback($stat, TRUE);?>
  <div class="page-header" id="<?php echo $stat->var_name;?>">
  <h1><code><?php echo $stat->var_name;?></code></h1>
  </div>
  <p class="lead">Across <?php echo $stat->total;?> rounds (where this statistic was recorded):</p>
  <?php 
  if ('' == $stat->var_name) continue;
  if (!$stat->details && $stat->value) {
    include('stats/statspages/bigNum.php');
  } else {
    switch ($stat->var_name){

      default:
        include('stats/statspages/generic.php');
      break;      

      case 'radio_usage':
        $radio = $stat->details;
        include('stats/statspages/radio.php');
      break;

      case 'job_preferences':
        $prefs = $stat->details;
        include('stats/statspages/jobprefs.php');
      break;

      case 'traitor_success':
      case 'wizard_success':
      case 'changeling_success':
        $details = $stat->details;
        include('stats/statspages/success.php');
      break;

      case 'traitor_objective':
      case 'wizard_objective':
      case 'changeling_objective':
      case 'cult_objective':
        $details = $stat->details;
        include('stats/statspages/objs.php');
      break;

      case 'round_end_result':
        include('stats/statspages/generic.php');
      break;

    }
  } 
}
?>
</div>
<div class="col-md-2">
<ul class="list-unstyled">
<?php foreach($stats as $s):?>
  <li><a href="#<?php echo $s->var_name;?>"><code><?php echo $s->var_name;?></code></a></li>
<?php endforeach;?>
</ul>
</div>
</div>

<?php require_once('footer.php');?>