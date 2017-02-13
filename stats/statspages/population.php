<?php

$dead = 0;
$endClients = 0;
$survivors = 0;
$survivingHumans = 0;
$escaped = 0;
$escapedH = 0;

if(isset($round->data->round_end_clients)) $endClients = $round->data->round_end_clients['value'];
if(isset($round->data->round_end_ghosts)) $dead = $round->data->round_end_ghosts['value'];
if(isset($round->data->survived_total)) $survivors = $round->data->survived_total['value'];
if(isset($round->data->survived_human)) $survivingHumans = $round->data->survived_human['value'];
if(isset($round->data->escaped_total)) $escaped = $round->data->escaped_total['value'];
if(isset($round->data->escaped_human)) $escapedH = $round->data->escaped_human['value'];

$escaped = $survivors - $escaped;
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

<hr>