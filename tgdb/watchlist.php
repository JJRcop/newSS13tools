<?php require_once("../header.php");?>

<?php require_once('tgdb_nav.php');?>

<?php $message = new message();
$messages = $message->activeWatchList();?>

<div class="page-header">
  <h1>Active Watchlist Entries</h1>
</div>

<?php foreach ($messages as $message):?>

  <div class="panel panel-<?php echo $message->class;?>">
    <div class="panel-heading">
      <h3 class="panel-title">
        <?php echo $message->icon;?> <?php echo $message->type;?> for
        <?php echo $message->ckeylink;?> by
        <?php echo $message->adminckey;?>, <?php echo $message->timeStamp;?>
        <p class="pull-right"><?php echo $message->permalink;?></p>
      </h3>
    </div>
    <div class="panel-body">
     <p><?php echo $message->text;?></p>
    </div>
    <div class="panel-footer">
      <small><?php echo $message->privacy;?></small>
    </div>
  </div>

<?php endforeach;?>

<?php require_once('../footer.php');?>