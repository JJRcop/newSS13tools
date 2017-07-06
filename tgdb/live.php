<?php
$id = false;

if(isset($_GET['id'])){
  require_once("../config.php");
  $death = new death();
  $id = filter_input(INPUT_GET, 'id');
  $deaths = $death->getDeathsSinceID($id);
  foreach ($deaths as $d){
    include(ROOTPATH."/inc/view/deathRow.php");
  }
  die();
} else {
  $wide = true; $skip = true; require_once('../header.php');
}

$user = new user();
if(2 > $user->level) {
  die("You must be an admin to access this page. <a href='".APP_URL."/auth.php'>Authenticate</a>");
}
$death = new death();
$deaths = $death->getDeathsSinceID();

?>

<style>

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

<?php require_once(ROOTPATH."/inc/view/deathTable.php");?>

<audio src="../tgstation/sound/misc/compiler-stage2.ogg" id="ding"></audio>

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
  var id = $('.table tbody tr').first().attr('data-id');
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
      console.log(e);
      $('.table tbody').prepend(e);
    }
  })
};

setInterval(function() {
  loadNewDeaths();
}, 5000);
</script>

<?php require_once('../footer.php');