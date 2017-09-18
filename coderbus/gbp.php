<?php require_once('../header.php');?>

<div class="page-header">
  <h1>PR Balance Data</h1>
</div>

<?php
$gbp = $app->getURL('https://tools.tgstation13.org/pr_balances.json',5,false, true);
?>

<table class="table sticky table-bordered table-condensed sort">
  <thead>
    <tr>
      <th>Username</th>
      <th>Balance</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach (json_decode($gbp->data) as $g => $n): ?>
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

<?php require_once('../footer.php');?>