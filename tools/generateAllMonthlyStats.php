<?php require_once('../header.php'); ?>
<?php echo alert('## CLASSIFIED ## GA+//NA</strong> This page is classified. This page should not shared with non-admins.');?>

<div class="page-header">
  <h1>Generate all monthly stats</h1>
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
    foreach ($period as $dt) {
      $dates[] = array(
        'date' => $dt->format("F Y"),
        'link' => APP_URL."stats/viewMonthlyStats.php?year=".$dt->format('Y')."&month=".$dt->format('m')
      );
    }
    $dates = array_reverse($dates);

    $start = new DateTime("2011-10-01");
    $end = new DateTime('last month');
    $interval = DateInterval::createFromDateString('1 month');
    $period = new DatePeriod($start, $interval, $end);
    $calendar = array();
    foreach($period as $dt){
      $calendar[] = array(
        'date'=>$dt->format('F Y'),
        'link'=>APP_URL."auto/generateMonthlyStats.php?year=".$dt->format('Y')."&month=".$dt->format('m')
      );
    }
    $calendar = array_reverse($calendar);

    ?>

    <ul class="list-unstyled">
    <?php foreach ($calendar as $d){
      foreach ($stats as $date){
        $count = $date->stats;
        $date = new dateTime("$date->month/01/$date->year");
        $inDB = $date->format('F Y');
        $link = APP_URL."stats/viewMonthlyStats.php?year=".$date->format('Y');
        $link.= "&month=".$date->format('m');
        if ($inDB == $d['date']) {
          echo "<li><i class='fa fa-check'></i> <a href='$link'>$inDB";
          echo "</a> $count datapoints</li>";
          continue 2;
        }
      }
      echo "<li><a href='".$d['link']."'>".$d['date']."</li></a>";
    } ?>
    </ul>
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