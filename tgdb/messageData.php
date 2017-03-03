  <div class="panel panel-<?php echo $message->class;?>">
    <div class="panel-heading">
      <h3 class="panel-title">
        <?php echo $message->icon;?> <?php echo $message->type;?> for
        <?php echo $message->ckeylink;?> by
        <?php echo $message->adminckey;?>, <?php echo $message->timeStamp;?>
        <p class="pull-right"><?php echo $message->permalink;?></p>
      </h3>
    </div>
    <div class="panel-body">
     <p><?php echo $message->text;?></p>
    </div>
    <div class="panel-footer">
      <small><?php echo $message->privacy;?>
        <?php echo ($message->edits)?"(Edited, see $message->permalink for details)":"";?>
      </small>
    </div>
  </div>