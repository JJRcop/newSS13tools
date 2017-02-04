<nav>
  <ul class="pager">
  <?php if ($round->neighbors->prev): ?>
    <li class="previous">
        <a href="viewRound.php?round=<?php echo $round->neighbors->prev;?>">
        <span aria-hidden="true">&larr;</span>
        Previous round</a>
    </li>
  <?php endif;?>

  <?php if ($round->neighbors->next): ?>
    <li class="next">
      <a href="viewRound.php?round=<?php echo $round->neighbors->next;?>">Next round
      <span aria-hidden="true">&rarr;</span></a>
    </li>
  <?php endif;?>
  </ul>
</nav>