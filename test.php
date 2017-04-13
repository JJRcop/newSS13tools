<?php
require_once('header.php');
$app = new app();

$test = function() use(&$app){
  return $this->downloadAdminsTxt();
};

var_dump($test);