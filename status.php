<?php require_once('header.php'); ?>

<?php $app = new app(TRUE); ?>
<div class="row">
  <div class="col-md-6">
    <div class="page-header">
    <h1>The Todo list</h1>
    </div>

    <h2>Done!</h2>
    <ul class="options-list">
      <li class="list-group-item">DMI diff viewer</li>
      <li class="list-group-item">Cached month-by-month stats</li>
      <li class="list-group-item">Month-by-month stats</li>
      <li class="list-group-item">Tgstation forum auth</li>
    </ul>

    <h2>Near-future</h2>
    <ul class="options-list">
      <li class="list-group-item">Make monthly stats faster</li>
      <li class="list-group-item">Round list calendar view</li>
      <li class="list-group-item">Split stats by server</li>
      <li class="list-group-item">Daily death maps</li>
    </ul>

    <h2>Far future</h2>
    <ul class="options-list">
      <li class="list-group-item">Ban appeals proof-of-concept</li>
      <li class="list-group-item">Stats refactor design document</li>
      <li class="list-group-item">Refactor stats</li>
    </ul>
  </div>
  <div class="col-md-6">
    <div class="page-header">
      <h1>Changelog</h1>
    </div>
    <ul class="list-group">
      <li class="list-group-item">
        <h4 class="list-group-heading">09-03-2017</h4>
        <ul class="list-unstyled">
          <li><i class="fa fa-plus text-success"></i> Coders and up now have access to the DMI Diff viewer, available under icon tools</li>
        </ul>
      </li>
      <li class="list-group-item">
        <h4 class="list-group-heading">28-02-2017</h4>
        <ul class="list-unstyled">
          <li><i class="fa fa-plus text-success"></i> You can now log in to atlantaned.space with your tgstation13.org forum account! Click on Authenticate in the upper right-hand corner to get started!</li>
        </ul>
      </li>
      <li class="list-group-item">
        <h4 class="list-group-heading">24-02-2017</h4>
        <p>A bunch of stuff got refactored, and...</p>
        <ul class="list-unstyled">
          <li><i class="fa fa-plus text-success"></i> The library has a search engine!</li>
        </ul>
      </li>
      <li class="list-group-item">
        <h4 class="list-group-heading">22-02-2017</h4>
        <p>The database is back and I fixed a bunch of stuff!</p>
        <ul class="list-unstyled">
          <li><i class="fa fa-check text-info"></i> Individual stat pages should work better, along with rounds as well.</li>
        </ul>
      </li>
      <li class="list-group-item">
        <h4 class="list-group-heading">13-02-2017</h4>
        <p>The central database is down, so I'm tweaking some other stuff, like:</p>
        <ul class="list-unstyled">
          <li><i class="fa fa-plus text-success"></i> Adding this changelog!</li>
          <li><i class="fa fa-check text-info"></i> Refactoring CSS</li>
          <li><i class="fa fa-check text-info"></i> FontAwesome!</li>
          <li><i class="fa fa-plus text-success"></i> Sticky table headers!</li>
          <li><i class="fa fa-plus text-success"></i> Pages for individual stats! <a href="<?php echo APP_URL;?>/stats/singleStat.php?stat=radio_usage">Check out radio usage!</a></li>
        </ul>
      </li>
    </ul>
  </div>
</div>
<?php require_once('footer.php');