<?php
$wide = true;
require_once('../header.php');

$app = new app();
$raw = $app->getAdminRanks();
$defs = (array) $raw['defs'];
$ranks = (array) $raw['ranks'];
$pos = array_keys($defs);
?>

<style>
  .table-centered td{
    text-align: center;
  }
</style>

<div class="page-header">
  <h1>What each rank can and cannot do</h1>
</div>
<table class="table table-bordered table-condensed table-centered">
  <thead>
    <tr>
    <th></th>
    <?php foreach($defs as $d => $t):?>
      <th><code><?php echo $d;?></code></th>
    <?php endforeach;?>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($ranks as $t => $r):?>
    <tr <?php echo ('Fruit' == $t)?'style="background: orange;"':'';?>><th><code><?php echo $t;?></code></th>
      <?php foreach ($pos as $p):?>
        <?php if('EVERYTHING' == $p){
          continue;
        }?>
        <?php $class = null; $line = null;?>
        <?php foreach ($r as $rank):?>
          <?php if('EVERYTHING' == $rank){
            $class = "success";
            $line = "<i class='fa fa-check-circle text-success' ";
            $line.= "aria-hidden='true' title='$t has $p' ";
            $line.= "data-toggle='tooltip'></i>";
            continue;
          }?>
          <?php if(strpos($p, $rank) !== FALSE):?>
            <?php $class = "success";?>
            <?php $line = "<i class='fa fa-check-circle text-success' ";
            $line.= "aria-hidden='true' title='$t has $p' ";
            $line.= "data-toggle='tooltip'></i>";?>
          <?php endif;?>
        <?php endforeach;?>
        <td class="<?php echo $class;?>"><?php echo $line;?></td>
      <?php endforeach;?>
    </tr>
  <?php endforeach;?>
  </tbody>
</table>

<div class="page-header">
  <h1>Explanation of permissions</h1>
</div>
<div class="alert alert-info">
<i class="fa fa-info-circle"></i> For a listing of active admins, see <a href="<?php echo APP_URL;?>stats/adminConnections.php">admin connection activity</a>.
</div>
<table class="table table-bordered table-condensed">
  <thead>
    <tr>
      <th>Permission</th>
      <th>Explanation</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($defs as $d => $t):?>
    <tr><th><?php echo $d;?></th><td><?php echo $t;?></td></tr>
  <?php endforeach;?>
  </tbody>
</table>
<?php require_once('../footer.php');