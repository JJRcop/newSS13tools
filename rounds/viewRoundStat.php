<?php
$json = false;
if (isset($_GET['json'])) $json = filter_input(INPUT_GET, 'json', FILTER_VALIDATE_BOOLEAN);
if (!isset($_GET['round'])) die("No round ID specified!");
$round = filter_input(INPUT_GET, 'round', FILTER_SANITIZE_NUMBER_INT);

$viewStat = filter_input(INPUT_GET, 'stat', FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_HIGH);

require_once('../header.php');
$round = new round($round,FALSE);
$stat = $round->getRoundStat($round->round_id,$viewStat);
?>

<nav>
  <ul class="pager">
  <?php if ($round->prev): ?>
    <li class="previous">
        <a href='<?php echo APP_URL;?>rounds/viewRoundStat.php?stat=<?php echo $stat->var_name;?>&round=<?php echo $round->prev;?>'>
        <span aria-hidden="true">&larr;</span>
        Previous round</a>
    </li>
  <?php endif;?>
  <li><?php echo $round->link;?></li>
  <?php if ($round->next): ?>
    <li class="next">
      <a href='<?php echo APP_URL;?>rounds/viewRoundStat.php?stat=<?php echo $stat->var_name;?>&round=<?php echo $round->next;?>'>Next round
      <span aria-hidden="true">&rarr;</span></a>
    </li>
  <?php endif;?>
  </ul>
</nav>

<?php if (!$round->round_id):?>
  <div class="alert alert-danger">Round not found: #<?php echo $_GET['round'];?></div>
<?php die(); endif;?>

<?php include(ROOTPATH.'/stats/statspages/'.$stat->include.'.php');?>

<?php require_once('../footer.php');?>
