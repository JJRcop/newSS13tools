<?php require_once("../header.php");?>

<?php require_once('tgdb_nav.php');?>

<?php $message = new message();
$messages = $message->getMessageList();?>

<div class="page-header">
  <h1>Message Database <small>See also: <a href="watchlist.php">Active
  Watchlist Entries</a></small></h1>
</div>

<?php foreach ($messages as $message):?>
  <?php include("messageData.php");?>
<?php endforeach;?>

<?php require_once('../footer.php');?>