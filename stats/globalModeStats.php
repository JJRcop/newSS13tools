<?php $wide = true; require_once('../header.php');?>

<?php
$round = new round();
$db = new database();
$db->query("SELECT count(id) AS rounds,
min(ss13round.start_datetime) AS earliest_start,
max(ss13round.end_datetime) AS latest_end,
min(ss13round.id) AS `first`,
max(ss13round.id) AS `last`,
floor(AVG(TIME_TO_SEC(TIMEDIFF(ss13round.end_datetime,ss13round.start_datetime)))) / 60 AS avgduration,
ss13round.game_mode,
ss13round.game_mode_result
FROM ss13round
WHERE ss13round.game_mode IS NOT NULL
GROUP BY ss13round.game_mode, ss13round.game_mode_result;");
$db->execute();?>

<table class="table sort table-bordered table-condensed">
  <thead>
    <tr>
      <th>Game Mode</th>
      <th>Result</th>
      <th># of Rounds</th>
      <th>First Round</th>
      <th>Last Round</th>
      <th>Avg. Duration</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($db->resultset() as $r):
    @$r = $round->parseRound($r);?>
      <tr class="<?php echo $r->statusClass;?>">
        <td><?php echo $r->modeIcon.ucfirst($r->game_mode);?></td>
        <td><?php echo "$r->statusIcon $r->result";?></td>
        <td><?php echo $r->rounds;?></td>
        <td><?php echo "<a href='".APP_URL."round.php?round=$r->first'>#$r->first</a>";?></td>
        <td><?php echo "<a href='".APP_URL."round.php?round=$r->last'>#$r->last</a>";?></td>
        <td><?php echo $r->avgduration;?></td>
      </tr>
    <?php endforeach;?>
  </tbody>
</table>

<?php require_once('../footer.php');