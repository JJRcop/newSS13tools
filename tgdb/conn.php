<?php require_once("../header.php");?>

<?php require_once('tgdb_nav.php');?>

<?php
$filter = null;
$filterby = null;
if (isset($_GET['ip'])){
  $filterby = 'ip';
  $filter = filter_input(INPUT_GET, 'ip',FILTER_VALIDATE_IP);
}

if (isset($_GET['cid'])){
  $filterby = 'cid';
  $filter = filter_input(INPUT_GET, 'cid',FILTER_VALIDATE_INT);
}
?>

<?php $player = new user();
$connections = $user->getConnectedPlayers();?>

<div class="page-header">
  <h1>''''Active'''' Connections</h1>
</div>

<?php
$bagil = array();
$sybil = array();
foreach ($connections as $c){
  if (2337 == $c->server_port) {
    $bagil[] = $c;
  } else {
    $sybil[] = $c;
  }
}
?>

<div class="row">
  <div class="col-md-6">
    <div class="page-header">
      <h2>Bagil</h2>
    </div>
    <table class="table table-bordered table-condensed">
      <thead>
        <tr>
          <th>ckey</th>
          <th>IP</th>
          <th>CID</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($bagil as $c): ?>
        <tr>
          <td>
            <a href='viewPlayer.php?ckey=<?php echo $c->ckey;?>'>
              <?php echo $c->ckey;?>
            </a>
          </td>
          <td><?php echo long2ip($c->ip);?></td>
          <td><?php echo $c->computerid;?></td>
        </tr>
      <?php endforeach;?>
      </tbody>
    </table>
  </div>
  <div class="col-md-6">
    <div class="page-header">
      <h2>Sybil</h2>
    </div>
    <table class="table table-bordered table-condensed">
      <thead>
        <tr>
          <th>ckey</th>
          <th>IP</th>
          <th>CID</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($sybil as $c): ?>
        <tr>
          <td>
            <a href='viewPlayer.php?ckey=<?php echo $c->ckey;?>'>
              <?php echo $c->ckey;?>
            </a>
          </td>
          <td><?php echo long2ip($c->ip);?></td>
          <td><?php echo $c->computerid;?></td>
        </tr>
      <?php endforeach;?>
      </tbody>
    </table>
  </div>
</div>


<?php require_once('../footer.php');?>