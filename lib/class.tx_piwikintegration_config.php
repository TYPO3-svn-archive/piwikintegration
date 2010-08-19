<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010     Kay Strobach (typo3@kay-strobach.de),
*
*  All rights reserved
*
*  This script is free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; version 2 of the License.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

class tx_piwikintegration_config {
	private static $configObject = null;
	private        $installer    = null;
	/**
	 *
	 */	 	
	private function __construct() {
		$this->installer = tx_piwikintegration_install::getInstaller();
		$this->initPiwikFrameWork();
	}
	/**
	 *
	 */	 	
	public static function getConfigObject() {
		if(self::$configObject == null) {
			self::$configObject = new tx_piwikintegration_config();
		}
		return self::$configObject;
	}
	/**
	 *
	 */
	function initPiwikFrameWork() {
		//load files from piwik
			if(!defined('PIWIK_INCLUDE_PATH'))
			{
				define('PIWIK_INCLUDE_PATH', PATH_site.'typo3conf/piwik/piwik/');
				define('PIWIK_USER_PATH'   , PATH_site.'typo3conf/piwik/piwik/');
			}
			if(!defined('PIWIK_INCLUDE_SEARCH_PATH'))
			{
				define('PIWIK_INCLUDE_SEARCH_PATH',
					  PIWIK_INCLUDE_PATH . '/core'
					. PATH_SEPARATOR . PIWIK_INCLUDE_PATH . '/libs'
					. PATH_SEPARATOR . PIWIK_INCLUDE_PATH . '/plugins'
					. PATH_SEPARATOR . get_include_path());
				@ini_set('include_path', PIWIK_INCLUDE_SEARCH_PATH);
				@set_include_path(PIWIK_INCLUDE_SEARCH_PATH);
			}
			set_include_path(PIWIK_INCLUDE_PATH
						. PATH_SEPARATOR . PIWIK_INCLUDE_PATH . '/libs/'
						. PATH_SEPARATOR . PIWIK_INCLUDE_PATH . '/plugins/'
						. PATH_SEPARATOR . PIWIK_INCLUDE_PATH . '/core/'
						. PATH_SEPARATOR . get_include_path());
			require_once PIWIK_INCLUDE_PATH .'libs/upgradephp/upgrade.php';
			require_once PIWIK_INCLUDE_PATH .'core/Loader.php';
			require_once('core/Piwik.php');
			require_once('core/Config.php');
			require_once('core/PluginsManager.php');
		//create config object
			Piwik::createConfigObject(PIWIK_INCLUDE_PATH.'config/config.ini.php');
		
		//define Table prefix for internal use
			$this->tableDbPrefix = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['piwikintegration']);
			$this->tableDbPrefix = $this->tableDbPrefix['databaseTablePrefix'];
			if($this->tableDbPrefix != '') {
				$this->tableDbPrefix.= '.';
			}
			$this->tablePrefix = $this->tableDbPrefix.'tx_piwikintegration_';
		
	}
	function initPiwikDatabase() {
		include_once(PIWIK_INCLUDE_PATH.'/core/Option.php');
		Piwik::createConfigObject(PIWIK_INCLUDE_PATH.'config/config.ini.php');
		#$piwikConfig = Zend_Registry::get('config');
		Piwik::createDatabaseObject();
	}
	function makePiwikConfigured() {
		global $typo_db_host,
		       $typo_db_username,
		       $typo_db_password,
		       $typo_db,
			   $BE_USER;
		$this->initPiwikFrameWork();
		//makeConfigObject
		$piwikConfig = Zend_Registry::get('config');

		//userdata
		$superuser = $piwikConfig->superuser->toArray();
		$superuser['login']    = md5(microtime());
		$superuser['password'] = md5(microtime());
		$superuser['email']    = $GLOBALS["BE_USER"]->user['email'];
		
		$piwikConfig->superuser = $superuser;

		//Database
		$database = $piwikConfig->database->toArray();
		$database['host']          = TYPO3_db_host;
		$database['username']      = TYPO3_db_username;
		$database['password']      = TYPO3_db_password;
		$database['dbname']        = TYPO3_db;
		$database['tables_prefix'] = $this->tablePrefix;
		$database['adapter']       = "PDO_MYSQL";
		#$piwikConfig->database = new Zend_Config($database);
		$piwikConfig->database = $database;

		//General
		$general = $piwikConfig->General->toArray();
		$general['show_website_selector_in_user_interface'] = 0;
		#$piwikConfig->General = new Zend_Config($general);
		$piwikConfig->General = $general;

		//force Load of TYPO3Login! and deny Login
		$plugins     = $piwikConfig->Plugins->toArray();
		$key_login   = array_search('Login'     ,$plugins);
		$key_t3login = array_search('TYPO3Login',$plugins);
		$key_t3menu  = array_search('TYPO3Menu' ,$plugins);
		//unload login
		if($key_login!==false) {
			unset($plugins[$key_login]);
		}
		//load typo3login
			if($key_t3login===false) {
				$plugins[]='TYPO3Login';
			}
		//load interface modifications
			if($key_t3menu===false) {
				$plugins[]='TYPO3Menu';
			}
		//write Config back
		$piwikConfig->Plugins = $plugins;

		//create PiwikTables, check wether base tables already exist
			Piwik::createDatabaseObject();
			$tablesInstalled = Piwik::getTablesInstalled();
			$tablesToInstall = Piwik::getTablesNames();
			if(count($tablesInstalled) == 0) {
				Piwik::createTables();
				Piwik::createAnonymousUser();
				$updater = new Piwik_Updater();
				//set Piwikversion
				$updater->recordComponentSuccessfullyUpdated('core', Piwik_Version::VERSION);
			}
	}
}