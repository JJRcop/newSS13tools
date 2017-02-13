<?php

function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}
function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

function str_contains($haystack, $needle) {
  return strpos($haystack, $needle) !== false;
}

//Adapted from https://gist.github.com/Jadzia626/2323023
function HSL2RGB($iH, $iS, $iV) {
  if($iH < 0)   $iH = 0;   // Hue:
  if($iH > 360) $iH = 360; //   0-360
  if($iS < 0)   $iS = 0;   // Saturation:
  if($iS > 100) $iS = 100; //   0-100
  if($iV < 0)   $iV = 0;   // Lightness:
  if($iV > 100) $iV = 100; //   0-100
  $dS = $iS/100.0; // Saturation: 0.0-1.0
  $dV = $iV/100.0; // Lightness:  0.0-1.0
  $dC = $dV*$dS;   // Chroma:     0.0-1.0
  $dH = $iH/60.0;  // H-Prime:    0.0-6.0
  $dT = $dH;       // Temp variable
  while($dT >= 2.0) $dT -= 2.0; // php modulus does not work with float
  $dX = $dC*(1-abs($dT-1));     // as used in the Wikipedia link
  switch(floor($dH)) {
      case 0:
          $dR = $dC; $dG = $dX; $dB = 0.0; break;
      case 1:
          $dR = $dX; $dG = $dC; $dB = 0.0; break;
      case 2:
          $dR = 0.0; $dG = $dC; $dB = $dX; break;
      case 3:
          $dR = 0.0; $dG = $dX; $dB = $dC; break;
      case 4:
          $dR = $dX; $dG = 0.0; $dB = $dC; break;
      case 5:
          $dR = $dC; $dG = 0.0; $dB = $dX; break;
      default:
          $dR = 0.0; $dG = 0.0; $dB = 0.0; break;
  }
  $dM  = $dV - $dC;
  $dR += $dM; $dG += $dM; $dB += $dM;
  $dR *= 255; $dG *= 255; $dB *= 255;
  $return['R'] = round($dR);
  $return['G'] = round($dG);
  $return['B'] = round($dB);
  return $return;
}

function iconStack($icon, $top, $class=null,$flip=false){
  if ($flip){
      return "<span class='fa-stack fa-lg'>
      <i class='fa fa-$icon fa-stack-2x'></i>
      <i class='fa fa-$top fa-stack-1x $class'></i>
    </span>";
  }
  return "<span class='fa-stack fa-lg'>
  <i class='fa fa-$icon fa-stack-1x'></i>
  <i class='fa fa-$top fa-stack-2x $class'></i>
</span>";
}

function icon($icon, $class=null){
  return iconStack($icon,'circle-thin',$class);
}