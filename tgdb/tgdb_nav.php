<nav class="navbar tgdb-navbar">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed"
      data-toggle="collapse" data-target="#tgdbnav"
      aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <i class="fa fa-navicon fa-2x" style="color: white;"></i>
      </button>
      <a class="navbar-brand" href="<?php echo $app->APP_URL;?>tgdb/index.php">
        <i class="fa fa-database"></i> TGDB
      </a>
    </div>
    <div id="tgdbnav" class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
        <li><a href="<?php echo $app->APP_URL;?>tgdb/bans.php"><i class="fa fa-ban"></i> Ban DB</a></li>
        <li><a href="<?php echo $app->APP_URL;?>tgdb/messages.php"><i class="fa fa-sticky-note"></i> Notes &amp; Messages DB</a></li>
        <li><a href="<?php echo $app->APP_URL;?>tgdb/conn.php"><i class="fa fa-plug"></i> Connection DB</a></li>
        <li><a href="<?php echo $app->APP_URL;?>tgdb/players.php"><i class="fa fa-group"></i> Player DB</a></li>
        <li><a href="<?php echo $app->APP_URL;?>tgdb/polls.php"><i class="fa fa-envelope"></i> Poll DB</a></li>
        <li><a href="<?php echo $app->APP_URL;?>tgdb/comments.php"><i class="fa fa-bullhorn"></i> Comments DB</a></li>
      </ul>
      <form class="navbar-form navbar-right" role="search" style="margin-right: 0;">
        <div class="form-group typeahead__container">
          <input type="text" class="form-control" id="search" name="ckey"
          placeholder="ckey quick search">
        </div>
      </form>
    </div><!--/.nav-collapse -->
  </div>
</nav>

<script src='../resources/js/jquery.typeahead.min.js'></script>
<link rel='stylesheet' href='../resources/css/jquery.typeahead.min.css' />

<script>

$("#search").submit(function(e){
  e.preventDefault();
});

$.typeahead({
  input: '#search',
  minLength: 2,
  order: 'desc',
  source: {
    ajax: {
      url: 'ckeySuggest.php',
      data: {
        query: '{{query}}'
      }
    }
  },
  dynamic: true,
  debug: true,
  hint: true,
  mustSelectItem: true,
  display: ['ckey'],
  href: function (item) {
    return "viewPlayer.php?ckey=" + item.ckey
  },
  callback: {
    onClickAfter: function (node, a, item, event) {
      event.preventDefault;
      window.location.href = item.href;
    }
  }
})
</script>