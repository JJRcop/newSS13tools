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
    <?php echo $death->pod;?> on <?php echo $death->mapname;?> (<?php echo $death->coord;?>)<br>
    <small><?php echo $death->tod;?> on <?php echo $death->server;?></small>
  </div>
</div>
<!-- <div class="row">
  <div class="col-md-6">
    <div class="well mapviewer" style="
    background-image: url(<?php echo $death->mapfile;?>);
    background-position-x: <?php echo 128-$death->x;?>px;
    background-position-y: <?php echo 128-$death->y;?>px;">
    </div>
  </div>
</div> -->
