<?php require_once('../header.php') ;?>

<?php
$stat = new stat();
$month = null;
$year = null;
$viewStat = null;
$month = filter_input(INPUT_GET, 'month', FILTER_SANITIZE_NUMBER_INT);
$year = filter_input(INPUT_GET, 'year', FILTER_SANITIZE_NUMBER_INT);
$viewStat = filter_input(INPUT_GET, 'stat', FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_HIGH);

if ($year && $month):?>
  <?php if ($viewStat): //Viewing a SINGLE STAT for month/year ?>

  <?php
  $date = new dateTime("$month/01/$year");

  $stat = $stat->getMonthlyStat($date->format("Y"),$date->format("m"),$viewStat);?>
    <?php
    $stat->details = json_decode($stat->data,TRUE);
    include(ROOTPATH.'/stats/statspages/'.$stat->include.'.php');
    ?>

  <?php else : //Viewing a list of ALL STATS for month/year ?>

    <?php 

    $date = new dateTime("$month/01/$year");

    $stats = $stat->getMonthlyStats($date->format("Y"),$date->format("m"));?>

    <div class="page-header">
      <h1>Stats for <?php echo $date->format("F Y");?></h1>
    </div>

    <ul class="list-unstyled">
    <?php foreach($stats as $s):?>
      <?php $link = APP_URL."stats/monthlyStats.php?year=".$date->format('Y');
      $link.= "&month=".$date->format('m')."&stat=$s->var_name";?>
      <li><a href="<?php echo $link;?>"><code><?php echo $s->var_name;?></code></a></li>
    <?php endforeach;?>
    </ul>

  <?php endif;?>
<?php else: //Viewing a list of all months with stats ?>
  <?php $stats = $stat->getMonthsWithStats(); ?>
  <div class="page-header">
    <h1>Stats by month</h1>
  </div>

  <ul class="list-unstyled">
    <?php foreach ($stats as $date):
    $count = $date->stats;
    $date = new dateTime("$date->month/01/$date->year");
    $link = APP_URL."stats/monthlyStats.php?year=".$date->format('Y');
    $link.= "&month=".$date->format('m');
    ?>
      <li>
        <a href="<?php echo $link;?>">
          <?php echo $date->format('F Y')." - $count datapoints";?>
        </a>
      </li>
    <?php endforeach;?>
  </ul>
<?php endif; ?>

<?php require_once('../footer.php') ;?>