<?php
/**
 * OpenGears Framework Configuration
 * @version 1.0
 * @package opengears
 * @author Denis Sedchenko [sedchenko.in.ua]
 */

// If Config loaded
define("IFCONFIG",true);

// DEFAULT CONTROLLER (INDEX)
define("DEFAULT_CONTROLLER","main");
define("DEFAULT_ACTIVITY","main");

// HTTP HOST ROOT
define("WWW",'http://'.$_SERVER['HTTP_HOST']);

// DATABASE Access
define("DB_HOST","localhost");
define("DB_USER","root");
define("DB_PASS","root");
define("DB_BASE","XmusiX");

// DIRECTORY ROOT AND PATH SEPARATOR
define("SYSTEMROOT","");
define("DS",DIRECTORY_SEPARATOR);

// Application directory
define("APP",SYSTEMROOT."application".DS);

// Controllers, models, views
define("CONTROLLERS",APP."controller".DS);
define("MODELS",APP."model".DS);
define("VIEWS",APP."view".DS);
define("LANG",APP."i18n".DS);

//Data
define("APPDATA",APP."data".DS);

// System directory
define("CORE",SYSTEMROOT."system".DS);

// Driver's path
define("DRIVERS",CORE."drivers".DS);


// SYSTEM EXTENSIONS
define("EXTENSIONS",CORE."extensions".DS);

// CLASSES
define("CLASSES",CORE."classes".DS);


// IS MOD_REWRITE ENABLED
define("REWRITE_URI",true);

?>
