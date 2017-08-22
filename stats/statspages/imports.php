<?php require_once("statsHeader.php");?>

<div class="row">
  <div class="col-md-12">
  <table class="table sticky  table-condensed table-bordered">
    <thead>
      <tr>
        <th>Path</th>
        <th>Name</th>
        <th>Cost</th>
        <th>Total Ordered</th>
      </tr>
    </thead>
    <tbdoy>
    <?php foreach ($stat->details as $k => $v):?>
      <tr>
        <td><?php echo $v['crate'];?></td>
        <td><?php echo $v['name'];?></td>
        <td><?php echo $v['cost'];?></td>
        <td><?php echo $v['count'];?></td>
      </tr>
    <?php endforeach;?>
    </tbdoy>
    <tfoot>
      <tr>
        <th colspan='2'></th>
        <th><?php echo $stat->totalSpent;?></th>
        <th><?php echo $stat->totalOrdered;?></th>
      </tr>
  </table>
  </div>
</div>