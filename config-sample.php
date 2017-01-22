<?php

if (!defined('PHP_VERSION_ID')) {
  $version = explode('.', PHP_VERSION);

  define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

define('DB_METHOD', 'mysql');//Probably won't need to change
define('DB_NAME', 'changeme');
define('DB_USER', 'changeme');
define('DB_PASS', 'changeme');
define('DB_HOST', 'localhost');//Probably won't need to change
define('TBL_PREFIX','ss13');

define("DMEDIR",'tgstation');
define("DMEFILE",DMEDIR."/tgstation.dme");
define("ICONS_DIR",DMEDIR."/icons/");

define("GENERATED_ICONS","icons");

define("TMPDIR","tmp");

define("FONTS","resources/shared/fonts");

define("PROJECT_GITHUB","tgstation/tgstation");

define("USE_SSL",FALSE);
(USE_SSL) ? define("SSL","S"): define("SSL",'');

define('APP_URL',"http".SSL."://DOMAIN.TLD/PATH/");

###### DEBUG FLAG ######
define('DEBUG', FALSE);
###### DEBUG FLAG ######

require_once('inc/functions.php');
require_once('inc/autoload.php');
require_once('inc/vendor/autoload.php');

if(DEBUG){
  ini_set('xdebug.var_display_max_depth',-1);
  ini_set('xdebug.var_display_max_data',-1);
  ini_set('xdebug.var_display_max_children',-1);
  set_time_limit(240);
}