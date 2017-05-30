<?php require_once('../header.php') ;?>

<?php $activity = $user->doAdminsPlay();?>
<div class="page-header">
  <h1>Admin Connection Activity</h1>
</div>

<p class="lead">This page shows admin connection logs for the last thirty days.</p>

<table class="table sticky  table-condensed table-bordered sort">
  <thead>
    <tr>
      <th>ckey</th>
      <th>Rank</th>
      <th>Connections</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($activity as $a):?>
    <?php if('Player' == $a->lastadminrank) continue;?>
    <tr>
      <td><?php echo $a->label;?></td>
      <td><?php echo $a->lastadminrank;?></td>
      <td><?php echo $a->connections;?></td>
    </tr>
  <?php endforeach;?>
  </tbody>
</table>

<?php require_once('../footer.php') ;?>