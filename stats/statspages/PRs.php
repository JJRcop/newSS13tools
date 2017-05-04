<div class="jumbotron">
  <h1>
    <small>Stats for</small> <?php echo $stat->var_name;?>
    <?php if(isset($date)):?>
      <small>in <?php echo $date->format("F Y");?></small>
    <?php endif;?>
  </h1>
  <p class="lead">Tracked 
  <?php if(isset($stat->rounds)):?>
    across <?php echo $stat->rounds;?> rounds, and
  <?php endif;?>
  <?php echo $stat->var_value;?> times in total</p>
  <?php if(isset($stat->round_id)):?>
    <p class="lead">From round <?php echo $round->link;?></p>
  <?php endif;?>
</div>

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