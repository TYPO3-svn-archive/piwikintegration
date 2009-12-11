<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 	Kay Strobach (typo3@kay-strobach.de),
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
/**
 * @author  Kay Strobach <typo3@kay-strobach.de>
 * @link http://kay-strobach.de
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * 
 */
 
	#ini_set('display_errors',1);
	class tx_piwikintegration_helper {
		var $piwik_id = array();
		function initPiwik() {
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
			require_once PIWIK_INCLUDE_PATH .'/core/Loader.php';
			require_once('core/Piwik.php');
			require_once('core/Config.php');
			require_once('core/PluginsManager.php');
		}
		function checkPiwikInstalled() {
			if(file_exists(t3lib_div::getFileAbsFileName('typo3conf/piwik/piwik/config/config.ini.php'))) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Manages the Piwik installation
		 *
		 * @return	void
		 */
		function makePiwikInstalled() {
			$this->makePiwikDownloadAndExtract();
			$this->makePiwikPatched();
			$this->makePiwikConfigured();
		}
		
		/**
		 * Manages the Piwik installation
		 *
		 * @return	void
		 */
		function makePiwikDownloadAndExtract() {
			if(!is_writeable(PATH_site.'typo3conf/')) {
				die('Installation is invalid, typo3conf for creating the piwik app folder');
			}

			//download piwik into typo3temp
			//can be hardcoded, because latest piwik is always on the same url ;) thanks guys
				$saveTo = t3lib_div::getFileAbsFileName('typo3temp/piwiklatest.zip');
				t3lib_div::writeFileToTypo3tempDir($saveTo,t3lib_div::getURL('http://piwik.org/latest.zip'));
			//make dir for extraction
				$installDir = t3lib_div::getFileAbsFileName('typo3conf/piwik/');
				t3lib_div::mkdir_deep(PATH_site,'typo3conf/piwik/');
			//extract archive
				if(class_exists('ZipArchive')) {
					$zip = new ZipArchive();
					$zip->open($saveTo);
					$zip->extractTo($installDir);
					$zip->close();
					unset($zip);
				} elseif(!(TYPO3_OS=='WIN' || $GLOBALS['TYPO3_CONF_VARS']['BE']['disable_exec_function']))	{
					$cmd = $GLOBALS['TYPO3_CONF_VARS']['BE']['unzip_path'].'unzip -qq "'.$saveTo.'" -d "'.$installDir.'"';
					exec($cmd);
				} else {
					die('There is no valid unzip wrapper, i need either the class ZipArchiv from php or a *nix system with unset path set.');
				}
			//unlink archiv to save space in typo3temp ;)
				t3lib_div::unlink_tempfile($saveTo);
		}

		/**
		 * [Describe function...]
		 *
		 * @return	[type]		...
		 */
		function checkPiwikPatched() {
			$_EXTKEY = 'piwikintegration';
			@include(t3lib_extMgm::extPath('piwikintegration').'ext_emconf.php');
			@include(PATH_site.'typo3conf/piwik/piwik/piwikintegration.php');
			if($EM_CONF['piwikintegration']['version'] != $piwikPatchVersion) {
				return false;
			}
			return true;
		}

		/**
		 * [Describe function...]
		 *
		 * @return	[type]		...
		 */
		function makePiwikPatched($exclude=array()) {
			if(!is_writeable(PATH_site.'typo3conf/piwik/piwik/')) {
				die('Installation is invalid, typo3conf/piwik/piwik was not writeable for applying the patches');
			}
			//recursive directory copy is not supported under windows ... so i implement is myself!!!
			$source = t3lib_extMgm::extPath('piwikintegration').'piwik_patches/';
			$dest   = PATH_site.'typo3conf/piwik/piwik/';
			$cmd    = array();
			$t = t3lib_div::getAllFilesAndFoldersInPath(
				array(),
				$source,
				'',
				true,
				99
			);
			foreach($t as $entry) {
				$shortEntry = str_replace($source,'',$entry);
				if($shortEntry!='' && $shortEntry!='.') {
					if(!in_array($shortEntry, $exclude)) {
						if(is_dir($entry)) {		
							$cmd['newfolder'][] = array(
								'data'   => basename($shortEntry),
								'target' => dirname($dest.$shortEntry),
							);
							@mkdir($dest.$shortEntry);
						} elseif(is_file($entry)) {
							$cmd['copy'][] = array(
								'data'   => $entry,
								'target' => $dest.$shortEntry,
							);
							@copy($entry,$dest.$shortEntry);
						}
					}
				}
			}
			//store information about the last patch process
			$_EXTKEY = 'piwikintegration';
			@include(t3lib_extMgm::extPath('piwikintegration').'ext_emconf.php');
			$data = '<?php $piwikPatchVersion = "'.$EM_CONF['piwikintegration']['version'].'"; '.chr(63).'>';
			file_put_contents(PATH_site.'typo3conf/piwik/piwik/piwikintegration.php',$data);
		}

		/**
		 * [Describe function...]
		 *
		 * @return	[type]		...
		 */
		function makePiwikConfigured() {
			global $typo_db_host,
			       $typo_db_username,
			       $typo_db_password,
			       $typo_db;
			$this->initPiwik();
			//makeConfigObject
			Piwik::createConfigObject(PIWIK_INCLUDE_PATH.'config/config.ini.php');
			$piwikConfig = Zend_Registry::get('config'); 
			
			//userdata
			$superuser = $piwikConfig->superuser->toArray();
			$superuser['login']    = md5(microtime());
			$superuser['password'] = md5(microtime());
			#$piwikConfig->superuser = new Zend_Config($superuser);
			$piwikConfig->superuser = $superuser;
			
			//Database
			$database = $piwikConfig->database->toArray();
			$database['host']          = TYPO3_db_host;
			$database['username']      = TYPO3_db_username;
			$database['password']      = TYPO3_db_password;
			$database['dbname']        = TYPO3_db;
			$database['tables_prefix'] = "tx_piwikintegration_";
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
					$updater->recordComponentSuccessfullyUpdated('core', Piwik_Version::VERSION);
				}
				
			
			//set Piwikversion
		}
		/**
		 * This function makes a page statistics accessable for a user
		 * 	call it with $this->pageinfo['uid'] as param from a backend module
		 *
		 * @param	[type]		$uid: ...
		 * @return	[type]		...
		 */
		function correctUserRightsForPid($uid) {
			if($uid <= 0 || $uid!=intval($uid)) {
				throw new Exception('Problem with uid in tx_piwikintegration_helper.php::correctUserRightsForPid');
			}
			/**
			 * ensure, that user is added to database
			 */
			if($GLOBALS['BE_USER']->user['admin']!=1) {
				$beUserName = $GLOBALS['BE_USER']->user['username'];
				$erg = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
						'*',
						'tx_piwikintegration_access',
						'login="'.$beUserName.'" AND idsite='.$this->getPiwikSiteIdForPid($uid),
						'',
						'',
						'0,1'
				);
				if(count($erg)==0) {
					$GLOBALS['TYPO3_DB']->exec_INSERTquery(
						'tx_piwikintegration_access',
						array(
							'login' => $beUserName,
							'idsite'=> $this->getPiwikSiteIdForPid($uid),
							'access'=> 'view'
						)
					);
				}
			}
		}
		/**
		 * returns the piwik site id for a given page
		 * 	call it with $this->pageinfo['uid'] as param from a backend module
		 *
		 * @param	integer		$uid: Page ID
		 * @return	integer
		 */
		function getPiwikSiteIdForPid($uid) {
			if($uid <= 0 || $uid!=intval($uid)) {
				throw new Exception('Problem with uid in tx_piwikintegration_helper.php::getPiwikSiteIdForPid');
			}

			if(isset($this->piwik_id[$uid])) {
				return $this->piwik_id[$uid];
			}
			//parse ts template
				$template_uid = 0;
				$pageId = $uid;
				$tmpl = t3lib_div::makeInstance("t3lib_tsparser_ext");	// Defined global here!
				$tmpl->tt_track = 0;	// Do not log time-performance information
				$tmpl->init();
	
				$tplRow = $tmpl->ext_getFirstTemplate($pageId,$template_uid);
				if (is_array($tplRow) || 1)	{	// IF there was a template...
						// Gets the rootLine
					$sys_page = t3lib_div::makeInstance("t3lib_pageSelect");
					$rootLine = $sys_page->getRootLine($pageId);
					$tmpl->runThroughTemplates($rootLine);	// This generates the constants/config + hierarchy info for the template.
					$tmpl->generateConfig();
				}
				if($tmpl->setup['config.']['tx_piwik.']['piwik_idsite']) {
					$id = intval($tmpl->setup['config.']['tx_piwik.']['piwik_idsite']);
				} else {
					$id = 0;
				}
			//check wether site already exists in piwik db
				$erg = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					'*',
					'tx_piwikintegration_site',
					'idsite="'.intval($id),
					'',
					'',
					'0,1'
				);
				if(count($erg)==0) {
					$GLOBALS['TYPO3_DB']->exec_INSERTquery(
						'tx_piwikintegration_site',
						array(
							'idsite'=> $id,
							'main_url'=> 'http://'.$_SERVER["SERVER_NAME"],
							'name'    => 'Customer '.$id,
						)
					);
				}
			$this->piwik_id[$uid] = $id;
			return $this->piwik_id[$uid];
		}
		function getPiwikJavaScriptCodeForSite($siteId) {
			$this->initPiwik();
			$content=Piwik::getJavascriptCode($siteId, $this->getPiwikBaseURL());
			return $content;
		}
		function getPiwikJavaScriptCodeForPid($uid) {
			return $this->getPiwikJavaScriptCodeForSite($this->getPiwikSiteIdForPid($uid));
		}
		function getPiwikBaseURL() {
			if(TYPO3_MODE == 'BE') {
				$this->initPiwik();
				$path = Piwik_Url::getCurrentUrlWithoutFileName();
				$path = dirname($path);
				$path.='/typo3conf/piwik/piwik/';
			} else {
				$path = 'http://'.$_SERVER["SERVER_NAME"].dirname($_SERVER['SCRIPT_NAME']).'/typo3conf/piwik/piwik/';
			}
			//need to be retrieved different for fe, so that it works ...
			#$path = 'http://localhost/t3alpha4.3/typo3conf/piwik/piwik/';
			return $path;
		}
		function getPiwikWidgetsForPid($uid) {
			return $this->getPiwikWidgets($this->getPiwikSiteIdForPid($uid));
		}
		function getPiwikWidgets() {
			$this->initPiwik();
			$controller = Piwik_FrontController::getInstance();
			$controller->init();
			$widgets = Piwik_GetWidgetsList();
			return $widgets;
		}
		static function getWidgetsForFlexForm(&$PA,&$fobj) {
			$PA['items'] = array();
			$piwikhelper = new tx_piwikintegration_helper();
			$widgets=$piwikhelper->getPiwikWidgets();
			
			foreach($widgets as $pluginCat => $plugin) {
				foreach($plugin as $widget) {
					$PA['items'][] = array(
						$pluginCat.' : '.$widget['name'],
						base64_encode(json_encode($widget['parameters'])),
						'i/catalog.gif'
					);
				}
			}
		}
		static function getSitesForFlexForm(&$PA,&$fobj) {
			//fetch anonymous accessable idsites
			$erg = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'idsite',
				'tx_piwikintegration_access',
				'login="anonymous"'
			);
			
			//build array for selecting more information
			$sites = array();
			foreach($erg as $site) {
				$sites[] = $site['idsite'];
			}
			$accessableSites = implode(',',$sites);
			$erg = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'idsite,name,main_url',
				'tx_piwikintegration_site',
				'idsite IN('.$accessableSites.')',
				'',
				'name, main_url, idsite'
			);
			$PA['items'] = array();
			
			//render items
			while(($site = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($erg)) !== false) {
				$PA['items'][] = array(
					$site['name'] ? $site['name'].' : '.$site['main_url'] : $site['main_url'],
					$site['idsite'],
					'i/domain.gif',
				);
			}
		}
	}
?>