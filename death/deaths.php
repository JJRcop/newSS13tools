<?php require_once('../header.php'); ?>

<div class="page-header">
  <h1>Deaths</h1>
</div>

<p class="lead">
Reported deaths here are one hour delayed.
</p> 

<?php
  $death = new death();
  $deaths = $death->getDeaths();
?>
<table id="deaths" class="table table-bordered table-condense">
  <thead>
    <tr>
      <th>Name</th>
      <th>Job</th>
      <th>Location & Map</th>
      <th>Damage & Attacker (if set)<br>
        <span title="Brute" class="label label-brute">BRU</span>
        <span title="Brain" class="label label-brain">BRA</span>
        <span title="Fire" class="label label-fire">FIR</span>
        <span title="Oxygen" class="label label-oxy">OXY</span>
        <span title="Toxin" class="label label-tox">TOX</span>
        <span title="Clone" class="label label-clone">CLN</span>
        <span title="Stamina" class="label label-stamina">STM</span>
      </th>
      <th>Time & Server</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($deaths as $d):?>
      <?php $d = $death->parseDeath($d);?>
      <tr data-id="<?php echo $d->id;?>" class="<?php echo $d->server_port;?>">
        <td><?php echo "$d->name<br><small>$d->byondkey</small>";?></td>
        <td><?php echo "$d->job <br><small class='text-danger'>".ucfirst($d->special)."</small>";?></td>
        <td><?php echo "$d->pod <br><small>$d->mapname  ($d->coord)</small>";?></td>
        <td>
          <span title="Brute" class="label label-brute"><?php echo $d->bruteloss;?></span>
          <span title="Brain" class="label label-brain"><?php echo $d->brainloss;?></span>
          <span title="Fire" class="label label-fire"><?php echo $d->fireloss;?></span>
          <span title="Oxygen" class="label label-oxy"><?php echo $d->oxyloss;?></span>
          <span title="Toxin" class="label label-tox"><?php echo $d->toxloss;?></span>
          <span title="Clone" class="label label-clone"><?php echo $d->cloneloss;?></span>
          <span title="Stamina" class="label label-stamina"><?php echo $d->staminaloss;?></span><br>
          <?php if('' != $d->laname):?>
            <?php echo "By $d->laname <small>($d->lakey)</small>";?>        <?php if($d->suicide) echo " <small class='text-danger'>(Probable Suicide)</small>";?>
          <?php endif;?>
        </td>
        <td><?php echo "$d->tod";?></td>
      </tr>
    <?php endforeach;?>
  </tbody>
</table>

<?php require_once('../footer.php'); ?>