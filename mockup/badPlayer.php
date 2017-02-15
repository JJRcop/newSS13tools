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
    <code>A Dumb Griefer</code>
    <small><a href="#"><i class="fa fa-external-link"></i> BYOND</a></small>
  </h1>
</div>

<div class="row">
  <div class="col-md-4">
    <ul class="list-group">
      <li class="list-group-item">
        <strong class="list-group-item-heading">Last rank</strong>
          Player
      </li>
      <li class="list-group-item perma">
        <strong class="list-group-item-heading">Account standing</strong>
          PermaBanned
      </li>
      <li class="list-group-item">
        <strong class="list-group-item-heading">First seen</strong>
          2017-02-13 18:12:36
      </li>
      <li class="list-group-item">
        <strong class="list-group-item-heading">Last seen</strong>
          2017-02-15 01:59:32
      </li>
      <li class="list-group-item">
        <strong class="list-group-item-heading">Total Connections</strong>
          5
      </li>
    </ul>
  </div>
  <div class="col-md-4">
    <ul class="list-group">
      <li class="list-group-item list-group-item-danger">
        <strong class="list-group-item-heading">(<i class="fa fa-flask"></i>) Grief Index™</strong>
          100
      </li>
      <li class="list-group-item">
        <strong class="list-group-item-heading">Last IP</strong>
          123.456.789.0 <div class="ql">(<a href="#"><i class="fa fa-ban"></i></a>)(<a href="#"><i class="fa fa-plug"></i></a>)(<a href="#"><i class="fa fa-user"></i></a>)(<a href="#"><i class="fa fa-search"></i></a>)</div>
      </li>
      <li class="list-group-item">
        <strong class="list-group-item-heading">Last CID</strong>
          666666666 <div class="ql">(<a href="#"><i class="fa fa-ban"></i></a>)(<a href="#"><i class="fa fa-plug"></i></a>)(<a href="#"><i class="fa fa-user"></i></a>)</div>
      </li>
      <li class="list-group-item">
        <strong class="list-group-item-heading">IPs seen</strong>
          1
      </li>
      <li class="list-group-item">
        <strong class="list-group-item-heading">CIDs seen</strong>
          1
      </li>
    </ul>
  </div>
</div>

<div id="notes">
  <div class="page-header">
    <h3>Notes &amp; Messages</h3>
  </div>

  <div class="panel panel-warning">
    <div class="panel-heading">
      <h3 class="panel-title"><i class="fa fa-sticky-note"></i> Note created by <a href="#">atlantaned</a> 1 day ago</h3>
    </div>
    <div class="panel-body">
      Warned for creating a BOH sing. Claimed they didn't know what they were doing. Keep an eye on them.
    </div>
    <div class="panel-footer">
      <small>This note was filed on Sybil and has been edited 1 time in total. It is marked as secret.</small>
    </div>
  </div>
</div>

<div id="bans">
  <div class="page-header">
    <h3>Bans</h3>
  </div>

  <div class="panel panel-perma">
    <div class="panel-heading">
      <h3 class="panel-title"><i class="fa fa-ban text-danger"></i> Permaban issued by <a href="#">atlantaned</a> 12 hours ago<p class="pull-right"><a href="#">#54326</a></p></h3>
    </div>
    <div class="panel-body">
     <p>Detonated several max-cap bombs around the station and logged before they could be questioned. Appeal if you want to explain yourself and come back.</p>
    </div>
    <table class="table table-condensed table-bordered">
      <tr>
        <th>Banned from</th>
        <td>Server</td>
      </tr>
      <tr>
        <th>Duration</th>
        <td>Permanent</td>
      </tr>
      <tr>
        <th>Status</th>
        <td class="perma">Active</td>
      </tr>
      <tr>
        <th>Ban issued</th>
        <td>2017-02-14 23:55:02</td>
      </tr>
      <tr>
        <th>Ban expires</th>
        <td><strong>Never</strong></td>
      </tr>
      <tr>
        <th>Appeal status</th>
        <td>Ineligible</td>
      </tr>
    </table>
  </div>

  <div class="panel panel-danger">
    <div class="panel-heading">
      <h3 class="panel-title"><i class="fa fa-times text-danger"></i> Tempban issued by <a href="#">atlantaned</a> 1 day ago<p class="pull-right"><a href="#">#54325</a></p></h3>
    </div>
    <div class="panel-body">
     <p>Banned - The round after they were warned for non-antag BOH singing, they detonated the borgs and were unable to provide any justificaiton as to why.</p>
    </div>
    <table class="table table-condensed table-bordered">
      <tr>
        <th>Banned from</th>
        <td>Server</td>
      </tr>
      <tr>
        <th>Duration</th>
        <td>One day</td>
      </tr>
      <tr>
        <th>Status</th>
        <td class="success">Unbanned by Mekhi (two days ago)</td>
      </tr>
      <tr>
        <th>Ban issued</th>
        <td>2017-02-14 10:13:02</td>
      </tr>
      <tr>
        <th>Ban expires</th>
        <td>2017-02-14 10:13:02 (expired) </td>
      </tr>
      <tr>
        <th>Appeal status</th>
        <td class="success">Successfully Appealed</td>
      </tr>
    </table>
  </div>

  <div class="panel panel-danger">
    <div class="panel-heading">
      <h3 class="panel-title"><i class="fa fa-times text-danger"></i> Tempban issued by <a href="#">atlantaned</a> 1 day ago <p class="pull-right"><a href="#">#54324</a></p></h3>
    </div>
    <div class="panel-body">
     <p>Banned - IC in OOC</p>
    </div>
    <table class="table table-condensed table-bordered">
      <tr>
        <th>Banned from</th>
        <td>Server</td>
      </tr>
      <tr>
        <th>Duration</th>
        <td>15 minutes</td>
      </tr>
      <tr>
        <th>Status</th>
        <td>Expired (two days ago)</td>
      </tr>
      <tr>
        <th>Ban issued</th>
        <td>2017-02-14 09:01:44</td>
      </tr>
      <tr>
        <th>Ban expires</th>
        <td>2017-02-14 09:16:44 (expired) </td>
      </tr>
      <tr>
        <th>Appeal status</th>
        <td>Ineligible</td>
      </tr>
    </table>
  </div>

  <div class="panel panel-danger">
    <div class="panel-heading">
      <h3 class="panel-title"><i class="fa fa-times text-danger"></i> Jobban issued by <a href="#">atlantaned</a> 1 day ago <p class="pull-right"><a href="#">#54323</a></p></h3>
    </div>
    <div class="panel-body">
     <p>Just to be safe</p>
    </div>
    <table class="table table-condensed table-bordered">
      <tr>
        <th>Banned from</th>
        <td>Security Officer, Detective, Warden, Head of Security</td>
      </tr>
      <tr>
        <th>Duration</th>
        <td>Permanent</td>
      </tr>
      <tr>
        <th>Status</th>
        <td class="success">Active</td>
      </tr>
      <tr>
        <th>Ban issued</th>
        <td>2017-02-14 09:01:44</td>
      </tr>
      <tr>
        <th>Ban expires</th>
        <td><strong>Never</strong></td>
      </tr>
      <tr>
        <th>Appeal status</th>
        <td>Appeal filed (view)</td>
      </tr>
    </table>
  </div>
</div>

<?php require_once('../footer.php'); ?>

