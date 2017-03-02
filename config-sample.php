<?php
mb_internal_encoding("UTF-8");

###### Application Settings ######
define("DOMAIN",'domain.tld'); //Domain name where the app is running
define("DIRECTORY",'<path to install>'); //Directory where the app is installed
define("USE_SSL",FALSE); //Whether or not to use SSL
(USE_SSL) ? define("SSL","s") : define("SSL",'');
define('APP_URL',"http".SSL."://".DOMAIN."/".DIRECTORY);
define('APP_NAME','SS13 Tools'); //Name of the application

define('DATE_FORMAT','Y-m-d H:i:s'); //Date format

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
define("DMEDIR",ROOTPATH."/tgstation");
define("DMEFILE",DMEDIR."/tgstation.dme");
define("ICONS_DIR",DMEDIR."/icons/");

###### Directories for generated content ######
define("GENERATED_ICONS","icons");
define("TMPDIR","tmp");
define("FONTS","resources/shared/fonts");

###### Codebase Github information ######
define("PROJECT_GITHUB","tgstation/tgstation");
define("REMOTE_LOG_SRC","https://tgstation13.org/parsed-logs/");

###### OAuth settings ######
#Uncomment to enable
// define('REMOTE_ROOT','<OAUTH PROVIDER URL>');
// define('OAUTHREMOTECREATE',REMOTE_ROOT.'oauth_create_session.php');
// define('OAUTHREMOTE',REMOTE_ROOT.'oauth.php');
// define('OAUTHREMOTEINFO',REMOTE_ROOT.'oauth_get_session_info.php');

###### DEBUG FLAG ######
define('DEBUG', TRUE);
###### DEBUG FLAG ######

###### DO NOT EDIT BELOW THIS LINE ######

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