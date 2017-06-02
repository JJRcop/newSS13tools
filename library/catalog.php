<?php require_once('../header.php'); ?>

<?php if (!$user->legit): ?>

  <div class="alert alert-danger">
  You must be a known user to view this page.
  </div>

<?php die(); endif;?>

<?php
$books = new library();
$total = $books->countBooks();
$pages = floor($total/30);
if (isset($_GET['page'])){
  $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT,array(
    'min_range' => 1,
    'max_range' => $pages
  ));
} else {
  $page = 1;
}
$query = null;
if (isset($_GET['query'])){
  $query = filter_input(INPUT_GET, 'query',
    FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_HIGH);
}
?>
<div class="page-header">
  <form class="form-inline pull-right" action="" method="GET">
    <div class="form-group">
      <input type="text" class="form-control" id="query" name="query"
      placeholder="Search term" value="<?php echo $query;?>">
    </div>
    <button type="submit" class="btn btn-primary">Search</button>
  </form>
  <h1>The Library</h1>
</div>

<?php if(!$query):?>
<?php $link = "library/catalog"; include(ROOTPATH."/inc/view/pagination.php");?>
<?php endif;?>

<p class="lead">Welcome to the library. Blank books aren't being shown. I want to die.</p>

<table class="table sticky  table-bordered table-condensed sort">
  <thead>
    <tr>
      <th>NTBN</th>
      <th>Author</th>
      <th>Title</th>
      <th>Category</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $books = $books->getCatalog($page,30,$query);
    foreach ($books as $book) {
      if ($book->category == 'Adult') {
        echo "<tr class='library-adult book' id='$book->id'>";
      } else {
        echo "<tr class='book' id='$book->id'>";
      }
      echo "<td>#$book->id</td>";
      echo "<td>$book->author</td>";
      echo "<td>$book->title</td>";
      echo "<td>$book->category</td>";
      echo "</tr></a>";
    }
    if (!$books){
      echo "<tr><td colspan='4' style='text-align: center'>";
      echo "&laquo; No results &raquo;</td></tr>";
    }
    ?>
  </tbody>
</table>
<script>
$('.book').click(function(e){
  book = $(this).attr('id');
  window.location.href = "viewBook.php?book="+book;
})
</script>
<?php require_once('../footer.php'); ?>