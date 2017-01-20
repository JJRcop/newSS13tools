<?php require_once('header.php'); ?>

<div class="page-header">
<h1>Information and status</h1>
</div>

<table class="table table-condensed table-bordered">
  <thead>
    <tr>
      <th>Module</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>PHP Version</td>
      <td><?php echo PHP_VERSION;?></td>
    </tr>
  </tbody>
</table>

<?php require_once('footer.php');