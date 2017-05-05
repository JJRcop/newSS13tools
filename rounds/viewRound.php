<?php
$json = false;
if (isset($_GET['json'])) $json = filter_input(INPUT_GET, 'json', FILTER_VALIDATE_BOOLEAN);
if (!isset($_GET['round'])) die("No round ID specified!");
$round = filter_input(INPUT_GET, 'round', FILTER_SANITIZE_NUMBER_INT);

require_once('../header.php');
$round = new round($round,array('data'));
var_dump($round);
?>

<?php if (!$round->round_id):?>
  <div class="alert alert-danger">Round not found: #<?php echo $_GET['round'];?></div>
<?php die(); endif;?>

<?php if(DEBUG):?>
<div class="alert alert-info">The hash for this round is <code><?php echo hash('sha256',
json_encode($round->data)); ?></code></div>
<?php endif;?>

<?php require_once('statspages/pagination.php');?>

<div class="page-header">
  <h1>Round #<?php echo $round->round_id;?>
    <small>Ended <?php echo $round->end;?>
    <?php if ($round->logs): ?>
      <a href='viewRoundLogs.php?round=<?php echo $round->round_id;?>'
    class='btn btn-info btn-xs'>
        <i class="fa fa-tasks"></i>
        View logs
      </a>
    <?php endif; ?>
    </small>
  </h1>
</div>

<div class="row">

  <?php if(isset($round->data->traitor_objective)):?>
    <div class="col-md-4"><?php
    $stat = $round->data->traitor_objective;
    $smol = true;
    include(ROOTPATH.'/stats/statspages/'.$stat->include.'.php');?>
    </div>
  <?php endif;?>

  <?php if(isset($round->data->changeling_objective)):?>
    <div class="col-md-4"><?php
    $stat = $round->data->changeling_objective;
    $smol = true;
    include(ROOTPATH.'/stats/statspages/'.$stat->include.'.php');?>
    </div>
  <?php endif;?>

  <?php if(isset($round->data->wizard_objective)):?>
    <div class="col-md-4"><?php
    $stat = $round->data->wizard_objective;
    $smol = true;
    include(ROOTPATH.'/stats/statspages/'.$stat->include.'.php');?>
    </div>
  <?php endif;?>
</div>

<div id="rawdata">
  <h3>Raw data</h3>
  <ul class="list-unstyled">
    <?php foreach ($round->data as $d) :?>
      <li>
        <code>
          <a href='<?php echo APP_URL;?>rounds/viewRoundStat.php?stat=<?php echo $d->var_name;?>&round=<?php echo $round->round_id;?>'>
            <?php echo $d->var_name;?>
          </a>
        </code>
      </li>
    <?php endforeach;?>
    </ul>
</div>

<?php require_once('statspages/pagination.php');?>

<?php require_once('../footer.php'); ?>