<?php

require_once('config.php');
$app = new app();
error_reporting(-1);

if(isset($_GET['reset'])){
  die("Reset");
}

if('remote' != $app->auth_method){
  die("No remote OAuth provider specified. Defaulting to gameserver database checking.");
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
    $privToken = base64_encode($app->generateToken(TRUE));
    $_SESSION['site_private_token'] = $privToken;

    //This is where people will come back to
    $_SESSION['return_uri'] = $app->APP_URL.'auth.php?step=3';

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
    require_once(ROOTPATH.'/inc/view/header-lite.php');?>
    <div class="page-header">
    <h2>Authorize remote access</h2>
    </div>
    <p><code><?php echo $app->app_name;?></code> would like to access the following account information from <code><?php echo REMOTE_ROOT;?></code>:
    <ul>
      <li>Your PhpBB Username</li>
      <li>Your Byond key, if set</li>
    </ul>
    </p>

    <p>No other information, <em>including your passwords</em> will be shared with <code><?php echo $app->app_name;?></code>.</p>

    <p><small>When you click proceed, you will be directed to <code><?php echo REMOTE_ROOT;?></code> to complete the authentication process.</small></p>

    <p><a class="btn btn-primary" href="auth.php?step=2">Proceed</a> <a class="btn btn-default" href="<?php echo $app->APP_URL;?>">Cancel</a></p>

    <?php 
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
      $app->logEvent("ATF","Failed to authenticate with remote: $body->error");
      die("Error with OAuth request: $body->error");
    } else {
      foreach($body as $k => $v){
        $_SESSION[$k] = $v;
      }
      $_SESSION['expiry'] = SESSION_EXPIRY; //Fifteen minutes
      $app->logEvent("AUT","Authenticated via remote");
      $user = new user();
      if(1 <= $user->level){
        if($user->ip != $_SERVER['REMOTE_ADDR']) {
          $user->flagAuth();
        }
      }
      require_once(ROOTPATH.'/inc/view/header-lite.php');?>
      <div class="page-header">
      <h2>Success!</h2>
      </div>
      <p><h1><?php echo $user->label;?></h1></p>
      <p><code><?php echo $app->app_name;?></code> now recognizes you!</p>

      <p><a class="btn btn-primary" href="<?php echo $app->APP_URL;?>">Continue</a>

      <?php 
    }
  break;
}

require_once(ROOTPATH.'/footer.php');
