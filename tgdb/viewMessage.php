<?php require_once("../header.php");
require_once('tgdb_nav.php');

if (!isset($_GET['message'])) die("No message ID specified!");
$message = filter_input(INPUT_GET, 'message', FILTER_SANITIZE_NUMBER_INT);
$message = new message($message);
if (!$message->id) die("Message not found");
?>

<?php include('messageData.php');?>

<?php if ($message->edits): ?>
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Edits</h3>
  </div>
  <div class="panel-body">
    <?php echo $message->edits;?>
  </div>
</div>
<?php endif; ?>

<?php require_once('../footer.php');?>