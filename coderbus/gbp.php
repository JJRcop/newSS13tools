<?php require_once('../header.php');?>

<div class="page-header">
  <h1>PR Balance Data</h1>
</div>

<?php
$gbp = json_decode($app->getURL('https://tools.tgstation13.org/pr_balances.json'));?>

<table class="table sticky table-bordered table-condensed sort">
  <thead>
    <tr>
      <th>Username</th>
      <th>GitHub</th>
      <th>Balance</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($gbp as $g => $n): ?>
    <tr>
      <th>
        <?php echo $g;?>
      </th>
      <td>
        <a href='https://github.com/tgstation/tgstation/pulls?utf8=%E2%9C%93&q=is%3Apr%20author%3A<?php echo urlencode($g);?>' target="_blank">Github <i class='fa fa-external-link'></i></a> 
      </td>
      <td><?php echo $n;?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once('../footer.php');?>