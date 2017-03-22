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

<?php
$players = $user->getPlayerList($filterby, $filter);?>

<div class="page-header">
  <h1>Players <small>
  <?php if ($filter):?>
    <i class="fa fa-filter"></i> <?php echo $filterby;?>: <?php echo $filter;?>
      <a href="players.php">Clear filter</a>
  <?php else:?>
    Showing online
  <?php endif;?>
  </small>
  </h1>
</div>

<table class="table table-bordered table-condensed" style="table-layout: fixed;">
  <thead>
    <tr>
      <th>ckey</th>
      <th>IP</th>
      <th>CID</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($players as $p):?>
    <tr style="background: <?php echo $p->backColor;?>; color: <?php echo $p->foreColor;?>;">
      <td><?php echo $p->link;?></td>
      <td class="ipaddr"><?php echo $p->ip;?><?php echo $p->ipql;?></td>
      <td class="cid"><?php echo $p->computerid;?><?php echo $p->cidql;?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<?php require_once('../footer.php');?>