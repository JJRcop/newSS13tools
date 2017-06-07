<?php require_once('header.php');?>
<?php if ($user->ckey):?>
  <div class="page-header">
    <h1><small>You are</small> <?php echo $user->label;?></h1>
  </div>

  <p class='lead'>Between your first connection on <strong><?php echo $user->firstseen;?></strong> and your most recent connection on <strong><?php echo $user->lastseen;?></strong>, you have connected <?php echo $user->connections;?> times.</p>

  <p class='lead'>You have wasted 0 hours playing Space Station 13, because time spent doing something you enjoy isn't wasted time.</p>


  <?php $hours = $user->getActiveHours($user->ckey);?>
  <?php $roles = $user->getActiveRoles($user->ckey);?>

  <?php require_once(ROOTPATH."/inc/view/UserGraphs.php");?>

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
