<?php require_once('../header.php');?>
<?php require_once('tgdb_nav.php');?>

<?php
$ban = new ban();
$bans = $ban->adminBanCounts(); ?>
<div class="page-header">
<h1>Number of bans, by type, by admin</h1>
</div>
<table class="table table-bordered table-condensed">
  <thead>
    <tr>
      <th>Count</th>
      <th>Ban Type</th>
      <th>Admin</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($bans as $ban) :?>
    <tr>
      <td><?php echo $ban->bans;?></td>
      <td><?php echo $ban->bantype;?></td>
      <td><?php echo $ban->admin;?></td>
    </tr>
  <?php endforeach;?>
  </tbody>
</table>