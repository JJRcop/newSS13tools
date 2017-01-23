<?php require_once('../header.php'); ?>

<div class="page-header">
  <h1>Rounds</h1>
</div>

<table class="table table-bordered table-condensed">
  <thead>
    <tr>
      <th>Round ID</th>
      <th>Round ended</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    $round = new round();
    foreach($round->listRounds() as $round){
      echo "<tr><td><a href='viewRound.php?round=$round->round_id'>$round->round_id</a></td><td>".date('r',strtotime($round->roundend))."</td></tr>";
    }
    ?>
  </tbody>
</table>

<?php require_once('../footer.php'); ?>