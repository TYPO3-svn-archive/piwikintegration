<?php
ini_set("memory_limit", "512M");
error_reporting(E_ALL|E_NOTICE);
define('PIWIK_INCLUDE_PATH', dirname(__FILE__) . '/../../../');
ignore_user_abort(true);
set_time_limit(0);
set_include_path(PIWIK_INCLUDE_PATH 
					. PATH_SEPARATOR . PIWIK_INCLUDE_PATH . '/libs/'
					. PATH_SEPARATOR . PIWIK_INCLUDE_PATH . '/plugins/'
					. PATH_SEPARATOR . PIWIK_INCLUDE_PATH . '/core'
					. PATH_SEPARATOR . get_include_path() );
$GLOBALS['PIWIK_TRACKER_DEBUG'] = false;
define('PIWIK_ENABLE_DISPATCH', false);
require_once "FrontController.php";

Piwik_FrontController::getInstance()->init();

require_once "PluginsManager.php";
require_once "Timer.php";
require_once "Cookie.php";

Piwik::setUserIsSuperUser();
$geoIp = new Piwik_GeoIP();
$geoIp->updateExistingVisitsWithGeoIpData();
