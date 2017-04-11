<div class="jumbotron">
  <h1>
    <small><?php echo $stat->var_name;?>:</small>
      <?php echo $stat->details;?>
  </h1>
  <?php if(isset($stat->rounds)):?>
    <p class="lead">Tracked across <?php echo $stat->rounds;?> rounds</p>
  <?php endif;?>
  <?php if(isset($stat->round_id)):?>
    <p class="lead">From round <?php echo $round->link;?></p>
  <?php endif;?>
</div>