<?php require_once('header.php'); ?>

<div class="page-header">
  <h1>DMI List</h1>
</div>

<div class="row">
  <div class="col-md-4">
    <?php
    $rootpath = ICONS_DIR;
    if (!file_exists(ICONS_DIR)) die("Can't find tgstation/icons. Did you check it out?");
    $fileinfos = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($rootpath)
    );
    foreach($fileinfos as $pathname => $fileinfo) {
      if (!$fileinfo->isFile()) continue;
      if (str_contains('.png',$pathname)) continue;
      echo "<a href='renderDMI.php?icon=$pathname'>$pathname</a><br>";
    } ?>
  </div>

  <div class="col-md-8">
  <?php
  if (isset($_GET['icon'])){
    echo "<p><strong>Rendering icons from:</strong> ".$_GET['icon']."</p>"; ?>
    <table class="table table-condensed table-bordered">
      <thead>
        <tr>
          <th>Name</th>
          <th>Icon</th>
        </tr>
      </thead>
      <tbody>
    <?php
    $png = new PNGMetadataExtractor();
    $image = $png->loadImage($_GET['icon']);
    foreach($image as $icon) {
      echo "<tr><td><pre>".$icon['state']."</pre></td><td class='icon'>";
      if (isset($icon['dir'])){
        foreach ($icon['dir'] as $dir) {
          echo "<img src='data:image/png;base64,".$dir."'/>";
        }
      } else {
        echo "<img src='data:image/png;base64,".$icon['base64']."'/>";
      }
      echo "</td></tr>";
    } ?>
      </tbody>
    </table>
    <a href="generatePNGs.php?icon=<?php echo $_GET['icon'];?>" class="btn btn-success">Split this DMI into PNGs</a>
    <?php } ?>
  </div>
</div>
<?php require_once('footer.php'); ?>