<?php 
if (!isset($_GET['round'])) die("No round ID specified!");
$reset = FALSE;
if(1 <= $user->level){
  $reset = filter_input(INPUT_GET, 'reset', FILTER_VALIDATE_BOOLEAN);
}
if($reset){
  $log = new GameLogs($round, TRUE);
  $round = new round($round,array('logs'));
} else {
  $round = new round($round, array('logs'));
}

?>

<nav>
  <ul class="pager">
  <?php if ($round->prev): ?>
    <li class="previous">
        <a href="<?php echo $app->APP_URL;?>round.php?round=<?php echo $round->prev;?>&logs=true">
        <span aria-hidden="true">&larr;</span>
        Previous round</a>
    </li>
  <?php endif;?>

  <li><a href="<?php echo $app->APP_URL;?>round.php">
    <i class="fa fa-list"></i> Round listing</a>
  </li>

  <?php if ($round->next): ?>
    <li class="next">
      <a href="<?php echo $app->APP_URL;?>round.php?round=<?php echo $round->next;?>&logs=true">Next round
      <span aria-hidden="true">&rarr;</span></a>
    </li>
  <?php endif;?>
  </ul>
</nav>

<div class="page-header">
  <h1>
  <a href="<?php echo $round->href;?>" class="btn btn-info btn-xs">
    <i class="fa fa-arrow-left"></i> Back
  </a>
  Logs for Round #<?php echo $round->id;?>
    <small>Ended <?php echo $round->end;?></small>
  </h1>
</div>

<p class="lead">
  <?php if ($round->fromCache):?>
    This file was loaded from cache.
  <?php else:?>
    This log file was loaded from <code><?php echo $round->logsURL;?></code>.
    It is now cached. This logfile will load much faster next time.
  <?php endif;?>
</p>

<p class="lead">
  Additional logs from this round are available <a href='<?php echo $round->logsURL;?>' target="_blank">here</a>.
</p>

<p class="lead text-danger">
  Log time resolution is limited to seconds, and a lot can happen in one second. The logs are parsed on the backend in the correct order in which they are recevied. For display purposes though, they are re-sorted and displayed in order by seconds. This may put certain events out of order, but those incidences should be minimal.
</p>

<?php if(1 <= $user->level):?>
  <p class="lead">
    If these logs appear incorrect, or if you want to regenerate missing data, <a href="<?php echo $round->href;?>&logs=true&reset=true" class="btn btn-warning btn-xs"><i class="fa fa-refresh"></i> Reset them</a>.
  </p>

<?php endif;?>

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
      foreach($round->logs as $log): $i++;?>
<tr id="L-<?php echo $i;?>" class="<?php echo $log->type;?>">
<td class="ln"><a href="#L-<?php echo $i;?>"><?php echo $i;?></a></td>
<?php if($log->x):?>
<td class="ts">[<?php echo date("H:i:s", strtotime($log->timestamp));?>]</td>
<td class="coord">[<?php echo "$log->x, $log->y, $log->z";?>]</td>
<?php else:?>
<td class="ts" colspan="2">[<?php echo date("H:i:s", strtotime($log->timestamp));?>]</td>
<?php endif;?>
<td class="lt"><?php echo $log->type;?></td>
<td class="lc"><?php echo strip_tags($log->text);?></td>
</tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<nav>
  <ul class="pager">
  <?php if ($round->prev): ?>
    <li class="previous">
        <a href="<?php echo $app->APP_URL;?>round.php?round=<?php echo $round->prev;?>&logs=true">
        <span aria-hidden="true">&larr;</span>
        Previous round</a>
    </li>
  <?php endif;?>

  <li><a href="<?php echo $app->APP_URL;?>round.php">
    <i class="fa fa-list"></i> Round listing</a>
  </li>

  <?php if ($round->next): ?>
    <li class="next">
      <a href="<?php echo $app->APP_URL;?>round.php?round=<?php echo $round->next;?>&logs=true">Next round
      <span aria-hidden="true">&rarr;</span></a>
    </li>
  <?php endif;?>
  </ul>
</nav>
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