<?php require_once('../header.php') ;?>

<?php $stat = new stat();
$stats = $stat->getMonthsWithStats();?>
<div class="page-header">
  <h1>Stats by month</h1>
</div>

<ul class="list-inline">
  <?php foreach ($stats as $date):
  $count = $date->stats;
  $date = new dateTime("$date->month/01/$date->year");
  $link = APP_URL."stats/viewMonthlyStats.php?year=".$date->format('Y');
  $link.= "&month=".$date->format('m');
  ?>
    <li>
      <a href="<?php echo $link;?>">
        <?php echo $date->format('F Y')." - $count datapoints";?>
      </a>
    </li>
  <?php endforeach;?>
</ul>

<?php require_once('../footer.php') ;?>