<?php require_once('config.php');
PHP_Timer::start();
$app = new app();
$user = new user();
$death = new death();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $app->app_name;?></title>
  <link rel="stylesheet" href="<?php echo $app->app_URL;?>/resources/css/style.css<?php if (DEBUG) echo "?".time();?>">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  <link href="<?php echo $app->app_URL;?>/resources/vendor/c3/c3.css" rel="stylesheet" type="text/css">
  <script src="<?php echo $app->app_URL;?>/resources/vendor/d3/d3.min.js" charset="utf-8"></script>
  <script src="<?php echo $app->app_URL;?>/resources/vendor/c3/c3.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.17.7/js/jquery.tablesorter.min.js"></script>

</head>
<body>
<div class="container">
<?php require_once('nav.php');

if (defined('NOTICE')){
  echo NOTICE;
}

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
      width: 32
    },
    legend: {
      show: false
    },
    color: {
      pattern: ['#9d9d9d']
    }
});
</script>


