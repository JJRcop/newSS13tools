<?php if (!$round->logs): ?>
  <p class="lead">Unable to accurately locate logs for round #<?php echo $round->round_id;?>, because either the server or round start time could not be located.</p>
  <?php if (isset($round->data->end_error)):?>
    <p class="lead">The round ended prematurely because: <?php echo $round->data->end_error['details'];?></p>
  <?php endif;?>
<?php endif;?>