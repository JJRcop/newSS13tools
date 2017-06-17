<?php $skip = true;
$wide = true;
require_once('header.php');
$app = new app();?>
<style>
.round {
  display: block;
}
.round:hover {
  border: 1px solid white;
}
</style>
<?php

$db = new database();
$db->query("SELECT ss13round.game_mode, ss13round.id FROM ss13round
ORDER BY ss13round.start_datetime DESC;");
try {
  $rounds = $db->resultSet();
} catch (Exception $e) {
  return returnError("Database error: ".$e->getMessage());
}
?>

<?php foreach($rounds as $r):
$color = substr(hash('sha256',$r->game_mode),0,6);
?>
<a class='round' style="background-color: #<?php echo $color;?>; height: 10px;" data-toggle="tooltip" title="<?php echo $r->game_mode;?>" href="<?php echo $app->APP_URL;?>/round.php?round=<?php echo $r->id;?>" target="_blank">
</a>
<?php endforeach;?>

<?php require_once('footer.php');
