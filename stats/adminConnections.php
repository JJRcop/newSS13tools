<?php require_once('../header.php') ;?>
<?php
$options = array(
  'options'=>array(
    'default'=>30,
    'min_range'=>2,
    'max_range'=>90
));

$interval = filter_input(INPUT_GET, 'interval', FILTER_VALIDATE_INT, $options);
$activity = $user->doAdminsPlay($interval);

?>

<div class="page-header">
  <h1>Admin Connection Activity</h1>
</div>

<p class="lead">Showing admin connections in the last <?php echo single($interval, 'day','days');?>.</p>

<p>
  <form class="form-inline" method="GET">
    <div class="form-group">
      <div class="input-group">
        <input type="text" max='90' min='2' class="form-control" id="interval" name="interval" placeholder="Days">
        <div class="input-group-addon">days</div>
      </div>
    </div>
    <button type="submit" class="btn btn-primary">View Activity</button>
  </form>
</p>

<table class="table sticky  table-condensed table-bordered sort">
  <thead>
    <tr>
      <th>ckey</th>
      <th>Rank</th>
      <th>Connections</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($activity as $a):?>
    <?php if('Player' == $a->lastadminrank) continue;?>
    <tr>
      <td><?php echo $a->label;?></td>
      <td><?php echo $a->lastadminrank;?></td>
      <td><?php echo $a->connections;?></td>
    </tr>
  <?php endforeach;?>
  </tbody>
</table>

<?php require_once('../footer.php') ;?>