<?php
require_once('header.php');?>

<?php if (!$user->ckey):?>

  <div class="page-header">
    <h1><small>Hmm, I'm not sure who you are. Can you <a href="<?php echo APP_URL;?>auth.php">authenticate</a> for me?</small></h1>
  </div>
  <?php else: ?>

<div class="page-header">
  <h1><small>You are</small> <?php echo $user->label;?></h1>
</div>

<p class='lead'>Between your first connection on <strong><?php echo $user->firstseen;?></strong> and your most recent connection on <strong><?php echo $user->lastseen;?></strong>, you have connected <?php echo $user->connections;?> times.</p>

<p class='lead'>You have wasted 0 hours playing Space Station 13, because time spent doing something you enjoy isn't wasted time.</p>

<div class="row">
  <div class="col-md-6">
    <div class="page-header">
      <h2>Active hours</h2>
    </div>

    <div id="c">
    </div>

    <?php $hours = $user->getActiveHours($user->ckey);?>
    <?php $roles = $user->getActiveRoles($user->ckey);?>

    <script>

    var chart = c3.generate({
        bindto: '#c',
        data: {
          json: <?php echo json_encode($hours); ?>,
          keys: {
            value: ['hour', 'connections'],
          },
          x: 'hour',
          y: 'connections',
          type: 'bar',
        },
        axis: {
          x: {
            type: 'category',
            tick: {
              culling: false
            }
          }
        }
    });

    </script>
  </div>
  <div class="col-md-6">
    <div class="page-header">
      <h2>Active roles
        <small><i class="fa fa-flask"></i> Experimental</small> 
      </h2>
    </div>
    <div id="d">
    </div>
  </div>
  <script>
  var chart = c3.generate({
      bindto: '#d',
      data: {
        json: <?php echo json_encode($roles); ?>,
        keys: {
          value: ['job', 'minutes'],
        },
        x: 'job',
        y: 'minutes',
        type: 'bar',
      },
      axis: {
        x: {
          type: 'category',
          tick: {
            culling: false
          }
        }
      }
  });
  </script>
</div>

<?php endif;?>

<?php require_once('footer.php');
