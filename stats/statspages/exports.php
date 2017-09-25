<?php require_once("statsHeader.php");?>

<div class="row">
  <div class="col-md-12">
  <table class="table sticky table-condensed table-bordered sort">
    <thead>
      <tr>
        <th><?php echo $stat->path;?></th>
        <th><?php echo $stat->cost;?></th>
        <th><?php echo $stat->totalItems;?></th>
        <th><?php echo $stat->totalValue;?></th>
      </tr>
    </thead>
    <tbdoy>
    <?php foreach ($stat->details as $k => $v):?>
      <tr>
        <td><?php echo $v['crate'];?></td>
        <td><?php echo $v['cost'];?></td>
        <td><?php echo $v['count'];?></td>
        <td><?php echo $v['value'];?></td>
      </tr>
    <?php endforeach;?>
    </tbdoy>
    <tfoot>
      <tr>
        <th colspan='2'></th>
        <th><?php echo $stat->totalExports;?></th>
        <th><?php echo $stat->totalEarned;?></th>
      </tr>
  </table>
  </div>
</div>
