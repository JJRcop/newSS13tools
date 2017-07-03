<?php
mb_internal_encoding("UTF-8");

###### Application Settings ######
define("DOMAIN","domain.tld"); //Domain name where the app is running
define("DIRECTORY",'<directory>/'); //Directory where the app is installed
define("USE_SSL",FALSE); //Whether or not to use SSL

define('APP_URL',"http".SSL."://".DOMAIN."/".DIRECTORY);
define('APP_NAME','Application Name'); //Name of the application
define('DATE_FORMAT','Y-m-d H:i:s'); //Date format

//Google analytics tracking code. Uncomment to enable analytics tracking
// define('UA','UA-XXXXXXXX-X'); 

###### Game server information ######
define('GAME_SERVERS',array(
    'Edgar'=>array(
      'address'=>'game.domain.tld',
      'port'=>6666,
      'servername'=>'MurderSim: Server 1 (Edgar)',
      'name'=>'Edgar'
    )
  )
);

###### Database settings for the stats database ######
define('DB_METHOD', 'mysql');//Probably won't need to change
define('DB_NAME', 'CHANGEME');
define('DB_USER', 'CHANGEME');
define('DB_PASS', 'CHANGEME');
define('DB_HOST', 'localhost');//Probably won't need to change
define('DB_PORT', 3306);
define('TBL_PREFIX','ss13');

###### Settings for the metadata database ######
define('ALT_DB_METHOD', 'mysql');//Probably won't need to change
define('ALT_DB_NAME', 'CHANGEME');
define('ALT_DB_USER', 'CHANGEME');
define('ALT_DB_PASS', 'CHANGEME');
define('ALT_DB_HOST', 'CHANGEME');//Probably won't need to change
define('ALT_DB_PORT', 3306);

###### Settings for finding stuff in the codebase ######
define("ROOTPATH", __DIR__);
define("PROJECT_FILE",'murderSim');
define("DMEDIR",ROOTPATH."/".PROJECT_FILE);
define("DMEFILE",DMEDIR."/murderSim.dme");
define("ICONS_DIR",DMEDIR."/icons/");
define('MAP_DIR',DMEDIR.'/_maps');

###### Directories for generated content ######
define("GENERATED_ICONS",ROOTPATH."/icons");
define("TMPDIR",ROOTPATH."/tmp");
define("FONTS","resources/shared/fonts");

###### Codebase Github information ######
define("PROJECT_GITHUB","murderSim/murderSim");

###### Remote Webserver ######
// define('REMOTE_WEB','https://remoteServer.tld');
// define("REMOTE_LOG_SRC",REMOTE_WEB."/path/to/logs/");

###### Rank verification option ######
// define('TXT_RANK_VERIFY',array(
//   REMOTE_LOG_SRC.'basil/config/admins.txt',
//   REMOTE_LOG_SRC.'sybil/config/admins.txt'
// ));

###### OAuth settings ######
// define('REMOTE_ROOT','<OAUTH PROVIDER URL>');
// define('OAUTHREMOTECREATE',REMOTE_ROOT.'oauth_create_session.php');
// define('OAUTHREMOTE',REMOTE_ROOT.'oauth.php');
// define('OAUTHREMOTEINFO',REMOTE_ROOT.'oauth_get_session_info.php');

###### 2FA settings ######
define('2FA',FALSE);

###### Cookie settings ######
define('COOKIE_LIFTIME',time()+1296000); //15 days
define('SESSION_EXPIRY',time()+900); //15 minutes

###### DEBUG FLAG ######
define('DEBUG', FALSE);
###### DEBUG FLAG ######

###### DO NOT EDIT BELOW THIS LINE ######
(USE_SSL) ? define("SSL","s") : define("SSL",'');
define('APP_URL',"http".SSL."://".DOMAIN."/".DIRECTORY); //Do not edit

###### DO NOT EDIT BELOW THIS LINE ######
require_once('inc/constants.php');
require_once('inc/functions.php');
require_once('inc/autoload.php');
require_once('inc/vendor/autoload.php');

if(DEBUG){
  ini_set('xdebug.var_display_max_depth',-1);
  ini_set('xdebug.var_display_max_data',-1);
  ini_set('xdebug.var_display_max_children',-1);
  set_time_limit(240);
}

session_start();
