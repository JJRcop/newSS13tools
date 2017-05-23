<?php require_once("../header.php");?>
<?php require_once('tgdb_nav.php');?>

<?php
$ckey = false;
if (isset($_GET['ckey'])){
  $ckey = filter_input(INPUT_GET, 'ckey',
    FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_HIGH);
}

if (!$ckey){
  die(alert("Ckey not found.",FALSE));
}

$player = new user();
$player = $player->getPlayerByCkey($ckey);
?>

<div class="page-header">
  <h1><?php echo $player->label;?>&nbsp;
  <small>Last seen <?php echo $player->lastSeenTimeStamp;?> | 
  <a href="https://www.byond.com/members/<?php echo $player->ckey;?>"
  target="_blank">BYOND <i class="fa fa-external-link"></i></a>
  </small></h1>
</div>

<div class="row">
  <div class="col-md-4">
    <ul class="list-group">
      <li class="list-group-item" style="background: <?php echo $player->backColor;?>; color: <?php echo $player->foreColor;?>;">
        <strong class="list-group-item-heading">Last rank</strong>
          <?php echo $player->rank;?>
      </li>
      <?php if(strpos($player->standing,'Permanent') !== FALSE):?>
        <li class="list-group-item perma">
      <?php elseif (strpos($player->standing,'Temporarily') !== FALSE):?>
        <li class="list-group-item list-group-item-danger">
      <?php else:?>
        <li class="list-group-item list-group-item-success">
      <?php endif;?>
        <strong class="list-group-item-heading">Account standing</strong>
          <?php echo $player->standing;?>
      </li>
      <li class="list-group-item">
        <strong class="list-group-item-heading">First seen</strong>
          <?php echo $player->firstSeenTimeStamp;?>
      </li>
      <li class="list-group-item">
        <strong class="list-group-item-heading">Last seen</strong>
          <?php echo $player->lastSeenTimeStamp;?>
      </li>
      <li class="list-group-item">
        <strong class="list-group-item-heading">Total Connections</strong>
          <?php echo $player->connections;?>
      </li>
    </ul>
  </div>
  <div class="col-md-4">
    <ul class="list-group">
<!--       <li class="list-group-item list-group-item-success">
        <strong class="list-group-item-heading">(<i class="fa fa-flask"></i>) Grief Index™</strong>
          0
      </li> -->
      <li class="list-group-item ipaddr">
        <strong class="list-group-item-heading">Last IP</strong>
          <?php echo $player->ip;?>
          <?php echo $player->ipql;?>
            
      </li>
      <li class="list-group-item cid">
        <strong class="list-group-item-heading">Last CID</strong>
          <?php echo $player->computerid;?> <?php echo $player->cidql;?>
      </li>
      <li class="list-group-item">
        <strong class="list-group-item-heading">IPs seen</strong>
          <?php echo $player->IPs;?>
      </li>
      <li class="list-group-item">
        <strong class="list-group-item-heading">CIDs seen</strong>
          <?php echo $player->CIDs;?>
      </li>
    </ul>
  </div>
  <div class="col-md-4">
  </div>
</div>

<div class="row">
<div class="col-md-12">
<?php if ($player->messages):?>
    <div class="page-header">
      <h2>Messages <?php echo (count($player->messages) > 5)?"<a class='btn btn-xs btn-primary' data-toggle='collapse' href='#messages'>Show</a> ":""?><small>(<?php echo count($player->messages);?>)</small></h2>
    </div>
  <?php else:?>
    <div class="page-header">
      <h2>No messages on record</h2>
    </div>
  <?php endif;?>
    <div class="<?php echo (count($player->messages) > 5)?"collapse":""?>" id="messages">
  <?php if ($player->messages){
    foreach ($player->messages as $message) {
      include('messageData.php');
    }
  } else {
    echo alert("No messages to show",1);
  }?>
    </div>

<?php if ($player->bans):?>
    <div class="page-header">
      <h2>Bans <?php echo (count($player->bans) > 5)?"<a class='btn btn-xs btn-primary' data-toggle='collapse' href='#bans'>Show</a> ":""?><small>(<?php echo count($player->bans);?>)</small></h2>
    </div>
  <?php else:?>
    <div class="page-header">
      <h2>No Bans on record</h2>
    </div>
<?php endif;?>
  <div class="<?php echo (count($player->bans) > 5)?"collapse":""?>" id="bans">
<?php if ($player->bans){
  foreach ($player->bans as $ban) {
    include('banData.php');
  }
} else {
  echo alert("No bans to show",1);
}?>
  </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="page-header">
      <h2>Active hours</h2>
    </div>

    <div id="c">
    </div>
    <?php $etc = new user(); ?>
    <?php $hours = $etcr->getActiveHours($player->ckey);?>
    <?php $roles = $etcr->getActiveRoles($player->ckey);?>

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



<?php require_once('../footer.php');?>