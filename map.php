<?php require_once('header.php'); ?>

<?php
  $map = new map();
  $map->parseMap(DMEDIR.'/_maps/map_files/BoxStation/BoxStation.dmm');
  var_dump($map->defLength); 
?>


<?php require_once('footer.php');