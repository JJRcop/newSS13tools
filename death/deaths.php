<?php
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
  $death = new death();
  $deaths = $death->getDeaths(30,false,$page);
?>
<table id="deaths" class="table sticky  table-bordered table-condense">
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
    <?php foreach ($deaths as $d):?>
      <?php $d = $death->parseDeath($d);?>
      <tr data-id="<?php echo $d->id;?>" class="<?php echo $d->server_port;?>">
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
<?php $link = "death"; include(ROOTPATH."/inc/view/pagination.php");?>
