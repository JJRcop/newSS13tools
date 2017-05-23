<?php require_once("../header.php");?>
<?php require_once('tgdb_nav.php');?>
<p class="lead">ahelp activity over the last seven days:</p>
<?php $ahelps = $app->getAhelpStats();
$dates = array();
$dayTotals = array();
foreach ($ahelps as &$a){
  $dates[] = $a->day;
  $tmp[$a->var_name][$a->day] = $a->count;
}
$dates = array_unique($dates);
?>
<table class="table table-bordered table-condensed">
  <thead>
    <tr><th></th>
      <?php foreach ($dates as $d):?>
        <th><?php echo $d;?></th>
      <?php endforeach;?>
      <th>Total by type</th>
      </tr>
    </thead>
  <tbody>
    <?php foreach ($ahelps as $a => $d):?>
      <tr><th><?php echo $a;?></th>
        <?php foreach ($dates as $cd):?>
          <?php if (isset($d[$cd])):?>
            <td><?php echo $d[$cd]; @$dayTotals[$cd] += $d[$cd];?></td>
          <?php else:?>
            <td>0</td>
          <?php endif;?>
        <?php endforeach;?>
        <th><?php echo array_sum($d);?></th>
      </tr>
    <?php endforeach;?>
    </tbody>
    <tfoot>
      <tr>
        <th>Total by day</th>
          <?php foreach ($dayTotals as $t):?>
            <th><?php echo $t;?></th>
          <?php endforeach;?>
        <td></td>
      </tr>
    </tfoot>
</table>