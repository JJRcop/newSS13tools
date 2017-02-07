<?php require_once('../header.php');?>

<?php
$death = new death();
$death = $death->getDeathMap(10000);

$dim = 512;

//Create deathmap
$dm = imagecreate(256, 256);
imagesavealpha($dm, TRUE);
$alpha = imagecolorallocatealpha($dm, 0, 0, 0, 127);
imagefilledrectangle($dm, 0, 0, 256, 256, $alpha);

//Copy background
$bg = imagecreatefrompng(DMEDIR."/icons/minimaps/Box Station_1.png");
imagesavealpha($bg, TRUE);
$screen = imagecolorallocatealpha($bg, 0, 0, 0, 10);
imagefilledrectangle($bg, 0, 0, 2048, 2048, $screen);
imagecopyresized($dm, $bg, 0, 0, 0, 0, 256, 256, 2048, 2048);
imagedestroy($bg);

foreach ($death as $d) {
  $c = explode(', ',$d->coord);
  if (1 != $c[2]) continue;
  $x = $c[0]-2;
  $y = abs(256-$c[1]);
  $o = floor((255/$death[0]->number) * $d->number);
  $c = imagecolorallocate($dm, $o, 0, 0);
  imagesetpixel($dm, $x, $y, $c);
  imagecolordeallocate($dm, $c);
}

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