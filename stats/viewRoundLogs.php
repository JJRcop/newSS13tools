<?php
$json = false;
if (!isset($_GET['round'])) die("No round ID specified!");
$round = filter_input(INPUT_GET, 'round', FILTER_SANITIZE_NUMBER_INT);
if (isset($_GET['json'])) $json = filter_input(INPUT_GET, 'json', FILTER_VALIDATE_BOOLEAN);

if($json) {
  require_once('../config.php');
  header('Content-Type: application/json');
  $round = new round($round,FALSE,TRUE,TRUE);
  echo $round->logs;
  die();
} else {
  require_once('../header.php');
  $round = new round($round,FALSE,TRUE);
}
?>

<?php if (!$round->logs): ?>
  <p class="lead">Unable to accurately locate logs for round #<?php echo $round->round_id;?>, because either the server or round start time could not be located.</p>
    <a href='viewRound.php?round=<?php echo $round->round_id;?>'
  class='btn btn-info btn-xs'>
      <span class='glyphicon glyphicon-arrow-left'></span>
      Back
    </a>
<?php die(); endif;?>

<div class="page-header">
  <a href="viewRound.php?round=<?php echo $round->round_id;?>" class="btn btn-info btn-xs">
    <span class='glyphicon glyphicon-arrow-left'></span>
    Back
  </a>
  <h1>
  Logs for Round #<?php echo $round->round_id;?>
    <small>Ended <?php echo $round->end;?></small>
  </h1>
</div>

<p class="lead">Database timestamps do not correspond directly with log timestamps, so these are matched up as closely as possible. There may be errors or omissions (especially with rounds that go on past midnight GMT)</p>
<p>
  <?php if ($round->fromCache):?>
    This file was loaded from cache.
  <?php else:?>
    This log file was loaded from <code><?php echo $round->logURL;?></code>.
    It is now cached. This logfile will load much faster next time.
  <?php endif;?>
</p>

<p>Only show:</p>
<ul class="list-inline" id="filter">
  <li class="btn btn-info btn-xs filter" id="GAME">GAME</li>
  <li class="btn btn-info btn-xs filter" id="ACCESS">ACCESS</li>
  <li class="btn btn-info btn-xs filter" id="SAY">SAY</li>
  <li class="btn btn-info btn-xs filter" id="OOC">OOC</li>
  <li class="btn btn-info btn-xs filter" id="ADMIN">ADMIN</li>
  <li class="btn btn-info btn-xs filter" id="EMOTE">EMOTE</li>
  <li class="btn btn-info btn-xs filter" id="WHISPER">WHISPER</li>
  <li class="btn btn-info btn-xs filter" id="PDA">PDA</li>
  <li class="btn btn-info btn-xs filter" id="CHAT">CHAT</li>
  <li class="btn btn-info btn-xs filter" id="LAW">LAW</li>
  <li class="btn btn-info btn-xs filter" id="PRAY">PRAY</li>
  <li class="btn btn-info btn-xs filter" id="COMMENT">COMMENT</li>
</ul>

<div class="log-wrap">
  <table class="logs">
  <tbody>
    <?php
      foreach ($round->logs as $log){
        echo $log;
      }
    ?>
  </tbody>
  </table>
</div>

<script>
$('#filter .filter').click(function(e){
  e.preventDefault();
  var filter = $(this).attr('id');
  $(this).toggleClass('active');
  $('.logs tr').toggle();
  $('.logs tr.'+filter).toggle();
})
$('#filter .active').click(function(e){
  e.preventDefault();
  $(this).toggleClass('active');
  $('.logs tr').show();
})
</script>

<?php require_once('../footer.php'); ?>