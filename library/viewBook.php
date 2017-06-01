<?php require_once('../header.php'); ?>

<?php if (!$user->legit): ?>

  <div class="alert alert-danger">
  You must be a known user to view this page.
  </div>

<?php die(); endif;?>

<?php
if (!isset($_GET['book'])) die("No book specified!");
$book = filter_input(INPUT_GET, 'book', FILTER_SANITIZE_NUMBER_INT);
$book = new library($book,TRUE);
$flag = false;
$flag = filter_input(INPUT_GET, 'flag', FILTER_VALIDATE_BOOLEAN);
if($flag) echo $book->flagBook($book->id);

?>

<nav>
  <ul class="pager">
  <?php if ($book->prev): ?>
    <?php if ('Adult' == $book->prevcat):?>
      <li class="previous danger"><a href="viewBook.php?book=<?php echo $book->prev;?>"><span aria-hidden="true">&larr;</span> [Adult Content]</a></li>
    <?php else:?>
      <li class="previous"><a href="viewBook.php?book=<?php echo $book->prev;?>"><span aria-hidden="true">&larr;</span> <?php echo $book->prevtitle;?></a></li>
    <?php endif;?>
  <?php endif;?>
  <li><a href="catalog.php">&uarr; Catalog</a></li>
  <?php if ($user->legit):?>
    <li><a href="viewBook.php?book=<?php echo $book->id;?>&flag=true"><span class="glyphicon glyphicon-flag"></span> Flag for deletion</a></li>
  <?php endif;?>

  <?php if ($book->next): ?>
    <?php if ('Adult' == $book->nextcat):?>
      <li class="next danger"><a href="viewBook.php?book=<?php echo $book->next;?>">[Adult Content] <span aria-hidden="true">&rarr;</span></a></li>
    <?php else:?>
      <li class="next"><a href="viewBook.php?book=<?php echo $book->next;?>"><?php echo $book->nexttitle;?> <span aria-hidden="true">&rarr;</span></a></li>
    <?php endif;?>
  <?php endif;?>
  </ul>
</nav>

<div class="panel panel-<?php echo $book->class;?>">
  <div class="panel-heading">
    <p class="pull-right"><?php echo $book->label;?></p>
    <h3 class="panel-title"><?php echo $book->title;?> <small>by <?php echo $book->author;?></small></h3>
  </div>
  <div class="panel-body">
    <?php echo $book->content;?>
    </font></i></i> <!-- to catch any unclosed font tags -->
  </div>
  <div class="panel-footer">
  <?php if (2 <= $user->level):?>
    <p class="pull-right">
      (Actually published by <a href="<?php echo APP_URL;?>/tgdb/viewPlayer.php?ckey=<?php echo $book->ckey;?>">
      <?php echo $book->ckey;?></a>)
    </p>
    <?php endif;?>
    Published <?php echo $book->datetime;?>
  </div>
</div>

<?php require_once('../footer.php'); ?>