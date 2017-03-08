<?php
require_once("config.php");
define('MAP_DIR',DMEDIR.'/_maps/map_files/');
define('MAPFILE',MAP_DIR.'debug/runtimestation.dmm');

$handle = fopen(MAPFILE,'r');

$listre = "/(\")(\\d{1,3})(;)([\\d]{1,3})(\")/mi";

$map = [];

if ($handle) {
  while (($line = fgets($handle)) !== false) {
    $map[] = rtrim($line);
  }
    fclose($handle);
} else {
  echo "Failed to read $file";
}

$map = array_filter($map);

$i = 0;
foreach($map as $line){
  if(strpos($line,'(1,1,1) = {"') !== FALSE){
    $mapStart = $i;
    continue 1;
  }
  $i++;
}

$map = array_chunk($map, $mapStart+1);

var_dump($map);
