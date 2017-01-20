<?php require_once('../header.php');?>

<div class="page-header">
<h1>Generate Icon Metadata</h1>
</div>

<?php

$dir = new DirectoryIterator("../".GENERATED_ICONS);
foreach ($dir as $fileinfo) {
  if (!$fileinfo->isDot()) {
    if ('.DS_Store' == $fileinfo->getFilename()) continue;
    var_dump($fileinfo->getFilename());
    $files = array_diff(scandir("../".GENERATED_ICONS."/".$fileinfo->getFilename()), array('..', '.','.DS_Store','.json'));
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
    var_dump($files);
    $files = json_encode($files);
    $jsonFile = fopen("../".GENERATED_ICONS."/".$fileinfo->getFilename()."/".$fileinfo->getFilename().".json",'w+');
    fwrite($jsonFile, $files);
    fclose($jsonFile);
  }
  
}

?>

<?php require_once('../footer.php');?>
