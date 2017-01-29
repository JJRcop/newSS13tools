<?php require_once('../header.php'); ?>

<div class="page-header">
  <h1>Rounds</h1>
</div>

<table class="table table-bordered table-condensed">
  <thead>
    <tr>
      <th>Round ID</th>
      <th>Duration</th>
      <th>Mode</th>
      <th>Server</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    $rounds = new round();
    foreach($rounds->listRounds() as $round){
      if ($round->duration){
        echo "<tr>";
      } else {
        echo "<tr class='bad-round'>";
      }
      echo "<td><a href='viewRound.php?round=$round->round_id'>$round->round_id</a></td>";
      if ($round->duration){
        echo "<td>$round->duration minutes <small>(ended at $round->end GMT)</td>";
        echo "<td>".ucfirst($round->game_mode)."</td>";
        echo "<td>".$rounds->mapServer($round->server)."</td>";
      } else {
        echo "<td colspan='3'>Something went wrong and stats for this round are incomplete.</td>";
      }
      echo "</tr>";
    }
    ?>
  </tbody>
</table>

<?php require_once('../footer.php'); ?>