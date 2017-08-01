<?php require_once("../header.php");?>

<?php require_once('tgdb_nav.php');?>

<?php
$players = $user->getNewCkeys();?>

<div class="page-header">
  <h1>New Ckeys in the last 48 hours</h1>
</div>

<table class="table sticky  table-bordered table-condensed" style="table-layout: fixed;">
  <thead>
    <tr>
      <th>ckey</th>
      <th>IP</th>
      <th>CID</th>
      <th>Recent CIDs</th>
      <th>Last CID</th>
      <th>Recent IPs</th>
      <th>Last IP</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($players as $p):?>
    <tr style="background: <?php echo $p->backColor;?>; color: <?php echo $p->foreColor;?>;">
      <td><?php echo $p->link;?></td>
      <td class="ipaddr"><?php echo $p->ip;?><?php echo $p->ipql;?></td>
      <td class="cid"><?php echo $p->computerid;?><?php echo $p->cidql;?></td>
      <td><?php echo str_replace(',', ', ', $p->cid_recent_connection_matches);?></td>
      <td><?php echo str_replace(',', ', ', $p->cid_last_connection_matches);?></td>
      <td><?php echo str_replace(',', ', ', $p->ip_recent_connection_matches);?></td>
      <td><?php echo str_replace(',', ', ', $p->ip_list_connection_matches);?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<?php require_once('../footer.php');?>