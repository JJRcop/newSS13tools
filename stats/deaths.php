<?php require_once('../header.php'); ?>

<div class="page-header">
  <h1>Deaths</h1>
</div>

<p class="lead">
  This shows the last 30 deaths without accounting for which server the death occurred on. Additionally, the map the death occurred on is not tracked.
</p>

<table class="table table-bordered table-condensed">
  <thead>
    <tr>
      <th>Name</th>
      <th>Job</th>
      <th>Time of Death</th>
      <th>Location</th>
      <th>Coordinates</th>
      <th>Damage</th>
      <th>Special</th>
    </tr>
  </thead>
  <tbody>
<?php 

$death = new death();
foreach($death->getDeaths() as $death){
  echo $death->HTML;
}
?>
  </tbody>
</table>

<?php require_once('../footer.php'); ?>