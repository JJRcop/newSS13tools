<?php

$endClients = $round->data->round_end_clients['value'];
$dead = $round->data->round_end_ghosts['value'];
$survivors = $round->data->survived_total['value'];
$survivingHumans = $round->data->survived_human['value'];
$escaped = false;
$escapedH = false;
if (isset($round->data->escaped_total) && isset($round->data->escaped_human)) {
  $escaped  = $round->data->escaped_total['value'];
  $escaped = $survivors - $escaped;
  $escapedH = $round->data->escaped_human['value'];
}
$total = $dead + $survivors; ?>

<div class="progress">
  <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo ($dead/$total)*100;?>%;">
  <span><?php echo $dead;?> dead</span>
  </div>
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo (($survivors-$escaped)/$total)*100;?>%;">
  <span><?php echo $survivors;?> survivors</span>
  </div>
  <?php if ($escaped):?>
  <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo ($escaped/$total)*100;?>%;">
  <span><?php echo $escaped;?> escaped alive</span>
  </div>
  <?php endif;?>
</div>
<small><p class="text-muted text-right">(This is an approximation)</p></small>
<?php if (isset($round->data->emergency_shuttle)) :?>
  <p class="lead text-right">The crew evacuated aboard <em><?php echo str_replace('_', ' ', $round->data->emergency_shuttle['details']);?></em></p>
<?php else:?>
  <p class="lead text-center">The round ended without a crew evacuation.</p>
<?php endif;?>
<hr>