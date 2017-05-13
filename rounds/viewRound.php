<?php
$json = false;
if (isset($_GET['json'])) $json = filter_input(INPUT_GET, 'json', FILTER_VALIDATE_BOOLEAN);
if (!isset($_GET['round'])) die("No round ID specified!");
$round = filter_input(INPUT_GET, 'round', FILTER_SANITIZE_NUMBER_INT);

require_once('../header.php');
$round = new round($round,array('data'));
// var_dump($round);
?>

<?php if (!$round->round_id):?>
  <div class="alert alert-danger">
    Round not found: #<?php echo $_GET['round'];?>
  </div>
<?php die(); endif;?>

<nav>
  <ul class="pager">
  <?php if ($round->prev): ?>
    <li class="previous">
        <a href="viewRound.php?round=<?php echo $round->prev;?>">
        <span aria-hidden="true">&larr;</span>
        Previous round</a>
    </li>
  <?php endif;?>

  <li><a href="listRounds.php">
    <i class="fa fa-list"></i> Round listing</a>
  </li>

  <?php if ($round->next): ?>
    <li class="next">
      <a href="viewRound.php?round=<?php echo $round->next;?>">Next round
      <span aria-hidden="true">&rarr;</span></a>
    </li>
  <?php endif;?>
  </ul>
</nav>

<div class="page-header">
  <h1>Round #<?php echo $round->round_id;?></h1>
</div>

<table class="table table-bordered table-condensed">
  <tr>
    <th>Game Mode</th>
    <td><?php echo $round->modeIcon.ucfirst($round->game_mode);?></td>
    <th>Result</th>
    <td><?php echo $round->result;?></td>
  </tr>
  <tr>
    <th>Start</th>
    <td><?php echo $round->start;?></td>
    <th>Ending Status</th>
    <td><?php echo $round->status;?></td>
  </tr>
  <tr>
    <th>End</th>
    <td><?php echo $round->end;?></td>
    <th>Server</th>
    <td><?php echo $round->server;?></td>
  </tr>
  <tr>
    <th>Duration</th>
    <td><?php echo $round->duration;?></td>
    <th>Logs available</th>
    <td><?php echo ($round->logs)?"Yes (EVENTUALLY)":"No";?></td>
  </tr>
  </table>
  
  <table class="table table-bordered table-condensed">

  <?php if(isset($round->data->map_name)):
  $data = $round->data->map_name->details;?>
    <tr>
      <th colspan="2">Map</th>
      <td colspan="2"><?php echo str_replace('_',' ',$data);?></td>
    </tr>
  <?php endif;?>

  <?php if(isset($round->data->testmerged_prs)):?>
    <tr><th colspan="2">Testmerged PRs</th>
    <td colspan="2"><?php
    $stat = $round->data->testmerged_prs;
    foreach ($stat->details as $k => $v){
      echo "<a target='_blank'";
      echo "href='https://github.com/".PROJECT_GITHUB."/issues/$k'>#$k</a> ";
    }?>
    </td></tr>
  <?php endif;?>

  <?php if(isset($round->data->station_renames)):
  $data = $round->data->station_renames->details;?>
    <tr>
      <th colspan="2">Station Name</th>
      <td colspan="2"><?php echo $data;?></td>
    </tr>
  <?php endif;?>

  <?php if(isset($round->data->emergency_shuttle)):
  $data = $round->data->emergency_shuttle->details;?>
    <tr>
      <th colspan="2">Emergency Shuttle</th>
      <td colspan="2"><?php echo str_replace('_',' ',$data);?></td>
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

<hr>

<?php if($round->game_mode != 'nuclear emergency' && $round->status == 'Nuke'):?>
  <div class="alert alert-warning"><?php echo iconStack('certificate','trophy','fa-inverse', TRUE);?> <strong>RARE ENDING</strong></div>
<?php endif;?>

<?php if($round->status == 'Restart vote'):?>
  <div class="alert alert-success"><?php echo iconStack('certificate','hand-paper-o','fa-inverse', TRUE);?> <strong>RARE VOTE</strong> Democracy works!</div>
<?php endif;?>

<div class="row">

  <?php if(isset($round->data->traitor_objective)):?>
    <div class="col-md-4"><?php
    $stat = $round->data->traitor_objective;
    $smol = true;
    include(ROOTPATH.'/stats/statspages/'.$stat->include.'.php');?>
    </div>
  <?php endif;?>

  <?php if(isset($round->data->changeling_objective)):?>
    <div class="col-md-4"><?php
    $stat = $round->data->changeling_objective;
    $smol = true;
    include(ROOTPATH.'/stats/statspages/'.$stat->include.'.php');?>
    </div>
  <?php endif;?>

  <?php if(isset($round->data->wizard_objective)):?>
    <div class="col-md-4"><?php
    $stat = $round->data->wizard_objective;
    $smol = true;
    include(ROOTPATH.'/stats/statspages/'.$stat->include.'.php');?>
    </div>
  <?php endif;?>
</div>

<div id="rawdata">
  <h3>Raw data</h3>
  <ul class="list-unstyled">
    <?php foreach ($round->data as $d) :?>
      <li>
        <code>
          <a href='<?php echo APP_URL;?>rounds/viewRoundStat.php?stat=<?php echo $d->var_name;?>&round=<?php echo $round->round_id;?>'>
            <?php echo $d->var_name;?>
          </a>
        </code>
      </li>
    <?php endforeach;?>
    </ul>
</div>

<nav>
  <ul class="pager">
  <?php if ($round->prev): ?>
    <li class="previous">
        <a href="viewRound.php?round=<?php echo $round->prev;?>">
        <span aria-hidden="true">&larr;</span>
        Previous round</a>
    </li>
  <?php endif;?>

  <li><a href="listRounds.php">
    <i class="fa fa-list"></i> Round listing</a>
  </li>

  <?php if ($round->next): ?>
    <li class="next">
      <a href="viewRound.php?round=<?php echo $round->next;?>">Next round
      <span aria-hidden="true">&rarr;</span></a>
    </li>
  <?php endif;?>
  </ul>
</nav>

<?php require_once('../footer.php'); ?>