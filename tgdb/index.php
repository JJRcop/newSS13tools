<?php require_once("../header.php");?>

<?php require_once('tgdb_nav.php');?>

<?php echo alert('## CLASSIFIED ## GA+//NA</strong> This page is classified. This page should not shared with non-admins.');?>

<div class="page-header">
  <h1>tgdb</h1>
</div>

<div class="row">
  <div class="col-md-6">
  <h3>Current admin memos</h3>
    <ul class="list-group">
      <?php $memos = $app->getMemos();
      if ($memos):
        foreach($memos as $memo):?>
          <li class="list-group-item">
            <blockquote>
              <p><?php echo auto_link_text(nl2br($memo->text));?></p>
              <footer><?php echo $memo->adminckey;?>
                <cite><?php echo $memo->timestamp;?></cite>
              </footer>
            </blockquote>
          </li>
        <?php endforeach; else: ?>
        <li class="list-group-item">No memos</li>
       <?php endif; ?>
    </ul>
  </div>
  <div class="col-md-6">
  <h3>See also:</h3>
  <ul class="list-group">
    <li class="list-group-item">
      <a href="ahelpStats.php">Ahelp stats</a>
    </li>
    <li class="list-group-item">
      <a href="banCounts.php">Number of bans, by type, by admin</a>
    </li>
    <li class="list-group-item">
      <a href="adminHours.php">Hours admins are online</a>
    </li>
  </ul>
  </div>
</div>
<?php require_once('../footer.php');?>