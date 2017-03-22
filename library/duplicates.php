<?php require_once('../header.php'); ?>
<?php require_once('../user_check.php'); ?>
<?php
$books = new library();
?>
<div class="page-header">
  <h1>Duplicate book listing</h1>
</div>
<table class="table table-bordered table-condensed sort">
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