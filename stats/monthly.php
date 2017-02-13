<?php require_once('../header.php');?>
<?php

$db = new database();
    if($db->abort){
      return FALSE;
    }
$db->query("SELECT * FROM tbl_feedback WHERE `time` BETWEEN '2016-12-01' AND '2016-12-31';");
$db->execute();
$rounds = $db->resultset();

$data = [];

foreach ($rounds as $round){
  if('' == $round->var_name) continue;
  if(array_key_exists($round->var_name, $data)){
    $data[$round->var_name]['value']+= $round->var_value;
    $data[$round->var_name]['details'].= " ".$round->details;
  } else {
    $data[$round->var_name]['var_name'] = $round->var_name;
    $data[$round->var_name]['value'] = $round->var_value;
    $data[$round->var_name]['details'] = $round->details;
  }
}

$data = (object) $data;
$round = new round();
$data = $round->parseRoundFeedback($data);
var_dump($data->engine_started);