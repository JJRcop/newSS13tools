<?php
require_once('header.php');?>

<?php
$db = new database();
$db->query("SELECT COUNT(id) AS deaths FROM ss13death WHERE byondkey = ?");
$db->bind(1,$user->byond);
$db->execute();
$deathCount = $db->single()->deaths;
?>

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

<p class="lead text-muted">But you have died <?php echo $deathCount;?> times which is not as great.</p>

<div class="page-header">
  <h2>Your active hours</h2>
</div>

<div id="c">
</div>

<?php $hours = $user->getActiveHours($user->ckey);?>

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

<?php endif;?>

<?php require_once('footer.php');
