<?php

//Game mode determinations
$gamdemode = false;
if (isset($round->data->round_end_result)) {
  if (isset($round->data->game_mode)){
    //We have a game mode!
    $gamemode = ucfirst(str_replace('_', ' ',$round->data->game_mode['details']));
    
  } else {
    //We don't have a game mode, so we'll have to figure it out on our own
    
  }
} else {
  //We don't have a round end result, so we'll check for any objectives
  
}

//Duration
$duration = false;
if (isset($round->data->round_start)) {
  $start = strtotime($round->data->round_start['details']);
  $end = strtotime($round->data->round_end['details']);
  //Set our duration line
  $duration = " lasted ".round(abs($start - $end) / 60). " minutes and ";
}

//Shuttle
$shuttle = false;
if (isset($round->data->emergency_shuttle)) {
  $shuttle = "The crew evacuated aboard <em>".str_replace('_', ' ', $round->data->emergency_shuttle['details'])."</em></p>";
}

//Server
//Parsed on the round() constructor (dont judge me)
$server = false;
if (isset($round->server)) {echo " on $round->server ";}