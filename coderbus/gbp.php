<?php require_once('../header.php');?>

<?php
$gbp = $app->getURL('https://tools.tgstation13.org/pr_balances.json',5,false, true);
$labels1 = $app->getURL('https://api.github.com/repos/tgstation/tgstation/labels');
$labels2 = $app->getURL('https://api.github.com/repos/tgstation/tgstation/labels?page=2');

$gbp->data = json_decode($gbp->data,TRUE);
arsort($gbp->data);
?>

<div class="row">
  <div class="col-md-6">
    <div class="page-header">
      <h1>PR Balance Data</h1>
    </div>
  <table class="table sticky table-bordered table-condensed sort">
    <thead>
      <tr>
        <th>Username</th>
        <th>Balance</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($gbp->data as $g => $n): ?>
      <tr>
        <th>
          <a href='https://github.com/tgstation/tgstation/pulls?utf8=%E2%9C%93&q=is%3Apr%20author%3A<?php echo urlencode($g);?>' target="_blank"><?php echo $g;?>
            <i class='fa fa-external-link'></i>
          </a> 
        </th>
        <td><?php echo $n;?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="page-header">
    <h4>Cache debug info</h4>
  </div>

  <table class="table table-bordered table-condensed">
    <tr>
      <th>Cache Saved at</th>
      <td><?php echo date('r',$gbp->timestamp);?></td>
    </tr>
    <tr>
      <th>Cache Lifetime</th>
      <td><?php echo single($gbp->lifetime*60,'second','seconds');?></td>
    </tr>
    <tr>
      <th>Cache Expires at</th>
      <td><?php echo date('r',$gbp->timestamp+($gbp->lifetime*60));?></td>
    </tr>
    <tr>
      <th>Cache Age</th>
      <td><?php echo single(time() - $gbp->timestamp,'second','seconds');?></td>
    </tr>
  </table>

</div>
<div class="col-md-6">
  <div class="page-header">
    <h1>Repository Label Values</h1>
  </div>
  <table class="table sticky table-bordered table-condensed sort">
    <thead>
      <tr>
        <th>Label</th>
        <th>Value</th>
      </tr>
    </thead>
    <tbody>
  <?php $labels1 = json_decode($labels1);
  $labels2 = json_decode($labels2);
  $labels = array_merge($labels1,$labels2);
  $label_values = array(
      'Fix' => 2,
      'Refactor' => 2,
      'Code Improvement' => 1,
      'Priority: High' => 4,
      'Priority: CRITICAL' => 5,
      'Atmospherics' => 4,
      'Logging' => 1,
      'Feedback' => 1,
      'Performance' => 3,
      'Feature' => -1,
      'Balance/Rebalance' => -1,
      'Tweak' => -1,
    );
  foreach ($labels as $label):?>
  <tr>
    <td>
      <span class="label label-gh" style="background: #<?php echo $label->color;?>">
          <a href="https://github.com/tgstation/tgstation/labels/<?php echo $label->name;?>" target="_blank"><?php echo $label->name;?>
          </a>
        </span>
    </td>
    <td>
      <?php echo @$label_values[$label->name];?>
    </td>
  </tr>
  <?php endforeach;?>
  </tbody>
</table>

</div>
</div>

<?php require_once('../footer.php');?>