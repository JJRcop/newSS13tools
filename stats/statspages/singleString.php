<?php require_once("statsHeader.php");?>
<div class="row">
  <div class="col-md-6">
  <table class="table sticky  table-condensed table-bordered">
    <thead>
      <tr>
        <th><?php echo $stat->key;?></th>
        <th><?php echo $stat->value;?></th>
      </tr>
    </thead>
    <tbdoy>
    <?php foreach ($stat->details as $k => $v):?>
      <tr>
        <td><?php echo $k;?></td>
        <td><?php echo $v;?></td>
      </tr>
    <?php endforeach;?>
    </tbdoy>
    <tfoot>
      <tr>
        <th><?php echo $stat->total;?></th>
        <th><?php echo $stat->var_value;?></th>
      </tr>
    </tfoot>
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