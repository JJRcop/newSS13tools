<?php require_once('../header.php'); ?>

<nav class="navbar tgdb-navbar">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed"
      data-toggle="collapse" data-target="#tgdbnav"
      aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <i class="fa fa-navicon fa-2x" style="color: white;"></i>
      </button>
      <a class="navbar-brand" href="#">
        TGDB <i class="fa fa-database"></i>
      </a>
    </div>
    <div id="tgdbnav" class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
        <li><a href="#"><i class="fa fa-ban"></i> Ban DB</a></li>
        <li><a href="#"><i class="fa fa-sticky-note"></i> Notes &amp; Messages DB</a></li>
        <li><a href="#"><i class="fa fa-plug"></i> Connection DB</a></li>
        <li><a href="#"><i class="fa fa-group"></i> Player DB</a></li>
        <li><a href="#"><i class="fa fa-envelope"></i> Memo DB</a></li>
      </ul>
      <form class="navbar-form navbar-right" role="search" style="margin-right: 0;">
        <div class="form-group">
          <input type="text" class="form-control" placeholder="ckey quick search">
        </div>
      </form>
    </div><!--/.nav-collapse -->
  </div>
</nav>

<div class="page-header">
  <h1>
    <small>player overview</small>
    <code>Atlanta-ned</code>
    <small><a href="#"><i class="fa fa-external-link"></i> BYOND</a></small>
  </h1>
</div>

<div class="row">
  <div class="col-md-4">
    <ul class="list-group">
      <li class="list-group-item" style="background: #9570c0; color: white;">
        <strong class="list-group-item-heading">Last rank</strong>
          GameAdmin
      </li>
      <li class="list-group-item list-group-item-success">
        <strong class="list-group-item-heading">Account standing</strong>
          Not banned
      </li>
      <li class="list-group-item">
        <strong class="list-group-item-heading">First seen</strong>
          2013-04-22 17:54:40
      </li>
      <li class="list-group-item">
        <strong class="list-group-item-heading">Last seen</strong>
          2017-02-15 02:39:28
      </li>
      <li class="list-group-item">
        <strong class="list-group-item-heading">Total Connections</strong>
          4750
      </li>
    </ul>
  </div>
  <div class="col-md-4">
    <ul class="list-group">
      <li class="list-group-item list-group-item-success">
        <strong class="list-group-item-heading">(<i class="fa fa-flask"></i>) Grief Index™</strong>
          0
      </li>
      <li class="list-group-item">
        <strong class="list-group-item-heading">Last IP</strong>
          108.202.100.29 <div class="ql">(<a href="#"><i class="fa fa-ban"></i></a>)(<a href="#"><i class="fa fa-plug"></i></a>)(<a href="#"><i class="fa fa-user"></i></a>)(<a href="#"><i class="fa fa-search"></i></a>)</div>
      </li>
      <li class="list-group-item">
        <strong class="list-group-item-heading">Last CID</strong>
          522087567 <div class="ql">(<a href="#"><i class="fa fa-ban"></i></a>)(<a href="#"><i class="fa fa-plug"></i></a>)(<a href="#"><i class="fa fa-user"></i></a>)</div>
      </li>
      <li class="list-group-item">
        <strong class="list-group-item-heading">IPs seen</strong>
          6
      </li>
      <li class="list-group-item">
        <strong class="list-group-item-heading">CIDs seen</strong>
          6
      </li>
    </ul>
  </div>
</div>

<div id="notes">
  <div class="page-header">
    <h3>Notes &amp; Messages</h3>
  </div>

  <div class="panel panel-success">
    <div class="panel-heading">
      <h3 class="panel-title"><i class="fa fa-bullhorn"></i> Message created by <a href="#">mrstonedone</a> just now</h3>
    </div>
    <div class="panel-body">
      The database is back up. Sorry it took so long.
    </div>
    <div class="panel-footer">
      <small>This message was filed on Sybil and has not been delivered to the player yet</small>
    </div>
  </div>

  <div class="panel panel-warning">
    <div class="panel-heading">
      <h3 class="panel-title"><i class="fa fa-sticky-note"></i> Note created by <a href="#">aloraydrel</a> 3 months ago</h3>
    </div>
    <div class="panel-body">
      INCREDIBLY SMART MAN nerd
    </div>
    <div class="panel-footer">
      <small>This note was filed on Sybil and has been edited 1 time in total</small>
    </div>
  </div>

  <div class="panel panel-warning">
    <div class="panel-heading">
      <h3 class="panel-title"><i class="fa fa-sticky-note"></i> Note created by <a href="#">atlantaned</a> 4 months ago</h3>
    </div>
    <div class="panel-body">
      Removes meme notes
    </div>
    <div class="panel-footer">
      <small>This note was filed on Sybil</small>
    </div>
  </div>

  <div class="panel panel-warning">
    <div class="panel-heading">
      <h3 class="panel-title"><i class="fa fa-sticky-note"></i> Note created by <a href="#">bgobandit</a> 10 months ago</h3>
    </div>
    <div class="panel-body">
      ANTAG TOKEN -- Wiki contest prize. Remove when redeemed. -bandit
    </div>
    <div class="panel-footer">
      <small>This note was filed on Sybil</small>
    </div>
  </div>
</div>

<?php require_once('../footer.php'); ?>

