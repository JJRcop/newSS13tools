<?php require_once("../header.php");?>
<?php require_once('tgdb_nav.php');?>

<div class="jumbotron">
  <div class="page-header">
    <h1 class="text-center">RESTRICTED AREA <br> AUTHORIZED PERSONNEL ONLY</h1>
  </div>
  <p class="text-center">You are accessing a /tg/station13 information system, which includes: 1) this computer network, 2) all computers connected to this network, and 3) all devices and storage media attached to this network or to a computer on this network. You understand and consent to the following: you may access this information system for authorized use only; you have no reasonable expectation of privacy regarding any communication of data transiting or stored on this information system; at any time and for any lawful administrative purpose, the administration may monitor, intercept, and search and seize any communication or data transiting or stored on this information system; and any communications or data transiting or stored on this information system may be disclosed or used for any lawful administration purpose.</p>

  <p class="text-center"><a href="#" class="btn btn-lg btn-primary">I agree</a> <a href="#" class="btn btn-lg btn-default">I do not agree</a></p>

</div>

<?php $activeBans = $app->getActiveBanCount();?>
<div class="page-header">
  <p class="pull-right">
  Active bans:
  <?php foreach($activeBans as $bans): $class='label-danger';?>
    <?php if(strpos($bans->type, 'PERMA')!==FALSE) $class = 'perma';?>
    <span class="label <?php echo $class;?>"><?php echo $bans->type;?>: <?php echo $bans->bans;?></span>
  <?php endforeach;?>
  </p>
  <h1>tgdb
  </h1>
</div>



<div class="row">
  <div class="col-md-6">
  <h3>Current admin memos</h3>
    <ul class="list-group">
      <?php $memos = $app->getMemos();
      if ($memos):
        foreach($memos as $memo):?>
          <li class="list-group-item">
            <blockquote>
              <p><?php echo auto_link_text(nl2br($memo->text));?></p>
              <footer><?php echo $memo->adminckey;?>
                <cite><?php echo $memo->timestamp;?></cite>
              </footer>
            </blockquote>
          </li>
        <?php endforeach; else: ?>
        <li class="list-group-item">No memos</li>
       <?php endif; ?>
    </ul>
  </div>
  <div class="col-md-6">
  <h3>See also:</h3>
  <ul class="list-group">
    <li class="list-group-item">
      <a href="newCkeys.php">New ckeys in the last 48 hours</a>
    </li>
    <li class="list-group-item">
      <a href="live.php">Deaths <span class="label label-danger">Live!</span></a>
    </li>
    <li class="list-group-item">
      <a href="ahelpStats.php">Ahelp stats</a>
    </li>
    <li class="list-group-item">
      <a href="banCounts.php">Number of bans, by type, by admin</a>
    </li>
    <li class="list-group-item">
      <a href="adminHours.php">Hours admins are online</a>
    </li>
  </ul>
  </div>
</div>
<?php require_once('../footer.php');?>
