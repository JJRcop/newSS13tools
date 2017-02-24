<?php

$map = 'Box';
if(isset($_GET['map'])) $map = filter_input(INPUT_GET, 'map', FILTER_SANITIZE_STRING, array(FILTER_FLAG_STRIP_HIGH));

require_once('../header.php');?>

<div class="page-header">
<h1>Show me deaths from...</h1>
</div>

<ul class="nav nav-tabs nav-justified">
  <li><a href="deathmap.php?map=Box">Box</a></li>
  <li><a href="deathmap.php?map=Meta">Meta</a></li>
  <li><a href="deathmap.php?map=Delta">Delta</a></li>
  <li><a href="deathmap.php?map=Omega">Omega</a></li>
</ul>


<?php
$death = new death();
$death = $death->getDeathsFromMap($map);
if (!$death) die("<p class='lead'>No deaths tracked for this map yet</p>");
$dim = 512;

$o = ceil((100/count($death))*127);

//Create deathmap
$dm = imagecreate(256, 256);
imagesavealpha($dm, TRUE);
$alpha = imagecolorallocatealpha($dm, 0, 0, 0, 127);
imagefilledrectangle($dm, 0, 0, 256, 256, $alpha);

//Copy background
switch($map){
  case 'Box':
  default:
    $bg = imagecreatefrompng(DMEDIR."/icons/minimaps/Box Station_1.png");
  break;

  case 'Meta':
    $bg = imagecreatefrompng(DMEDIR."/icons/minimaps/MetaStation_1.png");
  break;

  case 'Delta':
    $bg = imagecreatefrompng(DMEDIR."/icons/minimaps/Delta Station_1.png");
  break;

  case 'Omega':
    $bg = imagecreatefrompng(DMEDIR."/icons/minimaps/OmegaStation_1.png");
  break;
}
imagesavealpha($bg, TRUE);
$screen = imagecolorallocatealpha($bg, 0, 0, 0, 10);
imagefilledrectangle($bg, 0, 0, 2048, 2048, $screen);
imagecopyresized($dm, $bg, 0, 0, 0, 0, 256, 256, 2048, 2048);
imagedestroy($bg);

//Wait.. what are you-
$dot = imagecreate(1, 1);
imagesavealpha($dot, TRUE);

//Oh my god
$c = imagecolorallocate($dot, 255, 255, 0);
imagesetpixel($dot, 1, 1, $c);

//NO
foreach ($death as $d) {
  $c = explode(', ',$d->coord);
  if (1 != $c[2]) continue;
  $x = $c[0]-2;
  $y = abs(256-$c[1]);
  //STOP
  imagecopymerge($dm, $dot, $x, $y, 1, 1, 1, 1, $o*2);
}
imagedestroy($dot);

//This is so wrong on so many levels, BUT, imagesetpixel() just _SETS_ the
//color of a pixel. It doesn't paint the pixel with whatever shade I want. 
//Thusly, we merge the single pixel onto the full image.

//Final image
$final = imagecreate($dim, $dim);
imagesavealpha($final, TRUE);

//Copy deathmap
imagecopyresized($final, $dm, 0, 0, 0, 0, $dim, $dim, 256, 256);
imagedestroy($dm);

//Output
ob_start();
imagepng($final);
$img = base64_encode(ob_get_contents());
imagedestroy($final);
ob_end_clean();

echo "<img src='data:image/png;base64,$img' id='deathmap'>"
?>

<?php require_once('../footer.php');?>