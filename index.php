<?php require_once('header.php'); ?>

<div class="page-header">
  <h1>SS13 tools &amp; stats</h1>
</div>
<?php $num = $app->getBigNumbers();?>
<div class="row">
  <div class="col-md-6">
    <div class="jumbotron">
      <h1>
        <small>Tracking</small><br>
        <?php echo number_format($num['deaths']);?><br>
        <small>deaths</small>
      </h1>
    </div>
  </div>
  <div class="col-md-6">
    <div class="jumbotron">
      <h1>
        <small>Tracking</small><br>
        <?php echo number_format($num['rounds']);?><br>
        <small>rounds</small>
      </h1>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <h2>Recent deaths <small><a href='death/deaths.php'>See more</a></small></h2>
    <ul class="list-unstyled">
      <?php 
      $death = new death();
      $deaths = $death->getDeaths(10,TRUE);
      if(!$deaths){
        echo "<li>No deaths found</li>";
      } else {
        foreach($deaths as $death){
          echo $death->HTML;
        }
      }
      ?>
    </ul>
  </div>
  <div class="col-md-6">
  <h2>Recent rounds <small><a href='rounds/listRounds.php'>See more</a></small></h2>
  </div>
</div>

<?php require_once('footer.php');