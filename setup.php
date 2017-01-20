<?php require_once('header.php'); ?>

<div class="page-header">
  <h1>Setup</h1>
</div>

<div class="alert alert-success">

<?php $action = $_GET['action']; 

switch($action) {
  case 'generateIconsDir':
    if(!is_dir(GENERATED_ICONS)){
      mkdir(GENERATED_ICONS);
    }
    echo "Created the <code>icons</code> directory!";
  break;
}

?>

</div>

<?php require_once('footer.php'); ?>