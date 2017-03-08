<?php require_once('../header.php'); ?>

<?php
$app = new app(true);

$update = false;
if (isset($_GET['update'])){
  $update = filter_input(INPUT_GET, 'update', FILTER_VALIDATE_BOOLEAN);
  $updateLine = $app->updateLocalRepo();
}
?>

<div class="page-header">
  <h1>Update TG codebase</h1>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="jumbotron">
      <h1>Repo Status: <?php if ($app->reposSynced):?>
        <a class='btn btn-lg btn-success' disabled href='#'>In Sync!</a>
        <?php else :?>
        <a class='btn btn-lg btn-danger' href='?update=true'>Out Of Sync!</a>
        <?php endif;?>
      </h1>
      <?php if ($update):?>
        <p class="lead">Updating local repository returned:</p>
        <code><?php echo $updateLine;?></code>
      <?php else:?>
        <code><?php echo $app->localRepoVersion;?></code>
      <?php endif;?>
    </div>
  </div>
</div>
<?php require_once('../footer.php'); ?>