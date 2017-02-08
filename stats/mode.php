<?php
$mode = false;
if(isset($_GET['mode'])) $mode = filter_input(INPUT_GET, 'mode', FILTER_SANITIZE_STRING, array(FILTER_FLAG_STRIP_HIGH));
$mode = urldecode($mode);

require_once('../header.php'); 

switch($mode){
  case 'revolution':
  case 'clockwork cult':
  case 'cult':
  case 'nuclear emergency':
  case 'blob':
  case 'meteor':
  case 'gang war':
    $stat = new stat();
    $mode = $stat->getModeData($mode);
  break;

  default: 
    die("No game mode stats available for this game mode.");
  break;
} ?>

<?php if(!$mode):?>
  <div class="alert alert-danger">No mode specified</div>
<?php die(); endif;?>

<?php var_dump($mode);?>

<?php require_once('../footer.php'); ?>