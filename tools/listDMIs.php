<?php require_once('../header.php'); ?>
<?php echo alert('## CLASSIFIED ## GA+//NA</strong> This page is classified. This page should not shared with non-admins.');?>


<div class="page-header"><h1>DMI files</h1></div>

<?php if (!file_exists(DMEDIR)) die("Can't find ".DMEDIR.". Did you check it out?"); ?>

<?php
$fileinfos = new RecursiveIteratorIterator(
  new RecursiveDirectoryIterator(DMEDIR."/icons")
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