<div class="panel panel-<?php echo $ban->class;?>">
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
  </div>
  <table class="table table-condensed table-bordered ban">
    <tr>
      <th>Status</th>
      <td class="<?php echo $ban->statusClass;?>">
        <?php echo $ban->status;?>
      </td>
      <th>Duration</th>
      <td><?php echo $ban->duration;?></td>
    </tr>
    <tr>
      <th>Banned from</th>
      <td><?php echo $ban->scope;?></td>
      <th>Ban issued</th>
      <td><?php echo $ban->bantimestamp;?></td>
    </tr>
    <tr>
      <th>CID</th>
      <td class='cid'><?php echo $ban->computerid;?>
        <?php if ($ban->computerid):?>
            <div class="ql">
              (<a href="bans.php?cid=<?php echo $ban->computerid;?>"><i class="fa fa-ban"></i></a>)
              (<a href="#"><i class="fa fa-plug"></i></a>)
              (<a href="#"><i class="fa fa-user"></i></a>)
            </div>
          <?php endif;?></td>
      <?php if ($ban->status == 'Expired'):?>
        <th>Ban expired</th>
      <?php else:?>
        <th>Ban expires</th>
      <?php endif;?>
      <td><?php echo $ban->expirationtimestamp;?></td>
    </tr>
    <tr>
      <th>IP</th>
      <td class='ipaddr'><?php echo $ban->ip;?>
        <?php if ($ban->ip):?>
            <div class="ql">
              (<a href="bans.php?ip=<?php echo $ban->ip;?>"><i class="fa fa-ban"></i></a>)
              (<a href="#"><i class="fa fa-plug"></i></a>)
              (<a href="#"><i class="fa fa-user"></i></a>)
            </div>
        <?php endif;?></td>
      <th>Ban was issued on</th>
      <td><?php echo $ban->serverip;?></td>
    </tr>
    </table>
  <?php endif;?>
</div>
