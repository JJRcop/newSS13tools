<?php require_once("../header.php");?>

<?php require_once('tgdb_nav.php');?>

<?php
$filter = null;
$filterby = null;
if (isset($_GET['ip'])){
  $filterby = 'ip';
  $filter = filter_input(INPUT_GET, 'ip',FILTER_VALIDATE_IP);
}

if (isset($_GET['cid'])){
  $filterby = 'cid';
  $filter = filter_input(INPUT_GET, 'cid',FILTER_VALIDATE_INT);
}
?>

<?php $bans = new ban();
$bans = $bans->getBanList(0, 100, $filterby, $filter);?>

<div class="page-header">
  <h1>Ban Database <small>
  <?php if ($filter):?>
    <i class="fa fa-filter"></i> <?php echo $filterby;?>: <?php echo $filter;?>
      <a href="bans.php">Clear filter</a>
  <?php else:?>
    Showing most recent
  <?php endif;?>
  </small>
  </h1>
</div>
<?php if(!$filter) $hideInactive = TRUE;?>
<?php if ($bans):?>
<?php foreach ($bans as $ban):?>
  <?php include('banData.php');?>
<?php endforeach;?>
<?php elseif ($filter):?>
  <p class="pull-center text-muted">No bans for <?php echo $filter;?></p>
<?php else:?>
  <p class="pull-center text-muted">No bans to show</p>
<?php endif;?>

<?php require_once('../footer.php');?>