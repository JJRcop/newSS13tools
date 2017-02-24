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
    $db->query("SELECT ss13library.*,
      MAX(next.id) AS `next`,
      MIN(prev.id) AS `prev`,
      next.title AS nexttitle,
      prev.title AS prevtitle,
      prev.category AS prevcat,
      next.category AS nextcat
      FROM ss13library
      LEFT JOIN ss13library AS `next` ON next.id = ss13library.id + 1
      LEFT JOIN ss13library AS `prev` ON prev.id = ss13library.id - 1
      WHERE ss13library.id= ?");
    $db->bind(1,$book);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }

  public function parseBook(&$book){
    switch ($book->category) {
      case 'Adult':
        $book->class = 'danger';
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
      FROM ss13library
      LEFT JOIN ss13library AS `next` ON next.id = ss13library.id + 1
      LEFT JOIN ss13library AS `prev` ON prev.id = ss13library.id - 1
      WHERE ss13library.id=?");
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
    return $db->resultset();
  }

  public function countBooks(){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT count(DISTINCT id) AS total
      FROM ss13library
      WHERE content != ''");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single()->total;
  }
}