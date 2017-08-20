<?php 
require_once('../header.php'); 
$map = new map();

$render = null;
$render = filter_input(INPUT_GET, 'render',FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_HIGH);
if($render){
  $map->render($render);
  // var_dump($map);
  echo "<img src='".APP_URL."/tmp/maps/$map->map_file.png' />";
  return;
} 
?>

<div class="page-header">
  <h1>Maps</h1>
</div>

<?php
$maps = $map->getAvailableMaps();
foreach($maps as $n => $m):?>
<?php var_dump($m);?>
<div class="media">
  <div class="media-left">
      <img class="media-object" src="<?php echo $m->minimap;?>" width="64" height="64">
  </div>
  <div class="media-body">
    <h4 class="media-heading"><?php echo $m->map_name;?><br>
    <small>
    <?php if($m->rendered):?>
      <span class="label label-success"><i class="fa fa-check fa-fw"></i>
      Rendered</span>
      <a href="#" class="btn btn-xs btn-info">Re-Render</a>
    <?php else:?>
      <span class="label label-danger"><i class="fa fa-times fa-fw"></i>
      Not Rendered</span><br>
      <a href="?render=<?php echo $n;?>" class="btn btn-xs btn-success">Render</a>
    <?php endif;?>
    </small>
    </h4>
  </div>
</div>
<?php endforeach;?>

<?php require_once('../footer.php'); ?>