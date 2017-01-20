<?php

// $body = imagecreatetruecolor(32, 32);
// imagesavealpha($body, true);
// imagealphablending($body, true);
// $alpha = imagecolorallocatealpha($body, 0, 0, 0, 127);
// imagefill($body,0,0,$alpha);

// $head = imagecreatefrompng('icons/human_parts_greyscale/human_head_m_s-0.png');
// $chest = imagecreatefrompng('icons/human_parts_greyscale/human_chest_m_s-0.png');
// $rArm = imagecreatefrompng('icons/human_parts_greyscale/human_r_arm_s-0.png');
// $lArm = imagecreatefrompng('icons/human_parts_greyscale/human_l_arm_s-0.png');
// $rLeg = imagecreatefrompng('icons/human_parts_greyscale/human_r_leg_s-0.png');
// $lLeg = imagecreatefrompng('icons/human_parts_greyscale/human_l_leg_s-0.png');

// imagecopy($body, $head,  0, 0, 0, 0, 32, 32);
// imagecopy($body, $chest, 0, 0, 0, 0, 32, 32);
// imagecopy($body, $rArm,  0, 0, 0, 0, 32, 32);
// imagecopy($body, $lArm,  0, 0, 0, 0, 32, 32);
// imagecopy($body, $rLeg,  0, 0, 0, 0, 32, 32);
// imagecopy($body, $lLeg,  0, 0, 0, 0, 32, 32);

// $skinTone = "#c79b8b";
// $skinTone = str_replace('#', '', $skinTone);
// $skinTone = str_split($skinTone,2);
// foreach($skinTone as &$c){
//   $c = 255 - hexdec($c);
// }

// imagefilter($body, IMG_FILTER_NEGATE);
// imagefilter($body, IMG_FILTER_COLORIZE, $skinTone[0], $skinTone[1], $skinTone[2], 0);
// imagefilter($body, IMG_FILTER_NEGATE);


// header('Content-Type: image/png');
// imagepng($body);

echo json_encode($humanSkinTones = array(
  'caucasian1'=>'#ffe0d1',
  'caucasian2'=>'#fcccb3',
  'caucasian3'=>'#e8b59b',
  'latino'=>'#d9ae96',
  'mediterranean'=>'#c79b8b',
  'asian1'=>'#ffdeb3',
  'asian2'=>'#e3ba84',
  'arab'=>'#c4915e',
  'indian'=>'#b87840',
  'african1'=>'#754523',
  'african2'=>'#471c18',
  'albino'=>'#fff4e6',
  'orange'=>'#ffc905',
));


?>

