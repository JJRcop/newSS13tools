<ul class="list-inline">
<li><strong>Total</strong> - <?php echo $stat->var_value;?></li>
<?php foreach($stat->details as $channel => $num):?>
  <li><strong><?php echo $channel;?></strong> - <?php echo $num;?></li>
<?php endforeach;?>
</ul>
<div class="row">
  <div class="col-md-6">
  <table class="table table-condensed table-bordered">
    <thead>
      <tr>
        <th>Key</th>
        <th>Value</th>
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
      json: <?php echo json_encode($stat->details); ?>,
      type: 'donut',
    },
});

</script>