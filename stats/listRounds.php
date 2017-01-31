<?php require_once('../header.php'); ?>

<?php
$rounds = new round();
$total = $rounds->countRounds();
$pages = floor($total/30);

if (isset($_GET['page'])){
  $page = filter_input(INPUT_GET, 'page',FILTER_VALIDATE_INT,array(
    'min_range' => 1,
    'max_range' => $pages
  ));
} else {
  $page = 1;
}

?>

<div class="page-header">
  <h1>Rounds</h1>
</div>

<nav aria-label="Page navigation">
  <ul class="pagination">
    <?php if ($page > 1):?>
    <li>
      <a href="listRounds.php?page=<?php echo $page-1;?>" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
      </a>
    </li>
    <?php endif;?>
    <?php 

    if ($page > 5 && $page < ($pages-5)){      
      for ($i = ($page-5); $i <= ($page+5); $i++){
        if ($page == $i){
          echo "<li class='active'>";
        } else {
          echo "<li>";
        }
        echo "<a href='listRounds.php?page=$i'>$i</a></li>";
      }
    } else if ($page <= 5) {
      for ($i = 1; $i <= 5; $i++){
        if ($page == $i){
          echo "<li class='active'>";
        } else {
          echo "<li>";
        }
        echo "<a href='listRounds.php?page=$i'>$i</a></li>";
      }
    } else {
      for ($i = ($pages-5); $i <= $pages; $i++){
        if ($page == $i){
          echo "<li class='active'>";
        } else {
          echo "<li>";
        }
        echo "<a href='listRounds.php?page=$i'>$i</a></li>";
      }
    }

    ?>
    <li>
      <?php if ($page < $pages):?>
      <li>
        <a href="listRounds.php?page=<?php echo $page+1;?>" aria-label="Next">
          <span aria-hidden="true">&raquo;</span>
        </a>
      </li>
      <?php endif;?>
    </li>
  </ul>
</nav>

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

    foreach($rounds->listRounds($page) as $round){
      if ($round->duration){
        echo "<tr>";
      } else {
        echo "<tr class='bad-round'>";
      }
      echo "<td><a href='viewRound.php?round=$round->round_id'>$round->round_id</a></td>";
      if ($round->duration){
        echo "<td>$round->duration <small>(ended at $round->end GMT)</td>";
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