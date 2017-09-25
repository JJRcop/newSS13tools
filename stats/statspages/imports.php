<?php require_once("statsHeader.php");?>

<div class="row">
  <div class="col-md-12">
  <table class="table sticky table-condensed table-bordered sort">
    <thead>
      <tr>
        <th>Path</th>
        <th>Name</th>
        <th>Cost per crate</th>
        <th>Total Ordered</th>
        <th>Total Spent</th>
      </tr>
    </thead>
    <tbdoy>
    <?php foreach ($stat->details as $k => $v):?>
      <tr>
        <td><?php echo $v['crate'];?></td>
        <td><?php echo $v['name'];?></td>
        <td><?php echo $v['cost'];?></td>
        <td><?php echo $v['count'];?></td>
        <td><?php echo $v['cost'] * $v['count'];?></td>
      </tr>
    <?php endforeach;?>
    </tbdoy>
    <tfoot>
      <tr>
        <th colspan='3'></th>
        <th><?php echo $stat->totalOrdered;?></th>
        <th><?php echo $stat->totalSpent;?></th>
      </tr>
  </table>
  </div>
</div>