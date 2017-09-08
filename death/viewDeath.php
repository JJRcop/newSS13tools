<?php
if (isset($_GET['death'])) {
  $death = filter_input(INPUT_GET, 'death', FILTER_SANITIZE_NUMBER_INT);
  $death = new death($death);
} else {
  echo parseReturn(returnError("No death found"));
}
?>

<div class="page-header">
  <h1><small>R.I.P.</small> <?php echo $death->name;?> <small><?php echo $death->job;?></small>
  <?php if($death->special):?>
    <p class="pull-right">
      <span class="label label-danger"><?php echo $death->special;?></span>
    </p>
  <?php endif;?>
  </h1>
</div>
<div class="row">
  <div class="col-md-4">
    <h2>Final Vital Signs</h2>
    <?php echo $death->labels;?>
  </div>
  <div class="col-md-4">
    <h2>Cause of death</h2>
    <?php echo $death->cause;?>
  </div>
  <div class="col-md-4">
    <h2>Location and Time</h2>
    <?php echo $death->pod;?> on <?php echo $death->mapname;?> (<?php echo "$death->x, $death->y, $death->z"?>)<br>
    <small><?php echo $death->tod;?> on <?php echo $death->server;?> (Round <?php echo $death->roundLink;?>)</small>
  </div>
  <canvas id="map" width="300" height="300"></canvas>
</div>

<script>
var ctx = document.getElementById('map').getContext('2d');
  var sx = <?php echo $death->x;?>;
  var sy = <?php echo $death->y;?>;
  var img = new Image();
  img.addEventListener('load', function() {
    ctx.mozImageSmoothingEnabled = false;
    ctx.webkitImageSmoothingEnabled = false;
    ctx.msImageSmoothingEnabled = false;
    ctx.imageSmoothingEnabled = false;
    ctx.drawImage(img,  sx*8-50, sy*8-25, 300, 300, 0, 0, 1024, 1024);
  }, false);
  img.src = "<?php echo $death->mapfile;?>";
</script>
