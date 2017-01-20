<?php require_once('header.php'); ?>

<div class="page-header">
  <h1>Split into PNGs</h1>
</div>

<div class="row">
  <div class="col-md-4">
  <?php if (isset($_GET['icon'])) {} ?>
  </div>
  <div class="col-md-8">
    <p><strong>Splitting icons from:</strong> <?php echo $_GET['icon'];?></p>
    <?php
    $png = new PNGMetadataExtractor();
    $image = $png->loadImage($_GET['icon']);

    $dirname = explode('/', $_GET['icon']);
    $dirname = end($dirname);
    $dirname = str_replace('.dmi','', $dirname);
    echo "Going into <code>icons/$dirname</code><br>";
    if (!is_dir("icons/$dirname")){
      mkdir("icons/$dirname");
    } else {
      if(isset($_GET['override'])) {
      } else {
        ?>
        <strong> This directory already exists. Regenerate icons anyway?</strong>
        <a href="generatePNGs.php?icon=<?php echo $_GET['icon'];?>&override=true" class="btn btn-success">Override</a>
        <?php
        die();
      }
    }
    $i = 0;
    foreach($image as $icon) {
      $fileprefix = $icon['state'];
      if(isset($icon['dir'])){
        foreach ($icon['dir'] as $dir => $img){
          $i++;
          $file = fopen("icons/$dirname/$fileprefix-$dir.png",'w+');
          fwrite($file, base64_decode($img));
          fclose($file);
        }
      } else {
        $file = fopen("icons/$dirname/$fileprefix-$dir.png",'w+');
        fwrite($file, base64_decode($img));
        fclose($file);
      }
     
    } ?>
    <p>Generated <?php echo $i;?> png files.</p>
  </div>
</div>
<?php require_once('footer.php'); ?>