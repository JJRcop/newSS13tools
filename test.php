<?php $skip = true;
$wide = true;
require_once('header.php');?>

<?php
$icon = new icon();
var_dump($icon->DMIDiff("https://github.com/Expletive/tgstation/blob/7ac6d3f14d36dab81293c6c994e69a9d05d4de74/icons/obj/bureaucracy.dmi?raw=true"));
var_dump($icon->DMIDiff("https://github.com/Expletive/tgstation/blob/7ac6d3f14d36dab81293c6c994e69a9d05d4de74/icons/obj/bureaucracy.dmi"));
var_dump($icon->DMIDiff("google.com"));
?>


<?php require_once('footer.php');