<?php $skip = true; require_once("../header.php");?>
<?php $user = new user();?>
<?php var_dump($user);?>
<?php if($user->rank == 'Player') die();?>

<?php 

$db = new database();
$db->query("SELECT
sum(IF (lakey = $user->byond, 1,0)) AS kills,
sum(IF (byondkey = $user->byond,1,0)) AS deaths
FROM ss13death;");
$db->execute();
$ratio = $db->single();
var_dump($ratio);
?>

<code>
Deaths: <?php echo $ratio->deaths;?>
Kills: <?php echo $ratio->kills;?>
Ratio: <?php echo ($ratio->deaths/$ratio->kills);?>
</code>

<?php require_once("../footer.php");?>