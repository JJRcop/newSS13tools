<?php if ($pages):?>
<?php ob_start();?>
  <nav aria-label="Page navigation">
    <ul class="pagination">
      <?php if ($page > 1):?>
      <li>
        <a href="<?php echo APP_URL;?>link.php?page=<?php echo $page-1;?>" aria-label="Previous">
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
          echo "<a href='".APP_URL."link.php?page=$i'>$i</a></li>";
        }
      } else if ($page <= 5) {
        for ($i = 1; $i <= 5; $i++){
          if ($page == $i){
            echo "<li class='active'>";
          } else {
            echo "<li>";
          }
          echo "<a href='".APP_URL."link.php?page=$i'>$i</a></li>";
        }
      } else {
        for ($i = ($pages-5); $i <= $pages; $i++){
          if ($page == $i){
            echo "<li class='active'>";
          } else {
            echo "<li>";
          }
          echo "<a href='".APP_URL."link.php?page=$i'>$i</a></li>";
        }
      }

      ?>
      <li>
        <?php if ($page < $pages):?>
        <li>
          <a href="<?php echo APP_URL;?>link.php?page=<?php echo $page+1;?>" aria-label="Next">
            <span aria-hidden="true">&raquo;</span>
          </a>
        </li>
        <?php endif;?>
      </li>
    </ul>
  </nav>
  <?php $pagination = ob_get_contents();
  ob_end_clean();
  $pagination = str_replace('link', $link, $pagination);
  echo $pagination;?>
<?php endif;?>