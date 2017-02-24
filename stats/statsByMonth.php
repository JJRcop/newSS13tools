<?php require_once('../header.php') ;?>

<?php

$start    = (new DateTime('2011-10-30'))->modify('first day of this month');
$end      = (new DateTime())->modify('last day of last month');
$interval = DateInterval::createFromDateString('1 month');
$period   = new DatePeriod($start, $interval, $end);
$dates = array();
foreach ($period as $dt) {
  $dates[] = $dt->format("Y-m-d");
}
$dates = array_reverse($dates);
?>

<ul class="list-inline">
<?php foreach ($dates as $d):?>
  <li><a href="#"><?php echo $d;?></a></li>
<?php endforeach;?>
</ul>
<?php require_once('../footer.php') ;?>