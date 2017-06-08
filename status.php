<?php require_once('header.php'); ?>

<?php $app = new app(array('changelog','info')); ?>
<div class="row">
  <div class="col-md-3">
  </div>
  <div class="col-md-6">
    <div class="page-header">
      <h1>
        <p class="text-center">
          <?php echo $app->application;?> <?php echo $app->version;?><br>
          <small>
            <a href="<?php echo $app->info->homepage;?>" target="_blank">
              <i class="fa fa-github"></i> GitHub
            </a>
          </small>
        </p>
      </h1>
    </div>

    <div class="page-header">
      <h2>Changelog</h2>
    </div>
    <ul class="list-group">
        <?php foreach($app->changelog as $date => $changes):?>
          <a  id="<?php echo $date;?>"></a>
          <li class="list-group-item">
            <h4 class="list-group-heading"><?php echo $date;?></h4>
            <?php if(isset($date['blog'])):?>
              <p><?php echo $blog;?></p>
            <?php endif;?>
              <ul class="list-unstyled fa-ul">
                <?php foreach ($changes as $change):?>
                  <?php if('object' != gettype($change)) continue;?>
                  <li>
                    <i class="fa fa-li fa-<?php echo $change->icon;?> 
                    <?php echo $change->class;?>"></i>
                    <?php echo $change->text;?>
                  </li>
                <?php endforeach;?>
              </ul>
          </li>
        <?php endforeach;?>
      <li class="list-group-item">
        <h4 class="list-group-heading">31-05-2017</h4>
        <ul class="list-unstyled fa-ul">
          <li><i class="fa fa-li fa-li fa-check text-info"></i> Yet another stats refactor is underway!</li>
        </ul>
      </li>
      <li class="list-group-item">
        <h4 class="list-group-heading">30-05-2017</h4>
        <p>Oh man I need to get better about this...</p>
        <ul class="list-unstyled fa-ul">
          <li><i class="fa fa-li fa-li fa-plus text-success"></i> An admin ranks explanation is <a href="<?php echo $app->APP_URL;?>info/ranks.php">now available</a></li>
          <li><i class="fa fa-li fa-li fa-plus text-success"></i> Along with <a href="<?php echo $app->APP_URL;?>tgstation/html/changelog.html">the changelog</a> as it appears in-game!</li>
          <li><i class="fa fa-li fa-li fa-plus text-success"></i> The number of minutes people spend playing a given job was added to the database, so I'm taking advantage of that for the <a href="<?php echo $app->APP_URL;?>stats/jobs.php">"jobularity"</a> page.</li>
          <li><i class="fa fa-li fa-li fa-check text-info"></i> The <a href="<?php echo $app->APP_URL;?>death/deaths.php">deaths page</a> has been redesigned!</li>
          <li><i class="fa fa-li fa-li fa-check text-info"></i> A major PR just went through that makes rounds easier to track. This will break the round listing until I update the app to account for it</li>
          <li><i class="fa fa-li fa-li fa-check text-info"></i> I'm making some changes to how tables handle sticking their headers to the top of the page. Please let me know if anything looks weird</li>
          <li><i class="fa fa-li fa-li fa-minus text-danger"></i> Another refactor changed how logs work and thusly, public logs are no longer available. This may change at some point in the future</li>
        </ul>
      </li>
      <li class="list-group-item">
        <h4 class="list-group-heading">14-01-2017</h4>
        <ul class="list-unstyled fa-ul">
          <li><i class="fa fa-li fa-plus text-success"></i> Admin ranks can now be verified based on external txt files!</li>
          <li><i class="fa fa-li fa-plus text-success"></i> #coderbus has been exceptionally responsive in terms of fixing bugs and adding new datapoints to the stats. Be sure to say thanks!</li>
          <li><i class="fa fa-li fa-check text-info"></i> There's a ton of work being done on refactoring stats.</li>
        </ul>
      </li>
      <li class="list-group-item">
        <h4 class="list-group-heading">09-03-2017</h4>
        <ul class="list-unstyled fa-ul">
          <li><i class="fa fa-li fa-plus text-success"></i> Coders and up now have access to the DMI Diff viewer, available under icon tools</li>
        </ul>
      </li>
      <li class="list-group-item">
        <h4 class="list-group-heading">28-02-2017</h4>
        <ul class="list-unstyled fa-ul">
          <li><i class="fa fa-li fa-plus text-success"></i> You can now log in to atlantaned.space with your tgstation13.org forum account! Click on Authenticate in the upper right-hand corner to get started!</li>
        </ul>
      </li>
      <li class="list-group-item">
        <h4 class="list-group-heading">24-02-2017</h4>
        <p>A bunch of stuff got refactored, and...</p>
        <ul class="list-unstyled fa-ul">
          <li><i class="fa fa-li fa-plus text-success"></i> The library has a search engine!</li>
        </ul>
      </li>
      <li class="list-group-item">
        <h4 class="list-group-heading">22-02-2017</h4>
        <p>The database is back and I fixed a bunch of stuff!</p>
        <ul class="list-unstyled fa-ul">
          <li><i class="fa fa-li fa-check text-info"></i> Individual stat pages should work better, along with rounds as well.</li>
        </ul>
      </li>
      <li class="list-group-item">
        <h4 class="list-group-heading">13-02-2017</h4>
        <p>The central database is down, so I'm tweaking some other stuff, like:</p>
        <ul class="list-unstyled fa-ul">
          <li><i class="fa fa-li fa-plus text-success"></i> Adding this changelog!</li>
          <li><i class="fa fa-li fa-check text-info"></i> Refactoring CSS</li>
          <li><i class="fa fa-li fa-check text-info"></i> FontAwesome!</li>
          <li><i class="fa fa-li fa-plus text-success"></i> Sticky table headers!</li>
          <li><i class="fa fa-li fa-plus text-success"></i> Pages for individual stats! <a href="<?php echo $app->APP_URL;?>/stats/singleStat.php?stat=radio_usage">Check out radio usage!</a></li>
        </ul>
      </li>
    </ul>
  </div>
  <div class="col-md-3">
  </div>
</div>
<?php require_once('footer.php');
