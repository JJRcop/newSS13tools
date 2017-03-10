<?php require_once('../header.php') ;?>

<?php
if (!isset($_GET['month'])) die("No month specified!");
$month = filter_input(INPUT_GET, 'month', FILTER_SANITIZE_NUMBER_INT);

if (!isset($_GET['year'])) die("No year specified!");
$year = filter_input(INPUT_GET, 'year', FILTER_SANITIZE_NUMBER_INT);

$date = new dateTime("$month/01/$year");

$stat = new stat();
$stats = $stat->getMonthlyStats($date->format("Y"),$date->format("m"));
?>
<div class="page-header">
  <h1>Stats for <?php echo $date->format("F Y");?></h1>
</div>
<div class="row">
<div class="col-md-10">
<?php foreach ($stats as $stat):?>
  <div class="page-header" id="<?php echo $stat->var_name;?>">
    <h1><code><?php echo $stat->var_name;?></code></h1>
    </div>
    <p class="lead">Across <?php echo $stat->rounds;?> rounds (where this statistic was recorded):</p>
    <?php 
    $stat->details = json_decode($stat->data,TRUE);
    if (!$stat->details && $stat->value) {
      include(ROOTPATH.'/stats/statspages/bigNum.php');
    } else {
      switch ($stat->var_name){
        default:
          include(ROOTPATH.'/stats/statspages/generic.php');
        break;      
        case 'radio_usage':
          $radio = $stat->details;
          include(ROOTPATH.'/rounds/statspages/radio.php');
        break;
        case 'job_preferences':
          $prefs = $stat->details;
          include(ROOTPATH.'/stats/statspages/jobprefs.php');
        break;
        case 'traitor_success':
        case 'wizard_success':
        case 'changeling_success':
          $details = $stat->details;
          include(ROOTPATH.'/rounds/statspages/success.php');
        break;
        case 'traitor_objective':
        case 'wizard_objective':
        case 'changeling_objective':
        case 'cult_objective':
          $details = $stat->details;
          include(ROOTPATH.'/rounds/statspages/objs.php');
        break;
      }
    }
  endforeach;?>
  </div>
  <div class="col-md-2">
  <ul class="list-unstyled">
  <?php foreach($stats as $s):?>
    <li><a href="#<?php echo $s->var_name;?>"><code><?php echo $s->var_name;?></code></a></li>
  <?php endforeach;?>
  </ul>
  </div>
  </div>

<?php require_once('../footer.php') ;?>