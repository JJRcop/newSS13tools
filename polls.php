<?php require_once('header.php');?>
<?php
if (isset($_GET['poll'])) {
  $p = filter_input(INPUT_GET, 'poll', FILTER_SANITIZE_NUMBER_INT);
  $poll = new poll($p);
} else {
  $poll = new poll();
  $polls = $poll->getPoll();
}
?>

<?php if(isset($_GET['poll'])):?>
  <div class="page-header">
    <h1><small>#<?php echo $poll->id;?></small>
      <?php echo $poll->question;?>
    </h1>
  </div>
  <div class="row">
    <div class="col-md-4">
      <p class="lead text-right">
        <strong>Starts</strong><br>
        <?php echo $poll->starttime;?>
      </p>
    </div>

    <div class="col-md-4 text-center">
      <p class="lead text-center">
        <strong>Duration</strong><br>
        <?php echo $poll->duration;?>
      </p>
    </div>

    <div class="col-md-4">
      <p class="lead text-left">
        <strong>Ends</strong><br>
        <?php echo $poll->endtime;?>
      </p>
    </div>
  </div>

  <?php if ($poll->ended):?>
    <div class="alert alert-info"><i class="fa fa-clock-o"></i> This poll has ended</div>
  <?php endif;?>

  <hr>

  <?php if('IRV' === $poll->polltype):?>
    <?php shuffle($poll->options);?>
    <div class="page-header">
      <h2>Options <small>(Randomized order)</small></h2>
    </div>
    <ul class="list-group">
    <?php foreach($poll->options as $o):?>
      <li class="list-group-item"><?php echo $o->text;?></li>
    <?php endforeach;?>
    </ul>
  <?php elseif('TEXT' === $poll->polltype):?>
    <div class="page-header">
      <h2>Responses <small><?php echo $poll->totalVotes;?> responses, ABSTAIN responses are hidden</small></h2>
    </div>
    <dl class="dl-horizontal">
    <?php foreach($poll->results as $r):?>
      <?php if('ABSTAIN' === $r->replytext) continue;?>
      <dt id="<?php echo $r->id;?>">
        <?php echo $r->datetime;?><br>
        <a href="#<?php echo $r->id;?>">#<?php echo $r->id;?></a></dt>
      <dd><blockquote><?php echo $r->replytext;?></blockquote></dd>
    <?php endforeach;?>
    </dl>
  <?php else:?>
    <div class="page-header">
      <h2>Options</h2>
    </div>
    <?php if ('NUMVAL' === $poll->polltype):?>
      <?php $poll->options = $poll->options{0};?>
      <p class="lead">On a scale of <?php echo $poll->options->minval;?> to <?php echo $poll->options->maxval;?>, with: </p>
      <div class="row">
        <div class="col-md-4">
          <p class="lead text-right">
            <strong><?php echo $poll->options->minval;?></strong> meaning<br>
            &ldquo;<?php echo $poll->options->descmin;?>&rdquo;
          </p>
        </div>

        <div class="col-md-4 text-center">
          <p class="lead text-center">
            <strong>And</strong>
          </p>
        </div>

        <div class="col-md-4">
          <p class="lead text-left">
            <strong><?php echo $poll->options->maxval;?></strong> meaning<br>
            &ldquo;<?php echo $poll->options->descmax;?>&rdquo;
          </p>
        </div>
      </div>
      <blockquote>
      <?php echo $poll->question;?>
      </blockquote>
    <?php else:?>
      <ul class="list-group">
      <?php foreach($poll->options as $o):?>
        <li class="list-group-item"><?php echo $o->text;?></li>
      <?php endforeach;?>
      </ul>
    <?php endif;?>

    <div class="page-header">
      <h2>Results <small><?php echo $poll->totalVotes;?> votes cast</small></h2>
    </div>
    <ul class="list-group">
    <?php foreach($poll->results as $r):?>
      <li class="list-group-item">
        <span class="badge"><?php echo $r->percent;?>%</span>
        <span class="badge"><?php echo $r->votes;?></span>
        <strong><?php echo $r->option;?></strong>
      </li>
    <?php endforeach;?>
    </ul>
  <?php endif;?>
<?php else:?>

<div class="page-header">
  <h1>Polls</h1>
</div>

<table class="table table-bordered">
  <thead>
    <tr>
      <th>ID</th>
      <th>Question</th>
      <th>Type</th>
      <th>Created by</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($polls as $p):?>
      <tr class="<?php echo ($p->ended)?'warning':'success'?>">
        <td><a href="?poll=<?php echo $p->id;?>"><?php echo $p->id;?></td>
        <td><?php echo ($p->ended)?"<span class='label label-info'>Ended</span>":"";?>
        <?php echo $p->question;?><br>
        <small>Opened <?php echo $p->starttime;?> | Closes <?php echo $p->endtime;?> | runs for <?php echo $p->duration;?>
        </small>
        </td>
        <td><?php echo $poll->mapPollType($p->polltype);?></td>
        <td><?php echo $p->createdby_ckey;?></td>
      </tr>
    <?php endforeach;?>
  </tbody>
</table>
<?php endif;?>
<?php require_once('footer.php');?>