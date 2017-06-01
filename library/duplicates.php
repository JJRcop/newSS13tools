<?php require_once('../header.php'); ?>
<?php if (!$user->legit): ?>

  <div class="alert alert-danger">
  You must be a known user to view this page.
  </div>

<?php die(); endif;?>
<?php
$books = new library();
?>
<div class="page-header">
  <h1>Duplicate book listing</h1>
</div>
<table class="table sticky  table-bordered table-condensed sort">
  <thead>
    <tr>
      <th>Count</th>
      <th>NTBNs</th>
      <th>Title</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $books = $books->getDuplicates();
    foreach ($books as $book) {
      if ($book->count <= 1) continue;
      $book->ids = explode(',', $book->ids);
      foreach ($book->ids as &$b){
        $b = "<a href='viewBook.php?book=$b'>$b</a>";
      }
      echo "<tr>";
      echo "<td>$book->count</td>";
      echo "<td>".implode($book->ids,', ')."</td>";
      echo "<td>$book->title</td>";
      echo "</tr></a>";
    }
    if (!$books){
      echo "<tr><td colspan='3' style='text-align: center'>";
      echo "&laquo; No results &raquo;</td></tr>";
    }
    ?>
  </tbody>
</table>
<?php require_once('../footer.php'); ?>