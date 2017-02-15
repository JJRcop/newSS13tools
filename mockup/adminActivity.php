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

<div class="row">
<div class="col-md-6">
    <div class="page-header">
      <h1>Current admin memos</h1>
    </div>
    <ul class="list-group">
      <li class="list-group-item">
        <blockquote>
          <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod</p>
          <footer>longmin
            <cite>2017-02-13 22:33:30</cite>
          </footer>
        </blockquote>
      </li>
                  <li class="list-group-item">
            <blockquote>
              <p>Atlanta-Ned is no longer an administrator</p>
              <footer>notatlantaned                <cite>2017-02-13 22:33:10</cite>
              </footer>
            </blockquote>
          </li>
                  <li class="list-group-item">
            <blockquote>
              <p>You're a fucking meme and you know it. Get out.</p>
              <footer>atlantaned                <cite>2017-02-13 22:22:01</cite>
              </footer>
            </blockquote>
          </li>
            </ul>
  </div>
  <div class="col-md-6">
    <div class="page-header">
      <h1>Recent Admin activity</h1>
    </div>
    <table class="table table-bordered table-condensed">
    <thead>
      <tr>
        <th>ID</th>
        <th>Admin</th>
        <th>Activity</th>
        <th>Player</th>
        <th>When</th>
      </tr>
    </thead>
    <tbody>
      <tr class='success'>
        <th><a href="#" data-toggle='tooltip' title='A direct link to the event'>#41234</a></th>
        <td><a href="#" data-toggle='tooltip' title="A link to this admin's activity page">atlantaned</a></td>
        <td><a href="#" data-toggle='tooltip' title="Same as the ID link"><i class="fa fa-bullhorn"></i> Left a message "Please ahelp before you..."</a></td>
        <td><a href="#" data-toggle='tooltip' title="A link to this player's page">Leoz</a></td>
        <td><a href="#" data-toggle='tooltip' title="The actual event timestamp, like <?php echo date('c');?>">Two minutes ag</a></td>
      </tr>

      <tr class='warning'>
        <th><a href="#">#41233</a></th>
        <td><a href="#">Cobblestone</a></td>
        <td><a href="#"><i class="fa fa-sticky-note"></i> Added a note "Warned for IC in..."</a></td>
        <td><a href="#">Mimic</a></td>
        <td><a href="#">17 minutes ago</a></td>
      </tr>

      <tr class='danger'>
        <th><a href="#">#41232</a></th>
        <td><a href="#">Sawrge</a></td>
        <td><a href="#"><i class="fa fa-times text-danger"></i> Tempbanned "15 minute ban IC in OOC"</a></td>
        <td><a href="#">MrStonedOne</a></td>
        <td><a href="#">45 minutes ago</a></td>
      </tr>

      <tr class='perma'>
        <th><a href="#">#41231</a></th>
        <td><a href="#">shaps</a></td>
        <td><a href="#"><i class="fa fa-ban text-danger"></i> Permanently banned "Bombing the bar as a..."</a></td>
        <td><a href="#">Aloraydrel</a></td>
        <td><a href="#">Two hours ago</a></td>
      </tr>
    </tbody>
    </table>
  </div>
</div>
<?php require_once('../footer.php'); ?>

