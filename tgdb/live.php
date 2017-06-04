<?php
$where = null;
$id = false;
if(isset($_GET['id'])){
  $id = filter_input(INPUT_GET, 'id');
  $where = "WHERE id > $id";
  require_once("../config.php");
} else {
  $wide = true; $skip = true; require_once('../header.php');
}

$user = new user();
if(2 > $user->level) {
  die("You must be an admin to access this page. <a href='".APP_URL."/auth.php'>Authenticate</a>");
}

$db = new database();
$db->query("SELECT * FROM ss13death
  $where
  ORDER BY tod
  DESC LIMIT 0,30");
$db->execute();
$deaths = $db->resultSet();
$death = new death();
$round = new round();

if($id):?>
<?php foreach ($deaths as $d):?>
  <?php $d = $death->parseDeath($d);?>
  <tr data-id="<?php echo $d->id;?>" class="<?php echo $d->server;?>">
    <td><?php echo $d->link;?></td>
    <td><?php echo "$d->name<br><small>$d->byondkey</small>";?></td>
    <td><?php echo "$d->job <br><small class='text-danger'>".ucfirst($d->special)."</small>";?></td>
    <td><?php echo "$d->pod <br><small>$d->mapname  ($d->coord)</small>";?></td>
    <td>
      <?php echo $d->labels;?><br>
      <?php if('' != $d->laname):?>
        <?php echo "By $d->laname <small>($d->lakey)</small>";?>        <?php if($d->suicide) echo " <small class='text-danger'>(Probable Suicide)</small>";?>
      <?php endif;?>
    </td>
    <td><?php echo "$d->tod";?></td>
  </tr>
<?php endforeach; die(); endif;?>

<style>
.label-brute   {min-width: 36px; display: inline-block; background: pink; color: black;}
.label-brain   {min-width: 36px; display: inline-block; background: #5995ba; color: black;}
.label-fire    {min-width: 36px; display: inline-block; background: #e0a003; color: black;}
.label-oxy     {min-width: 36px; display: inline-block; background: #689bc3; color: white;}
.label-tox     {min-width: 36px; display: inline-block; background: #61af25; color: black;}
.label-clone   {min-width: 36px; display: inline-block; background: #ab63d8; color: white;}
.label-stamina {min-width: 36px; display: inline-block; background: #0e22aa; color: white;}

.Sybil {
  background: #eafdff;
}

.Basil {
  background: #ffeaea;
}
@keyframes new {
  from {
    background: #fff9c4;
  }

  to {
    background: transparent;
  }
}
.new td{
  background: #fff9c4;
  animation-name: new;
  animation-duration: 10s;
  animation-fill-mode: forwards;
}
</style>
<p>
  <h1>
    <div id="mute" class="label label-success" style="display: block;"><i class='fa fa-volume-off fa-fw'></i> <span>Mute</span></div>
  </h1>
</p>

<table id="deaths" class="table table-bordered table-condense">
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Job</th>
      <th>Location & Map</th>
      <th>Damage & Attacker (if set)<br>
        <span title="Brute" class="label label-brute">BRU</span>
        <span title="Brain" class="label label-brain">BRA</span>
        <span title="Fire" class="label label-fire">FIR</span>
        <span title="Oxygen" class="label label-oxy">OXY</span>
        <span title="Toxin" class="label label-tox">TOX</span>
        <span title="Clone" class="label label-clone">CLN</span>
        <span title="Stamina" class="label label-stamina">STM</span>
      </th>
      <th>Time & Server</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($deaths as $d):?>
      <?php $d = $death->parseDeath($d);?>
      <tr data-id="<?php echo $d->id;?>" class="<?php echo $d->server;?>">
        <td><?php echo $d->link;?></td>
        <td><?php echo "$d->name<br><small>$d->byondkey</small>";?></td>
        <td><?php echo "$d->job <br><small class='text-danger'>".ucfirst($d->special)."</small>";?></td>
        <td><?php echo "$d->pod <br><small>$d->mapname  ($d->coord)</small>";?></td>
        <td>
          <?php echo $d->labels;?><br>
          <?php if('' != $d->laname):?>
            <?php echo "By $d->laname <small>($d->lakey)</small>";?>        <?php if($d->suicide) echo " <small class='text-danger'>(Probable Suicide)</small>";?>
          <?php endif;?>
        </td>
        <td><?php echo "$d->tod";?></td>
      </tr>
    <?php endforeach;?>
  </tbody>
</table>
<audio src="../tgstation/sound/misc/compiler-stage2.ogg" id="ding"></audio>
<audio src="../tgstation/sound/misc/compiler-stage2.ogg" id="none"></audio>
<!--  <audio src="../tgstation/sound/effects/adminhelp.ogg" id="ding"></audio>
 -->

<script>
var mute = false;
$('#mute').click(function(e){
  e.preventDefault();
  $('#mute').toggleClass('label-success').toggleClass('label-danger');
  $('#mute i').toggleClass('fa-volume-off').toggleClass('fa-volume-up');
  if(mute){
    mute = false;
    $('#mute span').text('Mute');
  } else {
    mute = true;
    $('#mute span').text('Unmute');
  }
})

function loadNewDeaths(){
  var id = $('#deaths tbody tr').first().attr('data-id');
  console.log('Checking for deaths since '+id);
  // $('#deaths tbody').load('?id='+id);
  $.ajax({
    url: '?id='+id,
  })
  .done(function(e){
    if('' == e){
      console.log("No new deaths.");
    } else {
      if(!mute){
        ding.play();
      }
      console.log("Found some!");
      $('#deaths tbody').prepend(e);
    }
  })
};

setInterval(function() {
  loadNewDeaths();
}, 5000);
</script>

<?php require_once('../footer.php');