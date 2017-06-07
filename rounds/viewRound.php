<?php
if (!isset($_GET['round'])) die("No round ID specified!");
$round = filter_input(INPUT_GET, 'round', FILTER_SANITIZE_NUMBER_INT);
$round = new round($round,array('data','deaths'));

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
  <h1>Round #<?php echo $round->id;?></h1>
</div>

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
    <td><?php echo $round->logs;?></td>
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
      echo "<a target='_blank'";
      echo "href='https://github.com/".PROJECT_GITHUB."/issues/$k'>#$k</a> ";
    }?>
    </td></tr>
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

<?php if($round->rare):?>
  <div class="alert alert-warning"><?php echo iconStack('certificate','trophy','fa-inverse', TRUE);?>
  <strong>RARE ENDING</strong></div>
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


<?php if($round->deaths):?>
  <div class="page-header">
    <h2><a data-toggle="collapse" href="#deaths" aria-expanded="false"
     aria-controls="deaths">Deaths from this round</a></h2>
  </div>
  <table id="deaths" class="table table-bordered table-condensed collapse ">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Job</th>
        <th>Location & Map</th>
        <th>Damage & Attacker (if set)<br>
          <span title="Brute" class="label label-dam label-brute">BRU</span>
          <span title="Brain" class="label label-dam label-brain">BRA</span>
          <span title="Fire" class="label label-dam label-fire">FIR</span>
          <span title="Oxygen" class="label label-dam label-oxy">OXY</span>
          <span title="Toxin" class="label label-dam label-tox">TOX</span>
          <span title="Clone" class="label label-dam label-clone">CLN</span>
          <span title="Stamina" class="label label-dam label-stamina">STM</span>
        </th>
        <th>Time & Server</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($round->deaths as $d):?>
        <tr data-id="<?php echo $d->id;?>" class="<?php echo $d->server;?>">
          <td><?php echo $d->link;?></td>
          <td><?php echo "$d->name<br><small>$d->byondkey</small>";?></td>
          <td><?php echo "$d->job <br><small class='text-danger'>".ucfirst($d->special)."</small>";?></td>
          <td><?php echo "$d->pod <br><small>$d->mapname  ($d->coord)</small>";?></td>
          <td>
            <?php echo $d->labels;?><br>
            <?php if('' != $d->laname):?>
              <?php echo "By $d->laname <small>($d->lakey)</small>";?>        <?php if($d->suicide) echo " <small class='text-danger'>(Probable Suicide)</small>";?>
            <?php endif;?>
          </td>
          <td><?php echo "$d->tod";?></td>
        </tr>
      <?php endforeach;?>
    </tbody>
  </table>
<?php endif;?>

<hr>

<?php if ($user->legit):?>
<?php include(ROOTPATH."/rounds/comments.php");?>
<?php endif;?>

<hr>
<div id="rawdata">
  <h3>Raw data</h3>
  <ul class="list-unstyled">
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

  <li><a href="listRounds.php">
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
