<?php require_once('config.php');
PHP_Timer::start();
$user = new user();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SS13 Tools</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.17.7/js/jquery.tablesorter.min.js"></script>
  <style>
  body {
    padding-top: 60px;
  }
  img {
    image-rendering: -moz-crisp-edges;         /* Firefox */
    image-rendering:   -o-crisp-edges;         /* Opera */
    image-rendering: -webkit-optimize-contrast;/* Webkit (non-standard naming) */
    image-rendering: crisp-edges;
    -ms-interpolation-mode: nearest-neighbor;  /* IE (non-standard property) */
  }
  #output {
    text-align: center;
    margin: 0 0 20px 0;
  }
  #deathmap {
    display: block;
    width: 100%;
    height: auto;
  }
  td, th {
    vertical-align: middle !important;
  }
  tr.bad-round {
    color: grey;
    font-size: 8px;
    background: #eeeeee;
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
  .OOC      { color: #002eb8; }
  .ADMIN    { color: #b82e00; font-weight: bold; }
  .SAY      { color: #008000; }
  .ACCESS   { color: #ff5050; }
  .WHISPER  { font-style: italic; }
  .EMOTE    { font-style: italic; }
  .GAME     { color: #00c000; font-weight: bold; }
  .PDA      { color: #800000; }
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
  .ts,
  .lt {
    text-align: left;
    padding: 0 5px;
    color: black;
    font-weight: normal;
    font-style: normal;
  }
  .lt {
    text-align: right;
    padding-right: 5px;
  }
  .book, .round {
    color: #337ab7;
  }
  .book:hover,
  .round:hover {
    cursor: pointer;
    cursor: hand; 
    text-decoration: underline;
  }
  .library-adult {
    background: grey;
    color: grey;
  }
  .library-adult:hover {
    background: transparent;
    color: inherit;
  }
  .sort {
    width: 100%;
  }
  .sort thead tr th {
    background-repeat: no-repeat;
    background-position: right center;
  }
  .tablesorter-headerUnSorted {
    background-image: url(data:image/gif;base64,R0lGODlhFQAJAIAAACMtMP///yH5BAEAAAEALAAAAAAVAAkAAAIXjI+AywnaYnhUMoqt3gZXPmVg94yJVQAAOw==); }

  .tablesorter-headerAsc {
    background-image: url(data:image/gif;base64,R0lGODlhFQAEAIAAACMtMP///yH5BAEAAAEALAAAAAAVAAQAAAINjI8Bya2wnINUMopZAQA7); }

  .tablesorter-headerDesc {
    background-image: url(data:image/gif;base64,R0lGODlhFQAEAIAAACMtMP///yH5BAEAAAEALAAAAAAVAAQAAAINjB+gC+jP2ptn0WskLQA7); }
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
          </ul>
        </li>
        <?php if ($user->valid):?>
          <li><a href="<?php echo APP_URL;?>library/catalog.php">Library</a></li>
        <?php endif;?>
        <li class="label-danger">
          <a style="color: white;" href="https://github.com/nfreader/newSS13tools/issues" target="_blank"><span class="glyphicon glyphicon-alert"></span> I found a bug!</a>
        </li>
      </ul>
      <p class="navbar-text navbar-right">
      <?php if($user->legit):?>
        Identified as <?php echo $user->label;?>
      <?php endif;?>
      </p>
<!--       <p class="navbar-text pull-right">
        You are not logged in
      </p> -->
    </div><!--/.nav-collapse -->
  </div>
</nav>