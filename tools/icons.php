<?php require_once('../header.php'); ?>

<?php if(isset($_GET['view'])){ ?>
<?php $icon = filter_input(INPUT_GET, 'view', FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_HIGH);
  $icon = new icon($icon,array('view'));
  $render = false;
  $render = filter_input(INPUT_GET, 'render', FILTER_VALIDATE_BOOLEAN);
  if($render){
    $icon->renderIcon();
    echo parseReturn($icon->message);
  } 
?>
<div class="page-header">
  <h1>
  <a href="<?php echo $app->APP_URL;?>tools/icons.php" class="btn btn-info btn-xs">
    <i class="fa fa-arrow-left"></i> Back
  </a>
  Viewing
  <code><?php echo $icon->prettyFile;?></code><br>
  </h1>
</div>
<p>
  <input type='text' class='form-control' name='' id='bg' value="#FFFFFF">
  <a class="btn btn-info btn-xs" href="<?php echo $app->APP_URL."tools/icons.php?view=$icon->file&render=true";?>">
    <i class="fa fa-download fa-fw" data-toggle="tooltip" title="Render"></i> Render
  </a>
</p>
<div class="row">
<?php
$c = 1; foreach ($icon->icons as $i):
  include(ROOTPATH."/inc/view/icon.php");
   if($c % 6 === 0):
    echo "</div><div class='row'>";
  endif;
  $c++;
endforeach;
?>
</div>
<?php } else {
  $subdir = null;
  if(isset($_GET['subdir'])){
    $subdir = filter_input(INPUT_GET, 'subdir', FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_HIGH);
  }
  $icon = new icon();
  $icons = $icon->browseIcons($subdir);
  
  ?>

  <div class="page-header">
  <h1>Listing Icon Files</h1>
  </div>
  <ul class="list-unstyled">
  <?php foreach ($icons as $i):?>
    <li>
      <a href="<?php echo $app->APP_URL."tools/icons.php?view=$i";?>">
        <i class="fa fa-eye fa-fw" data-toggle="tooltip" title="View"></i>
      </a>

      <a href="<?php echo $app->APP_URL."tools/icons.php?view=$i&render=true";?>">
        <i class="fa fa-download fa-fw" data-toggle="tooltip" title="Render"></i>
      </a>

      <?php echo str_replace(ICONS_DIR, '', $i);?>
    </li>
  <?php endforeach;?>
  </ul>
<?php

}?>
<script src='../resources/js/spectrum.js'></script>
<link rel='stylesheet' href='../resources/css/spectrum.css' />
<script>
$('#bg').spectrum({
    showInput: true,
    allowEmpty:true,
    showPaletteOnly: true,
    palette: [
        ["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
        ["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
        ["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
        ["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
        ["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
        ["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
        ["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
        ["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
    ],
    change: function(color) {
      $('.icon-thumb img').css('background-color',color);
    },
    preferredFormat: 'hex'
});
</script>

<?php require_once('../footer.php'); ?>