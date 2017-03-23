<?php require_once('../header.php') ;?>

<?php
$activity = $app->doAdminsPlay();
var_dump($activity);?>
<div class="page-header">
  <h1>Admin Connection Activity</h1>
</div>

<table class="table table-condensed table-bordered">
  <thead>
    <tr>
      <th>ckey</th>
      <th>Rank</th>
      <th>Connections</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($activity as $a):?>
    <tr>
      <td><?php echo $a->label;?></td>
      <td><?php echo $a->lastadminrank;?></td>
      <td><?php echo $a->connections;?></td>
    </tr>
  <?php endforeach;?>
  </tbody>
</table>

<?php require_once('../footer.php') ;?>