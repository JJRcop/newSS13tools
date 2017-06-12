<?php require_once("../header.php");
require_once('tgdb_nav.php');

if (!isset($_GET['ban'])) die("No ban ID specified!");
$ban = filter_input(INPUT_GET, 'ban', FILTER_SANITIZE_NUMBER_INT);
$ban = new ban($ban);
if (!$ban->id) die("Ban not found");

if (isset($_GET['addComment']) && isset($_POST['comment'])) {
  $newComment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH);
  echo parseReturn($ban->addComment($ban->id, $newComment));
  resetURL($ban->link);
}

if (isset($_GET['approveComment'])){
  $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
  if($id){
    echo parseReturn($ban->flipCommentFlag($id, 'A'));
  } else {
    echo parseReturn(returnError("Unable to approve comment"));
  }
  resetURL($ban->link);
}

if (isset($_GET['reportComment'])){
  $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
  if($id){
    echo parseReturn($ban->flipCommentFlag($id, 'R'));
  } else {
    echo parseReturn(returnError("Unable to report comment"));
  }
  resetURL($ban->link);
}

if (isset($_GET['hideComment'])){
  $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
  if($id){
    echo parseReturn($ban->flipCommentFlag($id, 'H'));
  } else {
    echo parseReturn(returnError("Unable to hide comment"));
  }
  resetURL($ban->link);
}
?>

<?php include('banData.php');?>

<?php if ($ban->edits): ?>
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Edits</h3>
  </div>
  <div class="panel-body">
    <?php echo $ban->edits;?>
  </div>
</div>
<?php endif; ?>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          <a href="#badmins" data-toggle="collapse">
            Admins online at time of ban
          </a>
        </h3>
      </div>
      <div class="panel-body collapse in" id="badmins">
        <ul class="list-inline">
          <?php foreach ($ban->adminwho as $a) :?>
            <li><a href='viewAdmin.php?ckey=<?php echo $a['ref'];?>'>
              <?php echo $a['ckey'];?>
            </a></li>
          <?php endforeach;?>
          </ul>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          <a href="#who" data-toggle="collapse">
            Players online at time of ban
          </a>
        </h3>
      </div>
      <div class="panel-body collapse in" id="who">
        <ul class="list-inline">
          <?php foreach ($ban->who as $p) :?>
            <li><a href='viewPlayer.php?ckey=<?php echo $p['ref'];?>'>
              <?php echo $p['ckey'];?>
            </a></li>
          <?php endforeach;?>
        </ul>
      </div>
    </div>
  </div>
  <div class="col-md-12">
  <hr>
  <?php $comments = $ban->getbanComments($ban->id);?>
  <?php if(!$comments):?>
    <p>No comments on this ban yet.</p>
  <?php endif;?>

  <?php foreach ($comments as $comment):?>
    <div class="panel panel-<?php echo $comment->class;?>"
    id="<?php echo $comment->id;?>">
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
        <small>Comment pending approval. <a href="<?php echo $comment->approveHref;?>" class="btn btn-success btn-xs">Approve</a> or <a href="<?php echo $comment->hideHref;?>" class="btn btn-danger btn-xs">Deny</a> </small>

      <?php elseif ('R' == $comment->flagged && 2 <= $user->level):?>
        <small>Comment reported by <?php echo $comment->reporter;?> at <?php echo $comment->reported_time;?>. <a href="<?php echo $comment->hideHref;?>" class="btn btn-danger btn-xs">Remove comment</a> or <a href="<?php echo $comment->approveHref;?>" class="btn btn-success btn-xs">Unflag</a> </small>

      <?php elseif ('A' == $comment->flagged):?>
        <small><a href="<?php echo $comment->reportHref;?>" class="btn btn-danger btn-xs">Report Comment</a></small>

      <?php endif;?>

      </div>
    </div>
  <?php endforeach;?>
    <hr>
    <div class="page-header">
      <h2>Leave a comment</h2>
    </div>
    <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-circle"></i> <strong>Do not conduct official business in ban comments!</strong><br>
    This system is a proof-of-concept and <em>has not been sanctioned by any authority</em>. Any comments left here should be disregarded.</div>

    <?php
      $action = $ban->link;
      require_once(ROOTPATH."/inc/view/addComment.php");
    ?>

  </div>
</div>
<?php require_once('../footer.php');?>