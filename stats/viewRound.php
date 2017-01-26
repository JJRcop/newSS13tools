<?php require_once('../header.php'); ?>

<?php
if(!$_GET['round']) die("No round specified");
$round = filter_input(INPUT_GET, 'round');
$logs = false;
if(isset($_GET['logs'])) $logs = filter_input(INPUT_GET, 'logs');
$round = new round($round,$logs);
?>

<div class="page-header">
  <h1>Round #<?php echo $round->roundid;?>
    <small>Ended <?php echo $round->roundend;?>
      <a href='viewRound.php?round=<?php echo $round->roundid;?>&logs=true'
    class='btn btn-info btn-xs'>
        <span class='glyphicon glyphicon-search'></span>
        View logs
      </a>
    </small>
  </h1>
</div>
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

<?php if ($logs) :?>
<div class='log-wrap'>
  <table class="logs">
  <?php $i = 0; foreach ($round->logrange as $log){
    echo "<tr id='L-$i'><td class='ln'>#$i</td><td class='ts'>[".date('H:i:s',strtotime($log->timestamp))."]</td><td class='ld'>
      <span class='log ".strtolower($log->logtype)."'><strong>$log->logtype: </strong>$log->content</span>
    </td></tr>";
    $i++;
  }?>
  </table>
</div>
<?php else: ?>

<?php if (isset($round->data->export_sold_amount) || isset($round->data->cargo_imports)) {
  include 'statspages/econ.php';
} ?>

<?php if (isset($round->data->ore_mined)){
  include 'statspages/mining.php';
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
            }?></td>
        
      </tr>
    <?php endforeach;?>
    </tbody>
  </table>
</div>

<?php endif;?>

<?php require_once('../footer.php'); ?>