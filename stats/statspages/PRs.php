<?php require_once("statsHeader.php");?>

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
      <?php $link = "https://github.com/".PROJECT_GITHUB."/issues/$k";?>
      <tr>
        <td>
          <a href="<?php echo $link;?>" target="_blank">
            #<?php echo $k;?>
          </a>
        </td>
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