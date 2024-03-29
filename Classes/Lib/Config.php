<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Kay Strobach (typo3@kay-strobach.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * interact with Piwik core after download and unzip
 *
 * $Id: class.tx_piwikintegration_config.php 56783 2012-01-26 17:19:37Z kaystrobach $
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */


class tx_piwikintegration_config {
	private static $configObject = null;
	private        $installer    = null;
	private        $initPiwikFW  = false;
	private        $initPiwikDB  = false;
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
		if($this->initPiwikFW) {
			$this->initPiwikFW = true;
			return;
		}
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
			include_once PIWIK_INCLUDE_PATH . 'libs/upgradephp/upgrade.php';
			include_once PIWIK_INCLUDE_PATH . 'core/Loader.php';
			include_once('core/Piwik.php');
			include_once('core/Config.php');
			include_once('core/PluginsManager.php');
		//create config object
			try {
				Piwik::createConfigObject();
				$config = Piwik_Config::getInstance();
				$config->init();
			} catch(Exception $e) {
			}
	}
	function initPiwikDatabase($noLoadConfig = false) {
		$this->initPiwikFrameWork();
		if($this->initPiwikDB) {
			$this->initPiwikDB = true;
			return;
		}
		#include_once(PIWIK_INCLUDE_PATH . '/core/Option.php');
		#if($noLoadConfig===true) {
		#	Piwik::createConfigObject(PIWIK_INCLUDE_PATH.'config/config.ini.php');
		#}
		Piwik::createDatabaseObject();
	}
	function makePiwikConfigured() {
		$this->initPiwikFrameWork();

		#Piwik::setUserIsSuperUser(TRUE);

		//userdata
		$this->setOption('superuser','login'        ,md5(microtime()));
		$this->setOption('superuser','password'     ,md5(microtime()));
		$this->setOption('superuser','email'        ,$GLOBALS["BE_USER"]->user['email']);

		//Database
		$this->setOption('database' ,'host'         ,TYPO3_db_host);
		$this->setOption('database' ,'username'     ,TYPO3_db_username);
		$this->setOption('database' ,'password'     ,TYPO3_db_password);
		$this->setOption('database' ,'dbname'       ,TYPO3_db);
		$this->setOption('database' ,'tables_prefix','user_piwikintegration_');
		$this->setOption('database' ,'adapter'      ,"PDO_MYSQL");

		//General
		$this->setOption('General'  ,'show_website_selector_in_user_interface',0);
		$this->setOption('General'  ,'serve_widget_and_data'                  ,0);

		//Disable the frame detection of Piwik
		$this->setOption('General'  ,'enable_framed_pages'                    ,1);
		$this->setOption('General'  ,'enable_framed_logins'                   ,1);
		$this->setOption('General'  ,'enable_framed_settings'                 ,1);

		//init all plugins


		//set Plugins
		$this->disablePlugin('ExampleAPI');
		$this->disablePlugin('ExampleFeedburner');
		$this->disablePlugin('ExamplePlugin');
		$this->disablePlugin('ExampleRssWidget');
		$this->disablePlugin('ExampleUI');
		$this->disablePlugin('Login');
		$this->enableSuggestedPlugins();

		//create PiwikTables, check wether base tables already exist
		$this->installDatabase();
	}

	function enableSuggestedPlugins() {
		$this->enablePlugin('TYPO3Login');
		$this->enablePlugin('TYPO3Menu');
		$this->enablePlugin('TYPO3Widgets');
		$this->enablePlugin('SecurityInfo');
		$this->enablePlugin('DBStats');
		$this->enablePlugin('AnonymizeIP');

	}

	function installDatabase() {
		$this->initPiwikDatabase(true);
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
	/**
	 * This function makes a page statistics accessable for a user
	 * call it with $this->pageinfo['uid'] as param from a backend module
	 *
	 * @param	integer		$uid: pid for which the user will get access
	 * @return	void
	 */
	function correctUserRightsForPid($uid) {
		$this->initPiwikFrameWork();
		if($uid <= 0 || $uid!=intval($uid)) {
			throw new Exception('Problem with uid in tx_piwikintegration_helper.php::correctUserRightsForPid');
		}
		$beUserName = $GLOBALS['BE_USER']->user['username'];
		/**
		 * ensure, that user's right are added to the database
		 * tx_piwikintegration_access		 
		 */
		if($GLOBALS['BE_USER']->user['admin']!=1) {
			$erg = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					'*',
					tx_piwikintegration_div::getTblName('access'),
					'login="'.$beUserName.'" AND idsite='.$this->getPiwikSiteIdForPid($uid),
					'',
					'',
					'0,1'
			);
			if(count($erg)==0) {
				$GLOBALS['TYPO3_DB']->exec_INSERTquery(
					tx_piwikintegration_div::getTblName('access'),
					array(
						'login' => $beUserName,
						'idsite'=> $this->getPiwikSiteIdForPid($uid),
						'access'=> 'view',
					)
				);
			}
		}
	}
	function getTablePrefix() {
		#throw new Exception('config. getTablePrefix is deprecated');
		return $this->tablePrefix = $this->getOption('database','tables_prefix');
	}
    function getDBName() {
		return $this->dbName = $this->getOption('database','dbname');
	}
    function getT3DBName() {
		return $this->T3DBName = $this->getOption('database','t3dbname');
	}
	function setOption($sectionName,$option,$value) {
		$this->initPiwikFrameWork();
		$piwikConfig = Piwik_Config::getInstance();
		$section     = $piwikConfig->$sectionName;
		$section[$option] = $value;
		$piwikConfig->$sectionName = $section;
		$piwikConfig->forceSave();
	}
	function getOption($sectionName,$option) {
		$this->initPiwikFrameWork();
		$piwikConfig = Piwik_Config::getInstance();
		$section     = $piwikConfig->$sectionName;
		return $section[$option];
	}
	function enablePlugin($plugin) {
		$this->initPiwikFrameWork();
		if(!Piwik_PluginsManager::getInstance()->isPluginActivated($plugin)) {
			try {
				Piwik_PluginsManager::getInstance()->activatePlugin($plugin);
				Piwik_PluginsManager::getInstance()->loadPlugins( Piwik_Config::getInstance()->Plugins['Plugins'] );
				#Piwik_PluginsManager::getInstance()->installLoadedPlugins();
				Piwik::install();
			} catch(Exception $e) {

			}
		}
		return;
	}
	function disablePlugin($plugin) {
		$this->initPiwikFrameWork();
		if(Piwik_PluginsManager::getInstance()->isPluginActivated($plugin)) {
			try {
				Piwik_PluginsManager::getInstance()->deactivatePlugin($plugin);
				#Piwik_PluginsManager::getInstance()->installLoadedPlugins();
				Piwik::install();
			} catch(Exception $e) {

			}
		}
		return;
	}
	function getJsForUid($uid) {
		return '--';
	}
}
