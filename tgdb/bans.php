<?php require_once("../header.php");?>

<?php require_once('tgdb_nav.php');?>

<?php $bans = new ban();
$bans = $bans->getBanList();?>

<div class="page-header">
  <h1>Ban Database</h1>
</div>

<?php foreach ($bans as $ban):?>
  <?php include('banData.php');?>
<?php endforeach;?>

<?php require_once('../footer.php');?>