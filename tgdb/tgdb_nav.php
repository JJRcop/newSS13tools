<nav class="navbar tgdb-navbar">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed"
      data-toggle="collapse" data-target="#tgdbnav"
      aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <i class="fa fa-navicon fa-2x" style="color: white;"></i>
      </button>
      <a class="navbar-brand" href="<?php echo APP_URL;?>tgdb/index.php">
        TGDB <i class="fa fa-database"></i>
      </a>
    </div>
    <div id="tgdbnav" class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
        <li><a href="<?php echo APP_URL;?>tgdb/bans.php"><i class="fa fa-ban"></i> Ban DB</a></li>
        <li><a href="<?php echo APP_URL;?>tgdb/notes.php"><i class="fa fa-sticky-note"></i> Notes &amp; Messages DB</a></li>
        <li><a href="<?php echo APP_URL;?>tgdb/conn.php"><i class="fa fa-plug"></i> Connection DB</a></li>
        <li><a href="<?php echo APP_URL;?>tgdb/players.php"><i class="fa fa-group"></i> Player DB</a></li>
        <li><a href="<?php echo APP_URL;?>tgdb/memo.php"><i class="fa fa-envelope"></i> Memo DB</a></li>
      </ul>
      <form class="navbar-form navbar-right" role="search" style="margin-right: 0;">
        <div class="form-group">
          <input type="text" class="form-control" placeholder="ckey quick search">
        </div>
      </form>
    </div><!--/.nav-collapse -->
  </div>
</nav>