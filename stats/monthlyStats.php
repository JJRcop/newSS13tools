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

  $stat = $stat->getStatForMonth($viewStat, $date->format("m"), $date->format("Y"));?>
    <?php
    include(ROOTPATH.'/stats/statspages/'.$stat->include.'.php');
    ?>

  <?php else : //Viewing a list of ALL STATS for month/year ?>

    <?php 

    $date = new dateTime("$month/01/$year");

    $stats = $stat->getMonthlyStat($date->format("Y"),$date->format("m"));
    $rounds = $stat->getRoundStatsForMonth($date->format("Y"),$date->format("m"));
    // var_dump($rounds);
    ?>

    <div class="page-header">
      <h1>Stats for <?php echo $date->format("F Y");?></h1>
    </div>
    <div class="row">
      <div class="col-md-4">
        <table class="table sort table-bordered table-condensed">
          <thead>
            <tr>
              <th>Stat</th>
              <th>Times Recorded</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($stats as $s):?>
              <tr>
              <?php $link = APP_URL."stats/monthlyStats.php?year=".$date->format('Y');
              $link.= "&month=".$date->format('m')."&stat=$s->var_name";?>
                <td>
                  <a href="<?php echo $link;?>">
                    <?php echo $s->var_name;?>
                  </a>
                </td>
                <td><?php echo $s->times;?></td>
              </tr>
            <?php endforeach;?>
          </tbody>
        </table>
        <ul class="list-unstyled">
        
        </ul>
      </div>
      <div class="col-md-8">
        <table class="table sort table-bordered table-condensed">
          <thead>
            <tr>
              <th>Game Mode</th>
              <th>Result</th>
              <th># of Rounds</th>
              <th>Avg. Duration</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rounds as $r):?>
              <tr>
                <td><?php echo $r->game_mode;?></td>
                <td><?php echo $r->result;?></td>
                <td><?php echo $r->rounds;?></td>
                <td><?php echo $r->avgduration;?></td>
              </tr>
            <?php endforeach;?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif;?>
<?php else: //Viewing a list of all months with stats ?>
  <?php $stats = $stat->getMonthsWithStats(); ?>
  <div class="page-header">
    <h1>Stats by month</h1>
  </div>

  <table class="table sticky  table-bordered table-condensed">
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