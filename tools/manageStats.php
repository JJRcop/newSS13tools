<?php require_once('../header.php'); ?>
<?php echo alert('## CLASSIFIED ## GA+//NA</strong> This page is classified. This page should not shared with non-admins.');?>

<div class="page-header">
  <h1>Manage Monthly Stats</h1>
</div>

<div class="row">
  <div class="col-md-12">
    <?php
    $stat = new stat();
    $stats = $stat->getMonthsWithStats();

    $start    = (new DateTime('2011-10-30'))->modify('first day of this month');
    $end      = (new DateTime())->modify('last day of this month');
    $interval = DateInterval::createFromDateString('1 month');
    $period   = new DatePeriod($start, $interval, $end);
    $dates = array();
    $avail = array();
    foreach ($period as $dt) {
      $dates[] = $dt->format("F Y");
    }
    $dates = array_reverse($dates);

    foreach ($stats as $stat){
      $date = new datetime(date("$stat->year-$stat->month-01 00:00:00"));
      $avail[] = $date->format("F Y");
    }

    $gen = array_diff($dates, $avail);?>

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
      $date = new datetime("$month->year-$month->month-01");
      $link = APP_URL."stats/monthlyStats.php?year=$month->year";
      $link.= "&month=$month->month";
      $regenhref = APP_URL."auto/generateMonthlyStats.php?month=$month->month";
      $regenhref.= "&year=$month->year&regen=true";
      ?>
        <tr>
          <td><a href="<?php echo $regenhref;?>" class="btn btn-danger btn-xs">
            <i class="fa fa-refresh"></i> Regenerate</a> <a href="<?php echo $link;?>">
            <?php echo $date->format('F Y');?>
          </a>
          </td>
          <td><?php echo $month->stats;?></td>
          <td><?php echo $month->rounds;?></td>
          <td><?php echo $month->firstround;?></td>
          <td><?php echo $month->lastround;?></td>
        </tr>
      <?php endforeach;?>
      <?php foreach ($gen as $g):?>
        <?php $date = new datetime($g);
        $m = date("m",strtotime($g));
        $y = date("Y",strtotime($g));
        $href = APP_URL."auto/generateMonthlyStats.php?month=$m&year=$y";?>
        <tr>
          <td colspan="5">
            <a href="<?php echo $href;?>" class="btn btn-success btn-xs">
            Generate</a> <?php echo $g;?></td>
        </tr>
      <?php endforeach;?>
      </tbody>
    </table>
  </div>
</div>
<script>
$('tr.generate').click(function(e){
  var el = $(this);
  console.log(el);
  var file = $(this).attr('data-file');
  $(this).toggleClass("warning");
  $.ajax({
    url: 'splitDMIIntoPNGs.php?icon='+file,
    method: 'GET',
    dataType: 'json'
  })
  .done(function(e){
    console.log(e);
    // $(this).children('.pngs').addClass('success');
    $(el).toggleClass("warning").toggleClass('success');
    $(el).children('td #png-status').toggleClass('glyphicon-remove').toggleClass('glyphicon-ok');
    console.log($(el).children('td .glyphicon'));
    $(el).children('.file').append(' '+e.msg);
  })
  .success(function(e){
  })
  .error(function(e){
    console.log(e);
  })
});
$('#genAll').click(function(e){
  e.preventDefault();
  $(this).attr('disabled','true');
  $('tr.generate').click();
})
</script>
<?php require_once('../footer.php'); ?>