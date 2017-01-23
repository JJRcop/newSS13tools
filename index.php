<?php require_once('header.php'); ?>

<div class="page-header">
  <h1>SS13 tools & stats</h1>
</div>

<div class="row">
  <div class="col-md-6">
    <h2>Recent deaths</h2>
    <ul class="list-unstyled">
      <?php 
      $death = new death();
      foreach($death->getDeaths(10,TRUE) as $death){
        echo $death->HTML;
      }
      ?>
    </ul>
    <p class="pull-right"><a href='stats/deaths.php'>See more...</a></p>
  </div>
</div>

<?php require_once('footer.php');