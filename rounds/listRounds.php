<?php
$round = new round();
$total = $round->countRounds();
$pages = floor($total/30);

if (isset($_GET['page'])){
  $page = filter_input(INPUT_GET, 'page',FILTER_VALIDATE_INT,array(
    'min_range' => 1,
    'max_range' => $pages
  ));
} else {
  $page = 1;
}
$rounds = $round->listRounds($page);
?>

<div class="page-header">
  <h1><small class="pull-right">Between <?php echo $rounds[0]->start;?> and <?php echo end($rounds)->end;?></small>
  Rounds</h1>
</div>
<?php $link = "round"; include(ROOTPATH."/inc/view/pagination.php");?>
<table class="table sticky  table-bordered table-condensed">
  <thead>
    <tr>
      <th>Round ID</th>
      <th>Mode</th>
      <th>Map</th>
      <th>Status</th>
      <th>Duration</th>
      <th>Start</th>
      <th>End</th>
      <th>Server</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    if (!$rounds) {
      echo "<tr><td colspan='7'>No rounds found</td></tr>";
    } else{
      foreach($rounds as $round): ?>
        <tr class="<?php echo $round->statusClass;?>">
          <td><?php echo $round->link;?></td>
          <td><?php echo $round->modeIcon.ucfirst($round->game_mode);?></td>
          <td><?php echo str_replace('_',' ',$round->map);?></td>
          <td><?php echo "$round->statusIcon $round->result";?></td>
          <td><?php echo $round->duration;?></td>
          <td><?php echo $round->start;?></td>
          <td><?php echo $round->end;?></td>
          <td><?php echo $round->server;?></td>
      <?php endforeach;
    }
    ?>
  </tbody>
</table>

<?php $link = "round"; include(ROOTPATH."/inc/view/pagination.php");?>



