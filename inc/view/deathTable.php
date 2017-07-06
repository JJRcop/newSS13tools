<table class="table table-bordered table-condensed">
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
      <?php include('deathRow.php');?>
    <?php endforeach;?>
  </tbody>
</table>