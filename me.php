<?php require_once('header.php');?>
<?php 
  $logout = filter_input(INPUT_GET, 'logout', FILTER_VALIDATE_BOOLEAN);
  if($logout) {
    echo parseReturn($user->logout());
    return;
  }?>
<?php if ($user->ckey):?>

  <div class="page-header">
    <h1><span class="pull-right">
      <a href="?logout=true" class="btn btn-danger">Log out</a></span>
      <small>You are</small> <?php echo $user->label;?></h1>
  </div>

  <p class='lead'>Between your first connection on <strong><?php echo $user->firstseen;?></strong> and your most recent connection on <strong><?php echo $user->lastseen;?></strong>, you have connected <?php echo $user->connections;?> times.</p>

  <p class='lead'>You have wasted 0 hours playing Space Station 13, because time spent doing something you enjoy isn't wasted time.</p>

  <?php $hours = $user->getActiveHours($user->ckey);?>
  <?php $roles = $user->getActiveRoles($user->ckey);?>
  <?php require_once(ROOTPATH."/inc/view/UserGraphs.php");?>

  <?php $public = true;?>
  <?php
  $message = new message();
  $user->messages = $message->getPlayerMessages($user->ckey);?>
  <div class="row">
  <div class="col-md-12">
    <?php if ($user->messages):?>
      <div class="page-header">
        <h2>Messages <?php echo (count($user->messages) > 5)?"<a class='btn btn-xs btn-primary' data-toggle='collapse' href='#messages'>Show</a> ":""?><small>(<?php echo count($user->messages);?>)</small></h2>
      </div>
    <?php else:?>
      <div class="page-header">
        <h2>No messages on record</h2>
      </div>
    <?php endif;?>
      <div class="<?php echo (count($user->messages) > 5)?"collapse":""?>" id="messages">
    <?php if ($user->messages){
      foreach ($user->messages as $message) {
        include(ROOTPATH.'/tgdb/messageData.php');
        if ($message->edits){ ?>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">Edits</h3>
          </div>
          <div class="panel-body">
            <?php echo $message->edits;?>
          </div>
        </div>
        <?php 
        }
      }
    } else {
      echo alert("No messages to show",1);
    }?>
    </div>

  </div>
  </div>

<?php else: ?>
  <?php if('remote' == $app->auth_method):?>
  <div class="page-header">
    <h1><small>Hmm, I'm not sure who you are. Can you <a href="<?php echo $app->APP_URL;?>auth.php">authenticate</a> for me?</small></h1>
  </div>
  <?php else:?>
    <div class="page-header">
      <h1><small>Hmm, I'm not sure who you are. Have you connected to one of the game servers recently?</small></h1>
    </div>
  <?php endif;?>
<?php endif;?>

<?php require_once('footer.php');
