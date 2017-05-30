<div class="panel panel-<?php echo $ban->class;?>" id="ban-<?php echo $ban->id;?>">
  <div class="panel-heading">
    <h3 class="panel-title">
      <i class="fa fa-<?php echo $ban->icon;?>"
      title="<?php echo $ban->scope;?> ban" data-toggle="tooltip"></i>
      <a href='viewPlayer.php?ckey=<?php echo $ban->ckey;?>'>
        <?php echo $ban->ckey;?></a> 
      banned from <?php echo $ban->scope;?>, <?php echo $ban->bantimestamp;?> by
      <a href="#">
        <?php echo $ban->a_ckey;?></a>
      <p class="pull-right"><a href="viewBan.php?ban=<?php echo $ban->id;?>">#<?php echo $ban->id;?></a></p>
    </h3>
  </div>
  <div class="panel-body">
  <?php if ('Active' != $ban->status && isset($hideInactive)):?>
    <p class="text-center text-muted">
    &laquo; <?php echo $ban->status;?>. <a href="viewBan.php?ban=<?php echo $ban->id;?>">Click here</a> for details &raquo;
    </p>
    </div>
  <?php else:?>
   <p><?php echo $ban->reason;?></p>
   <?php if (!empty($ban->rules)):?>
    <hr>
    <p><em>The following rules were detected in this ban:</em></p>
    <ul class="list-unstyled">
      <?php foreach($ban->rules as $r):?>
        <li><?php echo "<strong>#".$r['number'].":</strong> ".$r['text'];?></li>
      <?php endforeach;?>
    </ul>
   <?php endif;?>
  </div>
  <table class="table table-condensed table-bordered ban">
    <tr>
      <th>Status</th>
      <td class="<?php echo $ban->statusClass;?>">
        <?php echo $ban->status;?>
      </td>
      <th>IP</th>
      <td class='ipaddr'><?php echo $ban->ip;?>
        <?php if ($ban->ip):?>
          <?php echo $ban->ipql;?>
        <?php endif;?></td>
    </tr>
    <tr>
      <th>Ban issued</th>
      <td><?php echo $ban->bantimestamp;?></td>
      <th>CID</th>
      <td class='cid'><?php echo $ban->computerid;?>
        <?php if ($ban->computerid):?>
            <?php echo $ban->cidql;?>
          <?php endif;?>
      </td>
    </tr>
    <tr>
      <?php if ($ban->status == 'Expired'):?>
        <th>Ban expired</th>
      <?php else:?>
        <th>Ban expires</th>
      <?php endif;?>
      <td><?php echo $ban->expirationtimestamp;?></td>
      <th>Banned from</th>
      <td><?php echo $ban->scope;?></td>
    </tr>
    <tr>
      <th>Duration</th>
      <td><?php echo $ban->duration;?></td>
      <th>Ban was issued on</th>
      <td><?php echo $ban->serverip;?></td>
    </tr>
    </table>
  <?php endif;?>
</div>
