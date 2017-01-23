<?php require_once('../config.php');

PHP_Timer::start();
header('Content-Type: application/json');
if(!isset($_GET['icon'])) {
  die(json_encode(array('msg'=>"No icon file specified",'failed'=>TRUE)));
}

$icon = $_GET['icon'];
if(!is_file("../".$icon)) {
  die(json_encode(array('msg'=>"Invalid icon file: $icon",'failed'=>TRUE)));
}

$png = new PNGMetadataExtractor();
$image = $png->loadImage("../".$icon);

if (!is_dir("../".GENERATED_ICONS)){
  mkdir("../".GENERATED_ICONS);
}

$dirname = explode('/', $_GET['icon']);
$dirname = end($dirname);
$dirname = str_replace('.dmi','', $dirname);
if (!is_dir("../".GENERATED_ICONS."/$dirname")){
  mkdir("../".GENERATED_ICONS."/$dirname");
}

$i = 0;
foreach($image as $icon) {
  $fileprefix = $icon['state'];
  if(isset($icon['dir'])){
    foreach ($icon['dir'] as $dir => $img){
      $i++;
      $file = fopen("../".GENERATED_ICONS."/$dirname/$fileprefix-$dir.png",'w+');
      fwrite($file, base64_decode($img));
      fclose($file);
    }
  } else {
    $file = fopen("../".GENERATED_ICONS."/$dirname/$fileprefix-$dir.png",'w+');
    fwrite($file, base64_decode($img));
    fclose($file);
  }
}

$dir = new DirectoryIterator("../".GENERATED_ICONS."/$dirname");
foreach ($dir as $fileinfo) {
  if (!$fileinfo->isDot()) continue;
  $files = array_diff(scandir("../".GENERATED_ICONS."/$dirname"), array(
    '..',
    '.',
    "$dirname.json"
  ));
  $files = array_values($files);
  foreach($files as &$file){
    $file = str_replace('.png', '', $file);
    $file = str_replace('-0', '', $file);
    $file = str_replace('-1', '', $file);
    $file = str_replace('-2', '', $file);
    $file = str_replace('-3', '', $file);
  }
  $files = array_unique($files);
  $files = array_values($files);
  $files = json_encode($files);
  $jsonFile = fopen("../".GENERATED_ICONS."/$dirname/$dirname.json",'w+');
  fwrite($jsonFile, $files);
  fclose($jsonFile);
}

echo json_encode(array('msg'=>"Generated $i icons (".PHP_Timer::resourceUsage().")",'failed'=>FALSE));

