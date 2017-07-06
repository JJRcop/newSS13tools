<?php
$death = new death();
$total = $death->countDeaths();
$pages = floor($total/30);

if (isset($_GET['page'])){
  $page = filter_input(INPUT_GET, 'page',FILTER_VALIDATE_INT,array(
    'min_range' => 1,
    'max_range' => $pages
  ));
} else {
  $page = 1;
}

?>

<div class="page-header">
  <h1>Deaths</h1>
</div>
<?php $link = "death"; include(ROOTPATH."/inc/view/pagination.php");?>
<p class="lead">
  Reported deaths here are one hour delayed.
</p>

<?php
  $deaths = $death->getDeaths(30,false,$page);

  require_once(ROOTPATH."/inc/view/deathTable.php");

$link = "death"; include(ROOTPATH."/inc/view/pagination.php");?>

