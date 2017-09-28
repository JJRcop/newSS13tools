<?php require_once('../header.php');?>

<?php
$query = filter_input_array(INPUT_POST, array(
    'content'  => array(
      'filter' => FILTER_SANITIZE_STRING,
      'flags'  => FILTER_FLAG_STRIP_HIGH
    ),
    // 'context' => array(
    //   'filter'    => FILTER_VALIDATE_INT,
    //   'default'   => 10,
    //   'min_range' => 1,
    //   'max_range' => 100
    // ),
    'type'  => array(
      'filter' => FILTER_SANITIZE_STRING,
      'flags'  => FILTER_FLAG_STRIP_HIGH
    ),
  ), TRUE
);

$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array('options' =>
  array(
      'filter'    => FILTER_VALIDATE_INT,
      'default'   => 0,
      'min_range' => 1)
  )
);
$prev = $page - 1;
$next = $page + 1;

if($query){
  $_SESSION['query'] = $query;
  $page = 0;
} else if(isset($_SESSION['query'])){
  $query = $_SESSION['query'];
} else {
  $query = null;
  unset($_SESSION['query']);
}

?>

<?php $db = new database(TRUE);
  $db->query("SELECT DISTINCT `type` FROM round_logs");
  $types = $db->resultset();
?>
<div class="page-header">
  <h1>Parsed Round Log Search</h1>
</div>

<p>This form will <strong>only search logs for rounds that have been parsed</strong>.</p>

<form method="POST" target="">
  <div class="form-group">
    <label for="content">Content</label>
    <input name="content" type="text" class="form-control input-lg" id="content" placeholder="String to search for" value="<?php echo $query['content'];?>">
  </div>
  <div class="row">
<!--     <div class="form-group col-md-6">
      <label for="context">Number of lines to return before and after the matched string is found</label>
      <input name="context" type="number" class="form-control input-lg" id="context" placeholder="10">
      <span id="helpBlock" class="help-block">Goes up to 100</span>
    </div> -->
    <div class="form-group col-md-6">
      <label for="context">Log type</label>
      <select class="form-control" name="type">
        <option value="">(any)</option>
        <?php foreach ($types as $t):?>
          <option value="<?php echo $t->type;?>"
            <?php echo ($t->type === $query['type'])?'selected="selected"':'';?>
            ><?php echo $t->type;?></option>
        <?php endforeach;?>
      </select>
    </div>
  </div>
  <button type="submit" class="btn btn-success">Search</button>
</form>

<?php if($query || isset($_SESSION['query'])):
  $logs = (new GameLogs)->query($query, $page);?>
  <hr>
  <nav aria-label="Search results">
    <ul class="pager">
      <?php if(isset($page) && $page >= 1):?>
        <li class="previous"><a href="?page=<?php echo $prev;?>"><span aria-hidden="true">&larr;</span> Previous</a></li>
      <?php endif;?>
      <?php if(isset($page) && 100 === count($logs)):?>
        <li class="next"><a href="?page=<?php echo $next;?>">Next <span aria-hidden="true">&rarr;</span></a></li>
      <?php endif;?>
    </ul>
  </nav>
  <div class="log-wrap">
    <table class="logs">
      <tbody>
<?php foreach($logs as $log):?>
  <tr class="<?php echo $log->type;?>">
  <td class="rn"><a href="<?php echo APP_URL."round.php?round=$log->round&logs=true";?>"><i class="fa fa-circle-o"></i> <?php echo $log->round;?></a></td>
  <?php if($log->x):?>
  <td class="ts">[<?php echo $log->timestamp;?>]</td>
  <td class="coord">[<?php echo "$log->x, $log->y, $log->z";?>]</td>
  <?php else:?>
  <td class="ts" colspan="2">[<?php echo $log->timestamp;?>]</td>
  <?php endif;?>
  <td class="lt"><?php echo $log->type;?></td>
  <td class="lc"><?php echo strip_tags($log->text);?></td>
  </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif;?>

<nav aria-label="Search results">
  <ul class="pager">
    <?php if(isset($page) && $page >= 1):?>
      <li class="previous"><a href="?page=<?php echo $prev;?>"><span aria-hidden="true">&larr;</span> Previous</a></li>
    <?php endif;?>
    <?php if(isset($page) && 100 === count($logs)):?>
      <li class="next"><a href="?page=<?php echo $next;?>">Next <span aria-hidden="true">&rarr;</span></a></li>
    <?php endif;?>
  </ul>
</nav>

<?php require_once('../footer.php');?>
