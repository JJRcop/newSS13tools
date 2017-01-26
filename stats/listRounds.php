<?php require_once('../header.php'); ?>

<div class="page-header">
  <h1>Rounds</h1>
</div>

<table class="table table-bordered table-condensed">
  <thead>
    <tr>
      <th>Round ID</th>
      <th>Round ended</th>
      <th>Mode</th>
      <th>Server</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    $rounds = new round();
    foreach($rounds->listRounds() as $round){
      echo "<tr>";
      echo "<td><a href='viewRound.php?round=$round->round_id'>$round->round_id</a></td>";
      echo "<td>".date('r',strtotime($round->end))."</td>";
      echo "<td>".ucfirst($round->game_mode)."</td>";
      echo "<td>".$rounds->mapServer($round->server)."</td>";
      echo "</tr>";
    }
    ?>
  </tbody>
</table>

<?php require_once('../footer.php'); ?>