<?php 
require_once('../header.php');
if (!isset($_GET['round'])) die("No round ID specified!");
$round = filter_input(INPUT_GET, 'round', FILTER_SANITIZE_NUMBER_INT);
$round = new round($round);
?>

  <p class="lead">Public logs are broken and will return at some point in the future. Sorry.</p>
    <a href='viewRound.php?round=<?php echo $round->round_id;?>'
  class='btn btn-info btn-xs'>
      <i class="fa fa-arrow-left"></i> Back
    </a>
<?php die();?>

<div class="page-header">
  <h1>
  <a href="viewRound.php?round=<?php echo $round->round_id;?>" class="btn btn-info btn-xs">
    <i class="fa fa-arrow-left"></i> Back
  </a>
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
    $i = 0;
     foreach ($round->logs as &$log){
       $i++;
       $ld = $log;
       // if (strpos($ld[2],' has renamed the station as ') !== FALSE){
       //   $this->attachStationNameToRoundID($ld[2],$round);
       // }
       $log = "<tr id='L-$i' class='".$ld[1]."'><td class='ln'><a href='#L-$i'>#$i</a></td><td class='ts'>[".$ld[0]."]";
       $log.= "</td><td class='lt'>".$ld[1].": </td><td>";
       $log.= $ld[2];
       $log.="</td></tr>";
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