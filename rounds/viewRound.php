<?php
$json = false;
if (isset($_GET['json'])) $json = filter_input(INPUT_GET, 'json', FILTER_VALIDATE_BOOLEAN);
if (!isset($_GET['round'])) die("No round ID specified!");
$round = filter_input(INPUT_GET, 'round', FILTER_SANITIZE_NUMBER_INT);

require_once('../header.php');
$round = new round($round,TRUE);

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

<?php include('statspages/loginfo.php');?>

<?php include('statspages/game_mode.php');?>

<?php include('statspages/pages.php'); ?>

<div id="rawdata">
  <h3>Raw data</h3>
  <ul class="list-unstyled">
    <?php foreach ($round->data as $k => $v) :?>
      <li>
        <code>
          <a href='<?php echo APP_URL;?>rounds/viewRoundStat.php?stat=<?php echo $k;?>&round=<?php echo $round->round_id;?>'>
            <?php echo $k;?>
          </a>
        </code>
      </li>
    <?php endforeach;?>
    </ul>
</div>

<?php require_once('statspages/pagination.php');?>

<?php require_once('../footer.php'); ?>