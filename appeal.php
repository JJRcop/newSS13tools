<?php require_once('header.php');?>
<?php if ($user->ckey):?>
  <div class="page-header">
    <h1><small>You are</small> <?php echo $user->label;?></h1>
  </div>
  <p class="lead">Searching for any active bans...</p>

  <?php
    $ban = new ban();
    $user->bans = $ban->getPlayerBans($user->ckey,TRUE);
    $count = count($user->bans);
  ?>

  <p class="lead">
  <?php if(0 == $count):?>
    You have no active bans!
  <?php else:?>
    The following <?php echo single($count,'ban','bans');?> are active and eligible for appeal:
  <?php endif;?>
  </p>

  <?php foreach ($user->bans as $ban):?>
    <div class="panel panel-<?php echo $ban->class;?>">
      <div class="panel-heading">
        <h3 class="panel-title">
          <i class="fa fa-<?php echo $ban->icon;?>"
          title="<?php echo $ban->scope;?> ban" data-toggle="tooltip"></i>
          [<?php echo $ban->a_ckey;?>] <?php echo $ban->ckey;?> | Banned from <?php echo $ban->scope;?>
        </h3>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
          <p>
            <strong>Byond account/character name:</strong>
            <?php echo $ban->ckey;?>/
          </p>
          <p>
            <strong>Banning admin:</strong>
            <?php echo $ban->a_ckey;?>
          </p>
          <p>
            <strong>Ban type:</strong>
            <?php echo $ban->scope;?>
          </p>
          <p>
            <strong>Ban reason and length:</strong>
            "<?php echo $ban->reason;?>", <?php echo $ban->duration;?>
          </p>
          <p>
            <strong>Time ban was placed:</strong>
            <?php echo $ban->bantime;?> (GMT)
          </p>
          <p>
            <strong>Server ban was placed on:</strong>
            <?php echo $ban->serverip;?> (GMT)
          </p>
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

         <div class="col-md-6">
           <h3>BB Code</h3>
           <p><strong>Thread title:</strong></p>
           <input class="form-control" value="[<?php echo $ban->a_ckey;?>] <?php echo $ban->ckey;?> | Banned from <?php echo $ban->scope;?>" />
           <textarea class="form-control" rows="10">
[b]Byond account/character name:[/b] <?php echo $ban->ckey;?>/[Character Name Here]

[b]Banning admin:[/b] <?php echo $ban->a_ckey;?>


[b]Ban type:[/b] <?php echo $ban->scope;?>


[b]Ban reason and length:[/b] "<?php echo $ban->reason_raw;?>", <?php echo $ban->duration;?>


[b]Time ban was placed:[/b] <?php echo $ban->bantime;?> (GMT)

[b]Server ban was placed on:[/b] <?php echo $ban->serverip;?> (GMT)

[b]Your side of the story:[/b]
[Provide own text]

[b]Why you think you should be unbanned:[/b]
[Provide own text]

           </textarea>
         </div>
       </div>
      </div>
    </div>

  <?php endforeach;?>

<?php else: ?>
  <div class="page-header">
    <h1><small>Hmm, I'm not sure who you are. Can you <a href="<?php echo APP_URL;?>auth.php">authenticate</a> for me?</small></h1>
  </div>
<?php endif;?>

<?php require_once('footer.php');
