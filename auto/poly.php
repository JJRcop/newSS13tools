<?php
require __DIR__ . '/../config.php';
PHP_Timer::start();
$app = new app();
if(!$app->isCLI()){
  die("This script can only be executed on the command line.");
}
$urls = array(
  REMOTE_WEB."/parsed-logs/sybil/data/npc_saves/polytalk.json.gz",
  REMOTE_WEB."/parsed-logs/basil/data/npc_saves/polytalk.json.gz",
  );
$client = new GuzzleHttp\Client();
$res = $client->request('GET',pick($urls),[
  'headers' => ['Accept-Encoding' => 'gzip'],
  ]);
$json = $res->getBody()->getContents();
$poly = fopen(ROOTPATH.'/tmp/poly.json', 'w+');
fwrite($poly,$json);
fclose($poly);
echo date('[r]')." Got Poly's lines in ".PHP_Timer::resourceUsage()."\n\r";