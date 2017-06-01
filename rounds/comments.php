<?php 
if (isset($_GET['addComment']) && isset($_POST['comment'])) {
  $newComment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH);
  echo parseReturn($round->addComment($round->id, $newComment));
}

if (isset($_GET['approveComment'])){
  $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
  if($id){
    echo parseReturn($round->flipCommentFlag($id, 'A'));
  } else {
    echo parseReturn(returnError("Unable to approve comment"));
  }
}

if (isset($_GET['reportComment'])){
  $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
  if($id){
    echo parseReturn($round->flipCommentFlag($id, 'R'));
  } else {
    echo parseReturn(returnError("Unable to report comment"));
  }
}

if (isset($_GET['hideComment'])){
  $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
  if($id){
    echo parseReturn($round->flipCommentFlag($id, 'H'));
  } else {
    echo parseReturn(returnError("Unable to hide comment"));
  }
}

?>

<div class="page-header">
  <h2>Comments about this round</h2>
</div>

<?php $comments = $round->getRoundComments($round->id);?>
<?php if(!$comments):?>
  <p>No comments on this round yet.</p>
<?php endif;?>

<?php foreach ($comments as $comment):?>
  <div class="panel panel-<?php echo $comment->class;?>">
    <div class="panel-heading">
      <h3 class="panel-title">
      <p class="pull-right"><a href="#<?php echo $comment->id;?>">#<?php echo $comment->id;?></a></p>
      <?php if(2 <= $user->level):?>
        <?php echo $comment->author_link;?>
      <?php else: ?>
      <?php echo $comment->author;?>
      <?php endif;?>
        <small>commented at <?php echo $comment->timestamp;?></small>
      </h3>
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