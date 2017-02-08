<?php
if ($round->game_mode) {
  $round->game_mode = ucfirst($round->game_mode);
} else {
  $round->game_mode = 'Unknown';
}
?>
<?php if(isset($round->data->round_end_result) && is_array($round->data->round_end_result['details'])){
  reset($round->data->round_end_result['details']);
  $round->data->round_end_result['details'] = key($round->data->round_end_result['details']);
  }?>
<p class="lead">
A round of <?php echo "$round->game_mode took place";?><?php if ($round->server) echo " on $round->server";?><?php if ($round->duration && $round->server) echo " and ";?><?php if ($round->duration) echo "lasted $round->duration";?>. <?php if (isset($round->data->round_end_result)) echo "The result was: ".ucfirst($round->data->round_end_result['details']);?><?php if($round->hasObjectives) echo " This resulted in the following:";?>
</p>