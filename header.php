<?php require_once('config.php');
PHP_Timer::start();
if(!isset($skip)) $skip = false;
if(!isset($wide)) $wide = false;
$app = new app();
if (isset($_GET['tgui'])) {
  $tgui = filter_input(INPUT_GET, 'tgui', FILTER_VALIDATE_BOOLEAN);
  setcookie('tgui',$tgui,COOKIE_LIFTIME,"/",DOMAIN,USE_SSL);
}
$user = new user();
$death = new death();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $app->app_name;?></title>
  <link rel="stylesheet" href="<?php echo $app->APP_URL;?>/resources/css/style.css<?php if (DEBUG) echo "?".time();?>">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  <link href="<?php echo $app->APP_URL;?>/resources/vendor/c3/c3.css" rel="stylesheet" type="text/css">
  <?php if ($user->tgui):?>
  <link href="<?php echo $app->APP_URL;?>/tgstation/tgui/assets/tgui.css" rel="stylesheet" type="text/css">
  <?php endif;?>
  <script src="<?php echo $app->APP_URL;?>/resources/vendor/d3/d3.min.js" charset="utf-8"></script>
  <script src="<?php echo $app->APP_URL;?>/resources/vendor/c3/c3.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.17.7/js/jquery.tablesorter.min.js"></script>

</head>
<body class="<?php echo ($user->tgui)?'tgui':'';?>">
<?php if(defined('UA')) :?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', '<?php echo UA;?>', 'auto');
  ga('send', 'pageview');

</script>
<?php endif;?>
<?php if(!$wide):?>
<div class="container">
<?php else:?>
<div class="container-fluid">
<?php endif;?>
<?php require_once(ROOTPATH.'/inc/view/nav.php');
if (defined('NOTICE')){
  echo NOTICE;
}
if(!$skip):
  $die = $app->restrictionCheck($user);
  if ($app->die) exit($die); //Application will exit if user is not auth'd
  $deathGraph = $death->countDeathsByDays();?>

<script>
var chart = c3.generate({
    bindto: '#deathChart',
    data: {
      json: <?php echo json_encode($deathGraph);?>,
      keys: {
        value: ['deaths', 'day']
      },
      x: 'day',
      y: 'deaths',
      type: 'bar'
    },
    axis: {
      x: {
        type: 'category',
        show: false
      },
      y: {
        type: 'category',
        show: false
      }
    },
    size: {
      height: 32,
      width: 35
    },
    legend: {
      show: false
    },
    color: {
      pattern: ['#9d9d9d']
    }
});
</script>
<?php endif; ?>
