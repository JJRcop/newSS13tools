<nav>
  <ul class="pager">
  <?php if ($round->prev): ?>
    <li class="previous">
        <a href="viewRound.php?round=<?php echo $round->prev;?>">
        <span aria-hidden="true">&larr;</span>
        Previous round</a>
    </li>
  <?php endif;?>

  <?php if ($round->next): ?>
    <li class="next">
      <a href="viewRound.php?round=<?php echo $round->next;?>">Next round
      <span aria-hidden="true">&rarr;</span></a>
    </li>
  <?php endif;?>
  </ul>
</nav>