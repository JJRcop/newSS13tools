<?php

class library {

  public $id;
  public $author;
  public $title;
  public $content;
  public $category;
  public $ckey;
  public $datetime;

  public function __construct($book=null){
    if ($book){
    $book = $this->getBook($book);
    $book = $this->parseBook($book);
      foreach ($book as $key => $value){
        $this->$key = $value;
      }
    }
  }

  public function getBook($book) {
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT tbl_library.*,
      MAX(next.id) AS `next`,
      MIN(prev.id) AS `prev`,
      next.title AS nexttitle,
      prev.title AS prevtitle,
      prev.category AS prevcat,
      next.category AS nextcat
      FROM tbl_library
      LEFT JOIN tbl_library AS `next` ON next.id = tbl_library.id + 1
      LEFT JOIN tbl_library AS `prev` ON prev.id = tbl_library.id - 1
      WHERE tbl_library.id= ?");
    $db->bind(1,$book);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }

  public function parseBook(&$book){
    $app = new app();
    //Link
    $book->href = $app->APP_URL."/library/viewBook.php?book=$book->id";
    $book->link = "<a href='$book->href'><i class='fa fa-book'></i> $book->id</a>";

    //Category
    switch ($book->category) {
      case 'Adult':
        $book->class = 'danger library-adult';
        $book->label = "ADULT";
      break;

      case 'Reference':
        $book->class = 'info';
        $book->label = "$book->category";
      break;

      case 'Fiction':
        $book->class = 'success';
        $book->label = "$book->category";
      break;

      case 'Non-Fiction':
        $book->class = 'default';
        $book->label = "$book->category";
      break;

      default:
        $book->class = 'default';
        $book->label = "$book->category";
      break;
    }
    return $book;
  }

  public function getBookNeighbors($book){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT MAX(next.id) AS `next`,
      MIN(prev.id) AS `prev`,
      next.title AS nexttitle,
      prev.title AS prevtitle,
      prev.category AS prevcat,
      next.category AS nextcat
      FROM tbl_library
      LEFT JOIN tbl_library AS `next` ON next.id = tbl_library.id + 1
      LEFT JOIN tbl_library AS `prev` ON prev.id = tbl_library.id - 1
      WHERE tbl_library.id=?");
    $db->bind(1,$book);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }

  public function getCatalog($page=1, $count=30, $query=null){
    $page = ($page*$count) - $count;
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    if ($query){
      $query = "AND content LIKE '%$query%'";
    }
    $db->query("SELECT id, author, title, category
      FROM tbl_library
      WHERE content != ''
      $query
      ORDER BY `datetime` DESC
      LIMIT ?,?");
    $db->bind(1,$page);
    $db->bind(2,$count);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $books = $db->resultset();
    foreach ($books as $book){
      $book = $this->parseBook($book);
    }
    return $books;
  }

  public function getDuplicates(){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT count(DISTINCT id) AS count, group_concat(id) as ids, title
      FROM tbl_library
      WHERE content != ''
      GROUP BY content
      ORDER BY count DESC;");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }

  public function countBooks(){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT count(DISTINCT id) AS total
      FROM tbl_library
      WHERE content != ''");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single()->total;
  }

  public function flagBook($book){
    $db = new database(TRUE);
    if($db->abort){
      return FALSE;
    }
    $user = new user();
    if($user->level < 2){
      die("You do not have the proper access credentials to flag books.");
    }
    $book = new library($book);
    $db->query("INSERT INTO f451 (book, flagger, `date`) VALUES (?, ?, NOW())");
    $db->bind(1,$book->id);
    $db->bind(2,$user->ckey);
    try {
      $db->execute();
    } catch (Exception $e) {
      return alert("Database error: ".$e->getMessage(),FALSE);
    }
    return alert("$book->title has been flagged for deletion.",0);
  }

  public function bb2HTML($bbcode){
    if(empty(trim($bbcode))) return false;
    // $bbcode = nl2br($bbcode);
    $bbcode = preg_replace("/(\[)(\/)?(center|large|list|u|B|b|small|i)(\])/","<$2$3>",$bbcode);
    $bbcode = preg_replace("/(\[)(br|hr|field)(\])/","<$2>",$bbcode);
    $bbcode = str_replace('<large>', "<font size='4'>", $bbcode);
    $bbcode = str_replace('</large>', "</font>", $bbcode);
    $bbcode = str_replace('<list>', "\n<ul class='list-unstyled'>", $bbcode);
    $bbcode = str_replace('</list>', "</ul>\n", $bbcode);
    $bbcode = str_replace('<field>', "<input />", $bbcode);
    $bbcode = str_replace('</field>', "", $bbcode);
    $bbcode = str_replace('<br>', "<br>\n", $bbcode);
    $bbcode = str_replace('[sign]', "<em>[signature line]</em>", $bbcode);
    $bbcode = str_replace('[tab]', "&nbsp;&nbsp;&nbsp;&nbsp;", $bbcode);
    $bbcode = preg_replace("/(\[)(\*)(\])(.*)(\n)/m","<li>$4</li>\n",$bbcode);
    // $bbcode = nl2br($bbcode);
    return $bbcode;
  }
}