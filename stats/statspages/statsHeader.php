<?php if(!isset($smol)) $smol = false;?>
<?php if (!$smol):?>
<div class="jumbotron">
  <h1>
    <small>Stats for</small> <?php echo $stat->var_name;?>
  </h1>
  <?php if(isset($stat->rounds)):?>
    <p class="lead">Tracked across <?php echo $stat->rounds;?> rounds</p>
  <?php endif;?>
  <?php if(isset($stat->round_id)):?>
    <p class="lead">From round <?php echo $round->link;?></p>
  <?php endif;?>
</div>
<?php endif;?>

<?php if($stat->splain):?>
  <div class="alert alert-info"><?php echo $stat->splain;?></div>
<?php endif;?>