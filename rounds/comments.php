<?php 
if (isset($_GET['addComment']) && isset($_POST['comment'])) {
  $newComment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH);
  echo parseReturn($round->addComment($round->id, $newComment));
}

if (isset($_GET['approveComment'])){
  $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
  echo parseReturn($round->flipCommentFlag($id, 'A'));
}

?>

<div class="page-header">
  <h2>Comments about this round</h2>
</div>

<?php
$comments = $round->getRoundComments($round->id);
foreach ($comments as $comment):?>
  <div class="panel panel-<?php echo $comment->class;?>">
    <div class="panel-heading">
      <h3 class="panel-title"><?php echo $comment->author;?> <small>commented at <?php echo $comment->timestamp;?></small></h3>
    </div>
    <div class="panel-body">
      <?php echo $comment->text;?>
    </div>
    <div class="panel-footer">
    <?php if('P' == $comment->flagged && 2 <= $user->level):?>
      <small>Comment pending approval. <a href="?approveComment&id=<?php echo $comment->id;?>&round=<?php echo $round->id;?>" class="btn btn-success btn-xs">Approve</a> or <a href="?hideComment&id=<?php echo $comment->id;?>&round=<?php echo $round->id;?>" class="btn btn-danger btn-xs">Deny</a> </small>
    <?php elseif ('R' == $comment->flagged && 2 <= $user->level):?>
      <small>Comment reported by <?php echo $comment->reporter;?> at <?php echo $comment->reported_time;?>. <a href="?hideComment&id=<?php echo $comment->id;?>&round=<?php echo $round->id;?>" class="btn btn-danger btn-xs">Remove comment</a> or <a href="?approveComment&id=<?php echo $comment->id;?>&round=<?php echo $round->id;?>" class="btn btn-success btn-xs">Unflag</a> </small>
    <?php elseif ('A' == $comment->flagged):?>
      <small><a href="?reportComment&id=<?php echo $comment->id;?>&round=<?php echo $round->id;?>" class="btn btn-danger btn-xs">Report Comment</a></small>
    <?php endif;?>
    </div>
  </div>
<?php endforeach;?>
<hr>
<form class="form" action="?round=<?php echo $round->id;?>&addComment=true" method="POST">
  <div class="form-group">
    <textarea class="form-control" name="comment" rows="10" placeholder="Comment" required=""></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Submit Comment</button>
</form>