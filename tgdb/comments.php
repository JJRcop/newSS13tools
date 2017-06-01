<?php
require_once("../header.php");
require_once('tgdb_nav.php');
$round = new round();

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
  <h2>Round Comment Management</h2>
</div>

<?php $comments = $round->getAllRoundComments();?>
<?php if(!$comments):?>
  <p>No comments on this round yet.</p>
<?php endif;?>
<table class="table table-bordered table-condesned">
  <thead>
    <tr>
      <th>ID</th>
      <th>Round</th>
      <th>Author</th>
      <th>Text</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($comments as $comment):?>
      <tr class="<?php echo $comment->class;?>">
        <td>
          <a href="#<?php echo $comment->id;?>">#<?php echo $comment->id;?></a>
        </td>
        <td>
          <?php echo $comment->round_link;?>
        </td>
        <td><?php echo $comment->author_link;?><br>
          <small><?php echo $comment->timestamp;?></small>
        </td>
        <td>
          <?php echo $comment->text;?>
        </td>
        <td>
          <?php if('P' == $comment->flagged && 2 <= $user->level):?>
            <small>Comment pending approval. <a href="?approveComment&id=<?php echo $comment->id;?>" class="btn btn-success btn-xs">Approve</a> or <a href="?hideComment&id=<?php echo $comment->id;?>" class="btn btn-danger btn-xs">Deny</a> </small>

          <?php elseif ('R' == $comment->flagged && 2 <= $user->level):?>
            <small>Comment reported by <?php echo $comment->reporter;?> at <?php echo $comment->reported_time;?>. <a href="?hideComment&id=<?php echo $comment->id;?>" class="btn btn-danger btn-xs">Remove comment</a> or <a href="?approveComment&id=<?php echo $comment->id;?>" class="btn btn-success btn-xs">Unflag</a> </small>

          <?php elseif ('A' == $comment->flagged):?>
            <small><a href="?reportComment&id=<?php echo $comment->id;?>" class="btn btn-danger btn-xs">Report Comment</a></small>

          <?php elseif ('H' == $comment->flagged):?>
            <a href="?approveComment&id=<?php echo $comment->id;?>" class="btn btn-success btn-xs">Unhide</a>
          <?php endif;?>
        </td>
      </tr>
    <?php endforeach;?>
  </tbody>
</table>

<?php require_once('../footer.php');?>