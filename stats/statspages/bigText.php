<?php require_once("statsHeader.php");?>
<?php if(!$smol):?>
<div class="jumbotron">
  <h1>
    <small><?php echo $stat->var_name;?>:</small><br>
    <?php if(is_array($stat->details)):?>
      <?php foreach ($stat->details as $d): echo "$d<br>"; endforeach;?>
    <?php else:?>
      <?php echo str_replace('_', ' ', $stat->details);?>
    <?php endif;?>
  </h1>
</div>

<?php else:?>
  <?php if(is_array($stat->details)):?>
    <?php foreach ($stat->details as $d): echo "$d<br>"; endforeach;?>
  <?php else:?>
    <?php echo str_replace('_', ' ', $stat->details);?>
  <?php endif;?>
<?php endif;?>