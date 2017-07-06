<?php require_once("statsHeader.php");?>
 
<?php foreach ($stat->details as $c):?>
  <div class="media medal" id="<?php echo $c->id;?>">
    <div class="media-left">
        <img class="media-object" src="<?php echo $app->APP_URL;?>icons/obj/clothing/accessories/<?php echo $c->graphic;?>-0.png" alt="<?php echo $c->medal;?>" width="64" height="64">
    </div>
    <div class="media-body">
      <h4 class="media-heading"><?php echo $c->commendee;?> <a href="#<?php echo $c->id;?>"><i class='fa fa-fw fa-link'></i></a></h4>
      <p><?php echo $c->commender;?> awards <?php echo $c->commendee;?> <em><?php echo $c->medal;?></em> for "<?php echo $c->reason;?>"</p>
    </div>
  </div>
<?php endforeach;?>