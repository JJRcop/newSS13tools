<?php

$json = false;
if (isset($_GET['json'])) $json = filter_input(INPUT_GET, 'json', FILTER_VALIDATE_BOOLEAN);
if ($json){
  require_once('../config.php');

  $death = new death();
  echo $death->liveDeaths();

  return;
}
?>

<?php require_once("../header.php");?>
<?php require_once('tgdb_nav.php');?>

<div class="page-header">
  <h1>Live Deaths</h1>
</div>

<div class="row">
  <div class="col-md-12">
    <table class="table table-bordered table-condensed">
      <thead>
        <tr>
          <th>Name</th>
          <th>Job</th>
          <th>Time of Death</th>
          <th>Location</th>
          <th>Coordinates</th>
          <th>Brute</th>
          <th>Brain</th>
          <th>Fire</th>
          <th>Oxy</th>
          <th>Special</th>
        </tr>
      </thead>
      <tbody>
    <?php 

    $death = new death();
    $deaths = $death->getDeaths();
    if(!$deaths){
      echo "<tr><td colspan='10'>No deaths found</td></tr>";
    } else {
      foreach($deaths as $death){
        echo $death->HTML;
      }
    }
    ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once('../footer.php');?>