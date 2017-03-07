<?php require_once('header.php'); ?>

<?php $app = new app(TRUE); ?>
<div class="row">
  <div class="col-md-6">
    <div class="page-header">
    <h1>The Todo list</h1>
    </div>

    <h2>Done!</h2>
    <ul class="options-list">
      <li class="list-group-item">Cached month-by-month stats</li>
      <li class="list-group-item">Month-by-month stats</li>
      <li class="list-group-item">Tgstation forum auth</li>
    </ul>

    <h2>Near-future</h2>
    <ul class="options-list">
      <li class="list-group-item">DMI diff viewer</li>
      <li class="list-group-item">Round list calendar view</li>
      <li class="list-group-item">Split stats by server</li>
      <li class="list-group-item">Daily death maps</li>
    </ul>

    <h2>Far future</h2>
    <ul class="options-list">
      <li class="list-group-item">Ban appeals proof-of-concept</li>
      <li class="list-group-item">Stats refactor design document</li>
      <li class="list-group-item">Refactor stats</li>
    </ul>
  </div>
  <div class="col-md-6">
    <?php require_once('changelog.php');?>
  </div>
</div>
<?php require_once('footer.php');