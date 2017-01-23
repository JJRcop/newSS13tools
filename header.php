<?php require_once('config.php');
PHP_Timer::start();
$user = new user();?>

<!DOCTYPE html>
<html>
<head>
  <title>SS13 Tools</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  <style>
  body {
    padding-top: 60px;
  }
  td, th {
    vertical-align: middle !important;
  }
  .text-brute {color: orange;}
  .text-brain {color: green;}
  .text-fire  {color: red;}
  .text-oxy   {color: blue;}
  .brute {background-color: orange;}
  .brain {background-color: green;}
  .fire  {background-color: red;}
  .oxy   {background-color: blue;}
  .censored { color: #bbbbbb; font-size: small; }
  .ooc      { color: #002eb8; }
  .admin    { color: #b82e00; font-weight: bold; }
  .say      { color: #008000; }
  .access   { color: #ff5050; }
  .whisper  { font-style: italic; }
  .emote    { font-style: italic; }
  .game     { color: #00c000; font-weight: bold; }
  .pda      { color: #800000; }
  .log-wrap {
    width: 100%;
    overflow-x: auto;
  }
  .logs {
    font-family: Monospace;
    width: auto;
    border: 1px solid grey;
  }
  .logs td {
    vertical-align: top !important;
  }
  .logs td:first-child {
    background: rgb(225,225,225);
  }
  .logs tr:target{
    background: yellow;
  }
  .logs tr:hover {
    background: rgba(255,255,0,.5);
  }
  .ln,
  .ts {
    text-align: left;
    padding: 0 5px;
  }
  </style>
</head>
<body>
<div class="container">
<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?php echo APP_URL;?>index.php">SS13 Tools</a>
    </div>
    <div id="navbar" class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
        <?php if ($user->legit): ?>
        <li><a href="<?php echo APP_URL;?>info.php">System Info</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Icon Tools
            <span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="<?php echo APP_URL;?>tools/listDMIs.php">List DMIs</a></li>
            <li><a href="<?php echo APP_URL;?>tools/generateallPNGs.php">Generate all mob PNGs</a></li>
          </ul>
        </li>
        <?php endif; ?>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Image Generators
            <span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="<?php echo APP_URL;?>generators/bio.php">Bio</a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Stats
            <span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="<?php echo APP_URL;?>stats/listRounds.php">Round List</a></li>
            <li><a href="<?php echo APP_URL;?>stats/deaths.php">Deaths</a></li>
            <?php if ($user->legit): ?>
            <li class="divider">
            <li><a href="<?php echo APP_URL;?>tools/importLogs.php">Import Logs</a></li>
            <?php endif; ?>
          </ul>
        </li>
      </ul>
      <p class="navbar-text navbar-right">
        <?php echo $user->label;?>
      </p>
<!--       <p class="navbar-text pull-right">
        You are not logged in
      </p> -->
    </div><!--/.nav-collapse -->
  </div>
</nav>