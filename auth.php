<?php

require_once('config.php');
error_reporting(-1);

if(isset($_GET['reset'])){
  die("Reset");
}

if(!defined('OAUTHREMOTE')){
  die("No remote OAuth provider specified.");
}

$step = 1;
if (isset($_GET['step'])){
  $step = filter_input(INPUT_GET,'step',FILTER_VALIDATE_INT,array(
    'min_range' => 1,
    'max_range' => 3
  ));
}

switch ($step){
  //Step 1: Create the Outh request
  default:
  case 1:
    $remote = OAUTHREMOTECREATE;

    //We need to save this
    $privToken = base64_encode(hash('sha256',time().microtime().$_SERVER['REMOTE_ADDR']));
    $_SESSION['site_private_token'] = $privToken;

    //This is where people will come back to
    $_SESSION['return_uri'] = APP_URL.'auth.php?step=3';

    //Build the request URL (Fuck you guzzle)
    $req = "$remote?site_private_token=".urlencode($privToken);
    $req.= "&return_uri=".urlencode($_SESSION['return_uri']);

    //Send it
    $client = new GuzzleHttp\Client();
    $res = $client->request('GET', $req);
    try{
      $body = $res->getBody()->getContents();
    } catch (Exception $e){
      die($e->getMessage());
    }

    //Decode the resoponse
    $body = json_decode($body);
    $_SESSION['session_private_token'] = $body->session_private_token;
    $_SESSION['session_public_token']  = $body->session_public_token;

    //YOU BLEW IT
    if ("OK" != $body->status){
      die("Error when constructing OAuth request: $body->error");
    }
    echo "<font style='font-family: sans-serif; width: 75%;";
    echo "margin: 100px auto; display: block; background: #eee;";
    echo "border: 1px solid #DDD; padding:20px;'>";
    echo "<h2>Authorize remote access</h2>";
    echo "<code>".APP_URL."</code> would like to access your account at ";
    echo "<code>".REMOTE_ROOT."</code> and retrieve your forum username ";
    echo "and Byond key, if set.<br><br>";
    echo "No other information, <strong>including your password</strong> ";
    echo "will be shared with <code>".APP_URL."</code><br><br>";
    echo "<a href='auth.php?step=2' style='background: blue; color: white;";
    echo "padding: 10px;'>Proceed</a>&nbsp;";
    echo "<a href='index.php2' style='background: red; color: white;";
    echo "padding: 10px;'>Cancel</a><br><br>";
    echo "<small style='text-align: right'>For more information, ";
    echo "<a href='https://tgstation13.org/phpBB/viewtopic.php?f=45&t=9922'";
    echo "target='_blank'>click here</a>.</font>";
  break;

  case 2:
    //Set up our redirection URL
    $location = OAUTHREMOTE."?session_public_token=".urlencode($_SESSION['session_public_token']);
    // consoleLog($location);
    //Send the user packing
    header("Location: $location");

  break;
  
  //Step 2: User approved us!
  case 3:
    if (!isset($_SESSION['session_private_token'])) die("You're in the wrong place.");
    $req = OAUTHREMOTEINFO."?site_private_token=".urlencode($_SESSION['site_private_token']);
    $req.= "&session_private_token=".urlencode($_SESSION['session_private_token']);
    //Send it
    $client = new GuzzleHttp\Client();
    $res = $client->request('GET', $req);
    try{
      $body = $res->getBody()->getContents();
    } catch (Exception $e){
      die($e->getMessage());
    }

    //Decode the resoponse
    // consoleLog($_SESSION);
    // consoleLog($body);
    $body = json_decode($body);
    if ("OK" != $body->status){
      die("Error with OAuth request: $body->error");
    } else {
      foreach($body as $k => $v){
        setcookie($k,$v,time()+1296000,DIRECTORY,DOMAIN,USE_SSL); // 15 day expiry
      }
      $user = new user();
      if($user->level){
        if($user->ip != $_SERVER['REMOTE_ADDR']) {
          $user->flagAuth();
        }
      }

      echo "<font style='font-family: sans-serif; width: 75%;";
      echo "margin: 100px auto; display: block; background: #eee;";
      echo "border: 1px solid #DDD; padding:20px;'>";
      echo "<h2>Success!</h2>";
      echo "<code>".APP_URL."</code> now recognizes you as $user->byond. ";
      echo "<br><br><a style='background: blue; color: white;";
      echo "padding: 10px;' href='".APP_URL."'>Continue</a>";
    }
  break;
}
?>