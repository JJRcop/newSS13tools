<?php require_once("statsHeader.php");?>
<div class="row">
  <div class="col-md-6">
  <table class="table sticky  table-condensed table-bordered">
    <thead>
      <tr>
        <th>Item</th>
        <th>Damage</th>
        <th>Times used</th>
        <th>Total Damage</th>
      </tr>
    </thead>
    <tbdoy>
    <?php $used = 0; $totalDam = 0; foreach ($stat->details as $k => $v):?>
      <?php $k = str_replace('/obj/item', '', $k); $k = explode('|',$k);?>
      <tr>
        <td><?php echo $k[0];?></td>
        <td><?php echo $k[1]; $used += $k[1];?></td>
        <td><?php echo $v;?></td>
        <td><?php echo $k[1]*$v; $totalDam += $k[1]*$v;?></td>
      </tr>
    <?php endforeach;?>
      <tr>
        <th>Total</th>
        <th><?php echo $stat->var_value;?></th>
        <th><?php echo $used;?></th>
        <th><?php echo $totalDam;?></th>
      </tr>
    </tbdoy>
  </table>
  </div>
  <div class="col-md-6">
    <div id="c" style="height: 512px;">
    </div>
  </div>
</div>
<script>

var chart = c3.generate({
    bindto: '#c',
    data: {
      json: <?php echo json_encode(array_chunk($stat->details, 20, TRUE)[0]); ?>,
      type: 'donut',
    },
});

</script>