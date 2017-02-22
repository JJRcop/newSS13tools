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
              <p><?php echo nl2br($memo->memotext);?></p>
              <footer><?php echo $memo->ckey;?>
                <cite><?php echo $memo->timestamp;?></cite>
              </footer>
            </blockquote>
          </li>
        <?php endforeach; else: ?>
        <li class="list-group-item">No memos</li>
       <?php endif; ?>
    </ul>
  </div>
</div>