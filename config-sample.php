<?php

if (!defined('PHP_VERSION_ID')) {
  $version = explode('.', PHP_VERSION);

  define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

define("DMEDIR",'tgstation');
define("DMEFILE",DMEDIR."/tgstation.dme");
define("TMPDIR","tmp");

define("ICONS_DIR",DMEDIR."/icons/");
define("GENERATED_ICONS","icons");

define("FONTS","resources/shared/fonts");

define("PROJECT_GITHUB","tgstation/tgstation");

define("USE_SSL",FALSE);
(USE_SSL) ? define("SSL","S"): define("SSL",'');

define('APP_URL',"http".SSL."://DOMAIN.TLD/SUBDIR/");

require_once('inc/functions.php');
require_once('inc/autoload.php');
require_once('inc/vendor/autoload.php');

ini_set('xdebug.var_display_max_depth',-1);
ini_set('xdebug.var_display_max_data',-1);
ini_set('xdebug.var_display_max_children',-1);
set_time_limit(240);