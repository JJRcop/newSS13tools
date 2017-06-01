<?php
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

?>

<div class="page-header">
  <h1>Rounds</h1>
</div>
<?php if ($pages):?>
  <nav aria-label="Page navigation">
    <ul class="pagination">
      <?php if ($page > 1):?>
      <li>
        <a href="<?php echo APP_URL;?>round.php?page=<?php echo $page-1;?>" aria-label="Previous">
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
          echo "<a href='".APP_URL."round.php?page=$i'>$i</a></li>";
        }
      } else if ($page <= 5) {
        for ($i = 1; $i <= 5; $i++){
          if ($page == $i){
            echo "<li class='active'>";
          } else {
            echo "<li>";
          }
          echo "<a href='".APP_URL."round.php?page=$i'>$i</a></li>";
        }
      } else {
        for ($i = ($pages-5); $i <= $pages; $i++){
          if ($page == $i){
            echo "<li class='active'>";
          } else {
            echo "<li>";
          }
          echo "<a href='".APP_URL."round.php?page=$i'>$i</a></li>";
        }
      }

      ?>
      <li>
        <?php if ($page < $pages):?>
        <li>
          <a href="<?php echo APP_URL;?>round.php?page=<?php echo $page+1;?>" aria-label="Next">
            <span aria-hidden="true">&raquo;</span>
          </a>
        </li>
        <?php endif;?>
      </li>
    </ul>
  </nav>
<?php endif;?>

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
    $rounds = $round->listRounds($page);
    if (!$rounds) {
      echo "<tr><td colspan='5'>No rounds found</td></tr>";
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

<?php if ($pages):?>
  <nav aria-label="Page navigation">
    <ul class="pagination">
      <?php if ($page > 1):?>
      <li>
        <a href="<?php echo APP_URL;?>round.php?page=<?php echo $page-1;?>" aria-label="Previous">
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
          echo "<a href='".APP_URL."round.php?page=$i'>$i</a></li>";
        }
      } else if ($page <= 5) {
        for ($i = 1; $i <= 5; $i++){
          if ($page == $i){
            echo "<li class='active'>";
          } else {
            echo "<li>";
          }
          echo "<a href='".APP_URL."round.php?page=$i'>$i</a></li>";
        }
      } else {
        for ($i = ($pages-5); $i <= $pages; $i++){
          if ($page == $i){
            echo "<li class='active'>";
          } else {
            echo "<li>";
          }
          echo "<a href='".APP_URL."round.php?page=$i'>$i</a></li>";
        }
      }

      ?>
      <li>
        <?php if ($page < $pages):?>
        <li>
          <a href="<?php echo APP_URL;?>round.php?page=<?php echo $page+1;?>" aria-label="Next">
            <span aria-hidden="true">&raquo;</span>
          </a>
        </li>
        <?php endif;?>
      </li>
    </ul>
  </nav>
<?php endif;?>


