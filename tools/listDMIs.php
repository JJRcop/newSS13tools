<?php require_once('../header.php'); ?>

<div class="page-header"><h1>DMI files</h1></div>

<?php if (!file_exists("../".ICONS_DIR)) die("Can't find tgstation/icons. Did you check it out?"); ?>

<?php
$fileinfos = new RecursiveIteratorIterator(
  new RecursiveDirectoryIterator("../".ICONS_DIR)
);?> 

<ul class="list-unstyled">

<?php 
foreach($fileinfos as $pathname => $fileinfo) {
  if (!$fileinfo->isFile()) continue;
  if (strpos($pathname,'.png')) continue;
  echo "<li><a href='viewDMI.php?dmi=$pathname'>$pathname</a></li>";
}
?>
</ul>

<?php require_once('../footer.php'); ?>