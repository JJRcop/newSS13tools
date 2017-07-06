<div class="panel panel-<?php echo $message->class;?>">
  <div class="panel-heading">
    <h3 class="panel-title">
      <?php echo $message->icon;?> <?php echo $message->privacy;?> <?php echo $message->type;?> for
      <?php if(!isset($public) || $public == false):?>
        <?php echo $message->ckeylink;?>
      <?php else: ?>
        <?php echo $message->targetckey;?>
      <?php endif;?> by
      <?php echo $message->adminckey;?>, <?php echo $message->timeStamp;?>
      <p class="pull-right">
      <?php if(!isset($public) || $public == false):?>
        <?php echo $message->permalink;?>
      <?php else: ?>
        #<?php echo $message->id;?>
      <?php endif;?>
      </p>
    </h3>
  </div>
  <div class="panel-body">
   <p><?php echo $message->text;?></p>
  </div>
  <div class="panel-footer">
    <small>
      <?php if(!isset($public) || $public == false):?>
        <?php echo ($message->edits)?"(Edited, see $message->permalink for details)":"";?>
      <?php else: ?>
        <?php echo ($message->edits)?"Edited, details below:":"";?>
      <?php endif;?>
    </small>
  </div>
</div>