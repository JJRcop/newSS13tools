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
<?php if ($pages):?>
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
<?php endif;?>

<table class="table table-bordered table-condensed">
  <thead>
    <tr>
      <th>Round ID</th>
      <th>Duration</th>
      <th>Mode</th>
      <th>Server</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    $rounds = $rounds->listRounds($page);
    if (!$rounds) {
      echo "<tr><td colspan='5'>No rounds found</td></tr>";
    } else{
      foreach($rounds as $round){
        if (!$round->duration){
          echo "<tr id='$round->round_id' class='round bad-round'>";
          $status = "<span class='glyphicon glyphicon-flag'></span>";
        } elseif ($round->status == 'nuke' || $round->status == 'nuke - unhandled ending') {
          echo "<tr id='$round->round_id' class='round warning'>";
          $status = "<span class='glyphicon glyphicon-asterisk'></span>";
        } elseif ($round->status != 'proper completion'){
          echo "<tr id='$round->round_id' class='round danger'>";
          $status = "<span class='glyphicon glyphicon-remove'></span>";
        } else {
          echo "<tr id='$round->round_id' class='round'>";
          $status = "<span class='glyphicon glyphicon-ok'></span>";
        }
        echo "<td>$status <a href='viewRound.php?round=$round->round_id'>$round->round_id</a></td>";
        if ($round->duration){
          echo "<td>$round->duration <small>(ended at $round->end GMT)</td>";
          echo "<td class='mode $round->game_mode'>".ucfirst($round->game_mode)."</td>";
          echo "<td>$round->server</td>";
        } else {
          echo "<td colspan='3'>Something went wrong and stats for this round are incomplete.</td>";
        }
        echo "<td>$round->status</td>";
        echo "</tr>";
      }
    }
    ?>
  </tbody>
</table>

<?php if ($pages):?>
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
<?php endif;?>

<script>
$('.round').click(function(e){
  round = $(this).attr('id');
  window.location.href = "viewRound.php?round="+round;
})
</script>

<?php require_once('../footer.php'); ?>