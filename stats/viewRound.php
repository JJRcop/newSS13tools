<?php require_once('../header.php'); ?>

<?php
if (!isset($_GET['round'])) die("No round ID specified!");
$round = filter_input(INPUT_GET, 'round', FILTER_SANITIZE_NUMBER_INT);
$round = new round($round,TRUE);
?>

<div class="page-header">
  <h1>Round #<?php echo $round->round_id;?>
    <small>Ended <?php echo $round->end;?>
    <?php if ($round->logs): ?>
      <a href='viewRoundLogs.php?round=<?php echo $round->round_id;?>'
    class='btn btn-info btn-xs'>
        <span class='glyphicon glyphicon-search'></span>
        View logs
      </a>
    <?php endif; ?>
    </small>
  </h1>
</div>

<?php if (!$round->logs): ?>
  <p class="lead">Unable to accurately locate logs for round #<?php echo $round->round_id;?>, because either the server or round start time could not be located.</p>
<?php endif;?>


  <p class="lead pull-center text-center">
<?php
if (isset($round->data->game_mode)){ 
  $duration = '';
  if (isset($round->data->round_start)) {
    $start = strtotime($round->data->round_start['details']);
    $end = strtotime($round->data->round_end['details']);
    $duration = " lasted ".round(abs($start - $end) / 60). " minutes and ";
  }
  echo "A round of ".ucfirst($round->data->game_mode['details']);
  if (isset($round->server)) {echo " on $round->server ";}
  echo $duration;
    if ('double agents' == $round->data->game_mode['details']
    || 'traitor' == $round->data->game_mode['details']
    || 'wizard' == $round->data->game_mode['details']){ ?>
  resulted in<br>
  <?php if (isset($round->data->traitor_success['details']['SUCCESS'])):?>
  <span class='label label-success'>
    Success: <?php echo $round->data->traitor_success['details']['SUCCESS'];?>
  </span> 
  <?php endif;?>

  &nbsp;

  <?php if (isset($round->data->traitor_success['details']['FAIL'])): ?>
  <span class='label label-danger'>
    Fail: <?php echo $round->data->traitor_success['details']['FAIL'];?>
  </span>
  <?php endif;?>

  <?php
  }
}
if (isset($round->data->round_end_result)) {
  echo " resulted in ".ucfirst($round->data->round_end_result['details']);
}
?>

</p>

<?php if (isset($round->data->emergency_shuttle)) :?>
  <p class="lead pull-center text-center">The crew evacuated aboard <em><?php echo str_replace('_', ' ', $round->data->emergency_shuttle['details']);?></em></p>
<?php endif;?>


<?php if (isset($round->data->export_sold_amount) || isset($round->data->cargo_imports)) {
  include 'statspages/econ.php';
} ?>

<?php if (isset($round->data->ore_mined)){
  include 'statspages/mining.php';
} ?>

<?php if (isset($round->data->job_preferences)){
  include 'statspages/jobprefs.php';
} ?>

<div class="page-header">
  <h2>
    <a class="btn btn-primary" role="button" data-toggle="collapse" href="#rawdata" aria-expanded="false" aria-controls="collapseExample">
      View
    </a> Raw Data</h2>
</div>

<div class=" collapse" id="rawdata">
  <table class="table table-bordered table-condensed">
    <thead>
      <tr>
        <th>Data point</th>
        <th>Value</th>
        <th>Details</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($round->data as $k => $v) :?>
      <tr>
        <td><?php echo $k;?></td>
        <td><?php echo $v['value']; ?></td>
        <td><?php if (is_array($v['details'])){
            var_dump($v['details']);
            } else {
              echo $v['details'];
            }?>
        </td>
      </tr>
    <?php endforeach;?>
    </tbody>
  </table>
</div>

<?php if ($round->deaths) :?>
<?php var_dump($round->deaths);?>
<?php endif;?>

<?php require_once('../footer.php'); ?>