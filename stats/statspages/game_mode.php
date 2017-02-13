<?php
$mode = false;
if ($round->game_mode) {
  switch ($round->game_mode){
    case 'revolution':
      $mode = icon('hand-paper-o')." The game mode was Revolution";
    break;

    case 'traitor+changeling':
      $mode = iconStack('user','crosshairs', 'text-danger')." The game mode was Traitor+Changeling";
    break;

    case 'traitor':
      $mode = icon('address-card-o')."The game mode was Traitor";
    break;

    case 'double agents':
      $mode = icon('user-secret')."The game mode was Double agents";
    break;

    case 'nuclear emergency':
      $mode = icon('bomb')."The game mode was Nuclear emergency";
    break;

    case 'wizard':
      $mode = icon('magic')."The game mode was Wizard";
    break;

    case 'abduction':
      $mode = icon('hand-spock-o')."The game mode was Abduction";
    break;

    case 'blob':
      $mode = icon('cloud')."The game mode was Blob";
    break;

    case 'extended':
      $mode = icon('bed')."The game mode was Extended";
    break;

    case 'secret extended':
      $mode = iconStack('bed','question')."The game mode was Secret Extended";
    break;

    case 'clockwork cult':
      $mode = icon('users')."The game mode was Clockwork Cult";
    break;

    case 'cult':
      $mode = icon('universal-access')."The game mode was Cult";
    break;

    case 'meteor':
      $mode = icon('shower')."The game mode was Meteor";
    break;

    case 'gang war':
      $mode = icon('bullhorn')."The game mode was Gang war";
    break;

    default:
      $mode = ucfirst($round->game_mode);
    break;
  }
}
?>
<?php if(isset($round->data->round_end_result) && is_array($round->data->round_end_result['details'])){
  reset($round->data->round_end_result['details']);
  $round->data->round_end_result['details'] = key($round->data->round_end_result['details']);
  }
  $result = false;
  if(isset($round->data->round_end_result)){
    switch ($round->data->round_end_result['details']){
      case 'win - heads killed':
        $result = icon('hand-grab-o')." The round ended with: Win - Heads Killed";
      break;

      case 'loss - rev heads killed':
        $result = icon('gavel')." The round ended with: Loss - Rev Heads Killed";
      break;

      case 'loss - evacuation - disk secured - syndi team dead':
        $result = icon('save text-success')." The round ended with: Loss - evacuation - disk secured - syndi team dead";
      break;

      case 'halfwin - detonation averted':
        $result = iconStack('certificate','ban','text-danger')." The round ended with: Halfwin - detonation averted";
      break;

      case 'win - syndicate nuke':
        $result = iconStack('certificate','check','fa-inverse', TRUE)." The round ended with: Win - syndicate nuke";
      break;

      case 'halfwin - syndicate nuke - did not evacuate in time':
        $result = icon('star-half-o')." The round ended with: Halfwin - syndicate nuke - did not evacuate in time";
      break;

      case 'win - blob took over':
        $result = icon('line-chart')." The round ended with: Win - blob took over";
      break;

      case 'loss - blob eliminated':
        $result = icon('smile-o')." The round ended with: Loss - blob eliminated";
      break;

      case 'loss - servants failed their objective (escape)':
        $result = icon('frown-o')." The round ended with: Loss - servants failed their objective (escape)";
      break;

      case 'win - servants completed their objective (escape)':
        $result = icon('chain-broken')." The round ended with: Win - servants completed their objective (escape)";
      break;

      case 'loss - staff stopped the cult':
        $result = icon('frown-o')." The round ended with: Loss - staff stopped the cult";
      break;

      case 'win - cult win':
        $result = icon('tint text-danger')." The round ended with: Win - cult win";
      break;

      case 'loss - servants failed their objective (gateway)':
        $result = icon('power-off')." The round ended with: loss - servants failed their objective (gateway)";
      break;

      case 'win - servants completed their objective (gateway)':
        $result = icon('superpowers')." The round ended with: Win - servants completed their objective (gateway)";
      break;

      case 'end - evacuation':
        $result = icon('rocket')." The round ended with: End - evacuation";
      break;

      default:
        $result = "The round ended with: ".$round->data->round_end_result['details'];
      break;
    }
  } else {
    if ($round->status == 'nuke'){
      $result = iconStack('certificate','check','fa-inverse', TRUE)." The round ended with: Nuke";
    }
  }
  if (isset($round->data->emergency_shuttle)) {
    $shuttle = icon('space-shuttle')." <em>".str_replace('_', ' ', $round->data->emergency_shuttle['details'])."</em>";
  } else {
    $shuttle = iconStack('space-shuttle','ban','text-danger')." The round ended without a crew evacuation.";
  }
  $error = false;
  if (isset($round->data->end_error)){
    $error = icon('server')." ".ucfirst($round->data->end_error['details']);
  }

  $rare = FALSE;
  if ($round->game_mode != 'nuclear emergency' && $round->status == 'nuke'){
    $rare = iconStack('certificate','trophy','fa-inverse', TRUE)." <strong>RARE ENDING</strong>";
  }

  if(!$round->duration) {
    $round->duration = '<em>Unknown</em>';
  }
  ?>
<ul class="list-unstyled">
<li><?php echo $mode;?></li>
<li><?php echo $result;?></li>
<li><?php echo icon('clock-o')." It lasted $round->duration";?></li>
<li><?php echo $shuttle;?></li>
<li><?php echo $error;?></li>
<?php if($rare) echo "<li>$rare</li>";?>
</ul>

