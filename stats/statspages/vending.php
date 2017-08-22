<?php require_once("statsHeader.php");?>

<div class="row">
  <div class="col-md-12">
  <table class="table sticky  table-condensed table-bordered">
    <thead>
      <tr>
        <th><?php echo $stat->key;?></th>
        <th><?php echo $stat->value;?></th>
      </tr>
    </thead>
    <tbdoy>
    <?php foreach ($stat->details as $k => $v): $m = 0;?>
      <tr>
        <th colspan='2' style="border-top: 2px solid">
          <?php echo $k;?>
        </th>
      </tr>
        <?php foreach ($v as $i => $c):?>
        <tr>
          <td><?php echo $i;?></td>
          <td><?php echo $c; $stat->var_value+= $c; $m+= $c;?></td>
        </tr>
        <?php endforeach;?>
      <tr>
        <th>Items From This Machine:</th>
        <td><?php echo $m;?></td>
      </tr>
    <?php endforeach;?>
    </tbdoy>
    <tfoot>
      <tr>
        <th>Total Items Vended</th>
        <th><?php echo $stat->var_value;?></th>
      </tr>
    </tfoot>
  </table>
  </div>
</div>
