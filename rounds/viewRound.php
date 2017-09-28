<?php
if (!isset($_GET['round'])) die("No round ID specified!");
$round = filter_input(INPUT_GET, 'round', FILTER_SANITIZE_NUMBER_INT);
$round = new round($round,array('data','deaths','explosions','antags'));
?>

<?php if (!$round->id):?>
  <div class="alert alert-danger">
    Round not found: #<?php echo $_GET['round'];?>
  </div>
<?php die(); endif;?>

<nav>
  <ul class="pager">
  <?php if ($round->prev): ?>
    <li class="previous">
        <a href="<?php echo $app->APP_URL;?>round.php?round=<?php echo $round->prev;?>">
        <span aria-hidden="true">&larr;</span>
        Previous round</a>
    </li>
  <?php endif;?>

  <li><a href="<?php echo $app->APP_URL;?>round.php">
    <i class="fa fa-list"></i> Round listing</a>
  </li>

  <?php if ($round->next): ?>
    <li class="next">
      <a href="<?php echo $app->APP_URL;?>round.php?round=<?php echo $round->next;?>">Next round
      <span aria-hidden="true">&rarr;</span></a>
    </li>
  <?php endif;?>
  </ul>
</nav>

<div class="page-header">
  <h1><?php echo $round->title;?></h1>
</div>

<?php if($round->rare):?>
  <div class="alert alert-warning">
    <?php echo iconStack('certificate','trophy','fa-inverse', TRUE);?>
  <strong>RARE ENDING</strong></div>
<?php endif;?>

<table class="table table-bordered table-condensed">
  <tr>
    <th>Game Mode</th>
    <td><?php echo $round->modeIcon.ucfirst($round->game_mode);?></td>
    <th>Game Mode Result</th>
    <td class="<?php echo $round->statusClass;?>"><?php echo "$round->statusIcon $round->result";?></td>
  </tr>
  <tr>
    <th>Round Start</th>
    <td><?php echo $round->start;?></td>
    <th>Server End Status</th>
    <td><?php echo $round->end_state;?></td>
  </tr>
  <tr>
    <th>Round End</th>
    <td><?php echo $round->end;?></td>
    <th>Server</th>
    <td><?php echo $round->server;?></td>
  </tr>
  <tr>
    <th>Round Duration</th>
    <td><?php echo $round->duration;?></td>
    <th>Logs available</th>
    <td id="logStatus">
      <em>Checking...</em>
    </td>
  </tr>
  <tr>
    <th>Map Name</th>
    <td><?php echo $round->map;?></td>
    <th>Server Revision</th>
    <td><?php echo $round->commit_link;?></td>
  </tr>
  <tr>
    <th>Escape Shuttle</th>
    <td><?php echo $round->shuttle;?></td>
    <th>Station Name</th>
    <td><?php echo $round->station_name;?></td>
  </tr>
  </table>

  <table class="table sticky  table-bordered table-condensed">
  <?php if(isset($round->data->testmerged_prs)):?>
    <tr><th colspan="2">Testmerged PRs</th>
    <td colspan="2"><?php
    $stat = $round->data->testmerged_prs;
    foreach ($stat->details as $k => $v){
      if (strpos($k, '|')){
        $k = explode('|', $k);
        $k = str_replace('"', '', $k[0]);
      }
      echo "<a target='_blank'";
      echo "href='https://github.com/".PROJECT_GITHUB."/issues/$k'>#$k</a> ";
    }?>
    </td></tr>
  <?php endif;?>

  <?php if(isset($round->data->shuttle_reason)):?>
    <tr><th colspan="2">Escape Shuttle Called for Reason(s)</th>
      <td colspan="2">
        <?php
        $stat = $round->data->shuttle_reason;
        if(is_array($stat->details)):
          echo implode($stat->details,"<br>");
         else:?>
          <?php echo str_replace('_', ' ', $stat->details);?>
        <?php endif;?>
         <i class="fa fa-check" data-toggle="tooltip" title="Shuttle Call Successful"></i>
      </td>
    </tr>
  <?php endif;?>

  <?php if(isset($round->data->shuttle_purchase)):
  $data = $round->data->shuttle_purchase->details;?>
    <tr>
      <th colspan="2">Shuttle Purchased</th>
      <td colspan="2"><?php echo str_replace('_',' ',$data);?></td>
    </tr>
  <?php endif;?>
</table>
<hr>

<?php
$dead = 0;
$endClients = 0;
$survivors = 0;
$survivingHumans = 0;
$escaped = 0;
$escapedH = 0;
if(isset($round->data->round_end_clients)) $endClients = $round->data->round_end_clients->var_value;
if(isset($round->data->round_end_ghosts)) $dead = $round->data->round_end_ghosts->var_value;
if(isset($round->data->survived_total)) $survivors = $round->data->survived_total->var_value;
if(isset($round->data->survived_human)) $survivingHumans = $round->data->survived_human->var_value;
if(isset($round->data->escaped_total)) $escaped = $round->data->escaped_total->var_value;
if(isset($round->data->escaped_human)) $escapedH = $round->data->escaped_human->var_value;
$escaped = $survivors - $escaped;
$total = $dead + $survivors; ?>

<div class="progress">
  <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo ($dead/$total)*100;?>%;">
  <span><?php echo $dead;?> dead</span>
  </div>
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo (($survivors-$escaped)/$total)*100;?>%;">
  <span><?php echo $survivors;?> survivors</span>
  </div>
  <?php if ($escaped):?>
  <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo ($escaped/$total)*100;?>%;">
  <span><?php echo $escaped;?> escaped alive</span>
  </div>
  <?php endif;?>
</div>
<small><p class="text-muted text-right">(This is an approximation and may not reflect actual station population)</p></small>


<div class="row">
  <?php if(isset($round->data->traitor_objective)):?>
    <div class="col-md-4">
    <div class="page-header">
      <h2>Traitor Objectives</h2>
    </div><?php
    $stat = $round->data->traitor_objective;
    $smol = true;
    include(ROOTPATH.'/stats/statspages/'.$stat->include.'.php');?>
    </div>
  <?php endif;?>

  <?php if(isset($round->data->changeling_objective)):?>
    <div class="col-md-4">
    <div class="page-header">
      <h2>Changeling Objectives</h2>
    </div>
    <?php
    $stat = $round->data->changeling_objective;
    $smol = true;
    include(ROOTPATH.'/stats/statspages/'.$stat->include.'.php');?>
    </div>
  <?php endif;?>

  <?php if(isset($round->data->wizard_objective)):?>
    <div class="col-md-4">
    <div class="page-header">
      <h2>Wizard Objectives</h2>
    </div><?php
    $stat = $round->data->wizard_objective;
    $smol = true;
    include(ROOTPATH.'/stats/statspages/'.$stat->include.'.php');?>
    </div>
  <?php endif;?>
</div>

<?php if ($user->legit && isset($round->data->commendation)):?>
  <div class="page-header">
    <h2>Commendations</h2>
  </div>
  <?php $stat = $round->data->commendation;
  $smol = true;?>
  <?php include(ROOTPATH.'/stats/statspages/'.$stat->include.'.php');?>
<?php endif;?>


<?php if($round->deaths):?>
  <div class="page-header">
    <h2><a data-toggle="collapse" href="#deaths" aria-expanded="false"
     aria-controls="deaths">Deaths from this round</a></h2>
  </div>
  <div id="deaths" class="collapse">
    <?php $deaths = $round->deaths;
    include(ROOTPATH."/inc/view/deathTable.php");?>
    <hr>
  </div>
<?php endif;?>

<?php if($round->explosions):?>
  <div class="page-header">
    <h2><img src='<?php echo $app->APP_URL;?>icons/obj/assemblies/syndicate-bomb-active.png' /> <a data-toggle="collapse" href="#explosions" aria-expanded="false"
     aria-controls="explosions">Explosions from this round</a></h2>
  </div>
    <div id="explosions" class="collapse">
      <table class="table table-bordered table-condensed sort">
        <thead>
          <tr>
            <th>Devestation</th>
            <th>Heavy</th>
            <th>Light</th>
            <th>Flash</th>
            <th>Location</th>
            <th>X</th>
            <th>Y</th>
            <th>Z</th>
            <th>Time</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($round->explosions as $e):?>
            <tr>
              <td><?php echo $e->devestation;?></td>
              <td><?php echo $e->heavy;?></td>
              <td><?php echo $e->light;?></td>
              <td><?php echo $e->flash;?></td>
              <td><?php echo $e->area;?></td>
              <td><?php echo $e->x;?></td>
              <td><?php echo $e->y;?></td>
              <td><?php echo $e->z;?></td>
              <td><?php echo $e->time;?></td>
            </tr>
          <?php endforeach;?>
        </tbody>
      </table>
      <hr>
    </div>
<?php endif;?>

<?php if($user->legit && $round->antags):?>
  <div class="page-header">
    <h2><a data-toggle="collapse" href="#antags" aria-expanded="false"
     aria-controls="antags">Antagonists from this round</a></h2>
  </div>
    <div id="antags" class="collapse">
    <?php foreach($round->antags as $a):?>
      <dl class="dl-horizontal">
        <dt><?php echo ucfirst($a->role);?></dt>
        <dd><?php echo $a->antags;?></dd>
      </dl>
    <?php endforeach;?>
      <hr>
    </div>
<?php endif;?>

<?php if ($user->legit):?>
<?php include(ROOTPATH."/rounds/comments.php");?>
<?php endif;?>

<hr>

<div id="rawdata">
  <h3>Raw data</h3>
  <?php if(!$round->data):?>
    <div class="alert alert-info">No stats found for this round</div>
  <?php else:?>
    <ul class="list-inline">
      <?php foreach ($round->data as $d) :?>
        <li>
          <code>
            <a href='<?php echo $app->APP_URL;?>round.php?stat=<?php echo $d->var_name;?>&round=<?php echo $round->id;?>'>
              <?php echo $d->var_name;?>
            </a>
          </code>
        </li>
      <?php endforeach;?>
    </ul>
  <?php endif;?>
</div>

<nav>
  <ul class="pager">
  <?php if ($round->prev): ?>
    <li class="previous">
        <a href="round.php?round=<?php echo $round->prev;?>">
        <span aria-hidden="true">&larr;</span>
        Previous round</a>
    </li>
  <?php endif;?>

  <li><a href="<?php echo $app->APP_URL;?>/round.php">
    <i class="fa fa-list"></i> Round listing</a>
  </li>

  <?php if ($round->next): ?>
    <li class="next">
      <a href="round.php?round=<?php echo $round->next;?>">Next round
      <span aria-hidden="true">&rarr;</span></a>
    </li>
  <?php endif;?>
  </ul>
</nav>

<script>
$(document).ready(function() {
  var viewBtn = "<a href='<?php echo $round->href;?>&logs=true' class='btn btn-success btn-xs'>View</a>";
  $.ajax({
    url: '<?php echo APP_URL."/auto/getRoundLogStatus.php";?>',
    data: {
      round: <?php echo $round->id;?>
    },
    method: 'GET'
  })
  .success(function(e){
    console.log(e);
    if(true == e.status){
      var html = "<strong>Yes</strong> "+viewBtn;
      $('#logStatus').toggleClass('success').html(html);
    } else {
      var generateBtn = "<strong>Not yet</strong> <span id='generate' class='btn btn-success btn-xs'>Generate</span>";
      var html = generateBtn;
      $('#logStatus').toggleClass('warning').html(html);
    }
  });
  $(document).on('click', '#generate', function(e){
    e.preventDefault();
    console.log(e);
    var html = "<i class='fa fa-refresh fa-spin'></i> Generating logs...";
    $('#logStatus').html(html);
    $.ajax({
      url: '<?php echo APP_URL."/auto/generateRoundLogs.php";?>',
      data: {
        round: <?php echo $round->id;?>
      },
      method: 'GET',
      timeout: 0
    })
    .success(function(d){
      var html = d.message + ' ' + viewBtn;
      $('#logStatus').toggleClass('warning').toggleClass('success').html(html);
    });
  });
});
</script>
