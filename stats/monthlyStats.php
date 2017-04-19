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
    // $stat->details = json_decode($stat->data,TRUE);
    if(is_array($stat->details) && $stat->include == 'bigText') {
      $stat->include = 'singleString';
    }
    include(ROOTPATH.'/stats/statspages/'.$stat->include.'.php');
    ?>

  <?php else : //Viewing a list of ALL STATS for month/year ?>

    <?php 

    $date = new dateTime("$month/01/$year");

    $stats = $stat->getMonthlyStat($date->format("Y"),$date->format("m"));?>

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

  <table class="table table-bordered table-condensed">
    <thead>
      <tr>
        <th>Date</th>
        <th>Datapoints</th>
        <th># of Rounds</th>
        <th>First Round ID</th>
        <th>Last Round ID</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($stats as $month):
    $date = new dateTime("$month->month/01/$month->year");
    $link = APP_URL."stats/monthlyStats.php?year=".$date->format('Y');
    $link.= "&month=".$date->format('m');
    ?>
      <tr>
        <td><a href="<?php echo $link;?>">
          <?php echo $date->format('F Y');?>
        </a>
        </td>
        <td><?php echo $month->stats;?></td>
        <td><?php echo $month->rounds;?></td>
        <td><?php echo $month->firstround;?></td>
        <td><?php echo $month->lastround;?></td>
      </tr>
    <?php endforeach;?>
    </tbody>
  </table>
<?php endif; ?>

<?php require_once('../footer.php') ;?>