<?php
if ($round->game_mode) {
  $game_mode = ucfirst($round->game_mode);
} else {
  $game_mode = 'unknown'; //Maybe?!
  if (isset($round->data->round_end_result)){
    $result = $round->data->round_end_result['details'];
    switch($result){
      case 'loss - rev heads killed';
        $game_mode = 'Revolution';
      break;

      default:
        $game_mode = 'Unknown';
      break;
    }
  }
}
?>

<p class="lead">
A round of <?php echo "$game_mode took place";?><?php if ($round->server) echo " on $round->server";?><?php if ($round->duration && $round->server) echo " and ";?><?php if ($round->duration) echo "lasted $round->duration ";?>. <?php if (isset($round->data->round_end_result)) echo "The result was: ".ucfirst($round->data->round_end_result['details']);?><?php if($round->hasObjectives) echo " This resulted in the following:";?>
</p>