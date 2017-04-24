<?php
require_once('header.php');?>

<div class="page-header">
  <h1><small>You are</small> <?php echo $user->label;?></h1>
</div>

<p class='lead'>Between your first connection on <strong><?php echo $user->firstseen;?></strong> and your most recent connection on <strong><?php echo $user->lastseen;?></strong>, you have connected <?php echo $user->connections;?> times.</p>

<p class='lead'>You have wasted 0 hours playing Space Station 13, because time spent doing something you enjoy isn't wasted time.</p>

<?php require_once('footer.php');
