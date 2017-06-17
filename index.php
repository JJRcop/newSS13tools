<?php require_once('header.php');?>
<div class="page-header">
  <h1>SS13 Tools &amp; Stats</h1>
</div>
<?php if ($user->legit):?>
  <div class="alert alert-success">
    <strong>NEW!</strong> Comments can now be left on individual rounds!
  </div>
  <?php if($user->tgui):?>
    <div class="alert alert-info">
      <strong>EXPERIMENTAL!</strong> You are using an experimental UI theme. <a href='?tgui=false'>Disable!</a> <em>You may have to refresh in order to see changes</em>
    </div>
  <?php else:?>
    <div class="alert alert-info">
      <strong>EXPERIMENTAL!</strong> Use the in-game tgui theme? <a href='?tgui=true'>Sure!</a> <em>You may have to refresh in order to see changes</em>
    </div>
  <?php endif;?>
  <?php $line = pick(json_decode(file_get_contents(ROOTPATH."/tmp/poly.json"),TRUE)['data']);?>
  <div id="poly" class="engradio">[Poly] &ldquo;<?php echo $line;?>&rdquo; <img src="icons/mob/animal/parrot_sit-1.png" height="64" width="64"  alt="And now a word from Poly" /></div>
<?php else:?>

<div class="alert alert-warning">
<strong>Disclaimer</strong><br>
In not so many words, some of the data in this tool may be offensive. I do not claim any responsibility for this data; I am simply analyzing and reporting it.
</div>

<?php endif;?>

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
    <h2>Recent deaths <small><a href='death.php'>See more</a></small></h2>
    <ul class="list-unstyled">
      <?php 
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

  <h2>Recent rounds <small><a href="<?php echo $app->APP_URL;?>round.php">See more</a></small></h2>
  <?php
    $round = new round();
    $rounds = $round->listRounds(1,10);
  ?>
  <ul class="list-unstyled">
  <?php
  if($rounds):
  foreach ($rounds as $round):?>
  <li>
    <?php echo $round->link;?>, a round of <?php echo $round->game_mode;?> that lasted <?php echo $round->duration;?>
  <?php endforeach; endif;?>
  </ul>
  </div>
</div>

<?php require_once('footer.php');