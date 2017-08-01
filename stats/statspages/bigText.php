<?php require_once("statsHeader.php");?>

<div class="jumbotron">
  <h1>
    <small><?php echo $stat->var_name;?>:</small><br>
    <?php echo str_replace('_', ' ', $stat->details);?>
  </h1>
</div>