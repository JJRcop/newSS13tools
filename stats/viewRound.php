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
    class='btn btn-info btn-xs'><span class='glyphicon glyphicon-search'></span>
    View logs</a>
    </small>

  </h1>
</div>

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
<?php endif;?>

<?php require_once('../footer.php'); ?>