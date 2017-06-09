<?php
require_once("../../config.php");
if (isset($_POST['text'])) {
  $text = filter_input(INPUT_POST, 'text', FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH);
}
$Parsedown = new safeDown();
echo $Parsedown->text($text);