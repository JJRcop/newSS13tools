<?php require_once('../header.php'); ?>
<?php require_once('../auth_check.php'); ?>

<div class="page-header"><h1>Import logs</h1></div>
<p class="lead">I will attempt to parse and import the public log files into a database!</p>
<?php

//$db = new database(LOG_DBS, LOG_DB_USER, LOG_DB_PASS);

$logfile = 'https://tgstation13.org/parsed-logs/sybil/logs/2017/01-January/23-Monday.txt';

// $curl = curl_init();
// curl_setopt_array($curl, array(
//   CURLOPT_RETURNTRANSFER => 1,
//   CURLOPT_URL => $logfile,
//   CURLOPT_USERAGENT => "atlantaned.space log parser",
//   CURLOPT_SSL_VERIFYPEER => 0,
//   CURLOPT_SSL_VERIFYHOST => 0,
//   CURLOPT_FOLLOWLOCATION => true,
//   CURLOPT_REFERER => "atlantaned.space",

// ));
// $logs = curl_exec($curl);
// curl_close($curl);

$logs = file_get_contents("../tmp/sybil-23-Monday.txt");
$logs = str_replace("-censored(misc)-\r\n",'',$logs);
$logs = str_replace("-censored(asay/apm/ahelp/notes/etc)-\r\n",'',$logs);
$logs = str_replace("-\r\n", '', $logs);
$logs = preg_replace("/(\[)(\d{2}:\d{2}:\d{2})(])(GAME|ACCESS|SAY|OOC|ADMIN|EMOTE|WHISPER|PDA|CHAT|LAW|PRAY)(:\s)/","$2#-#$4#-#", $logs);
$logs = explode("\r\n",$logs);
array_filter($logs);

$db = new database(LOG_DBS, LOG_DB_USER, LOG_DB_PASS);
$db->query("INSERT INTO tglogs(timestamp, logtype, content, server) VALUES (?,?,?,'sybil');");
$i = 0;
foreach ($logs as $log){
  $log = explode('#-#',$log);

  $db->bind(1,'2017-01-23 '.$log[0]);
  $db->bind(2,$log[1]);
  $db->bind(3,trim($log[2]));
  try {
    $db->execute();
  } catch (Exception $e) {
    return returnError("Database error: ".$e->getMessage());
  }
  $i++;
} 

echo "<code>Added $i lines to the database</code>"; ?>

<?php require_once('../footer.php'); ?>