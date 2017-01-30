<?php require_once('../header.php'); ?>

<?php
if (!isset($_GET['round'])) die("No round ID specified!");
$round = filter_input(INPUT_GET, 'round', FILTER_SANITIZE_NUMBER_INT);
$round = new round($round,TRUE);
?>

<?php if (!$round->round_id):?>
  <div class="alert alert-danger">Round not found: #<?php echo $_GET['round'];?></div>
<?php die(); endif;?>

<?php if(DEBUG):?>
<div class="alert alert-info">The hash for this round is <code><?php echo $round->hash; ?></code></div>
<?php endif;?>

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

<?php include('statspages/game_mode.php');?>

<?php

if (isset($round->data->round_end_ghosts) && isset($round->data->survived_total)) {
  include 'statspages/population.php';
}

if($round->hasObjectives) {
  include('statspages/objectives.php');
}

if (isset($round->data->export_sold_amount) || isset($round->data->cargo_imports)) {
  include 'statspages/econ.php';
}

if (isset($round->data->ore_mined) || isset($round->data->mobs_killed_mining)){
  include 'statspages/mining.php';
} 

if (isset($round->data->job_preferences)){
  include 'statspages/jobprefs.php';
}

if (isset($round->data->radio_usage)){
  include 'statspages/radio.php';
}

?>

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