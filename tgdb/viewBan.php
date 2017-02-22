<?php require_once("../header.php");
require_once('tgdb_nav.php');

if (!isset($_GET['ban'])) die("No ban ID specified!");
$ban = filter_input(INPUT_GET, 'ban', FILTER_SANITIZE_NUMBER_INT);
$ban = new ban($ban);
if (!$ban->id) die("Ban not found");
?>

<?php include('banData.php');?>

<?php if ($ban->edits): ?>
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Edits</h3>
  </div>
  <div class="panel-body">
    <?php echo $ban->edits;?>
  </div>
</div>
<?php endif; ?>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Admins online at time of ban</h3>
  </div>
  <div class="panel-body">
    <ul class="list-inline">
      <?php foreach ($ban->adminwho as $a) :?>
        <li><a href='viewAdmin.php?ckey=<?php echo $a['ref'];?>'>
          <?php echo $a['ckey'];?>
        </a></li>
      <?php endforeach;?>
      </ul>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Players online at time of ban</h3>
  </div>
  <div class="panel-body">
    <ul class="list-inline">
      <?php foreach ($ban->who as $p) :?>
        <li><a href='viewPlayer.php?ckey=<?php echo $p['ref'];?>'>
          <?php echo $p['ckey'];?>
        </a></li>
      <?php endforeach;?>
    </ul>
  </div>
</div>

<?php require_once('../footer.php');?>