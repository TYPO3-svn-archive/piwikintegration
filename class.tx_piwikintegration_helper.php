<?php
	class tx_piwikintegration_helper {
		var $piwik_id = array();
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
			//download piwik into typo3temp
			//can be hardcoded, because latest piwik is always on the same url ;) thanks guys
				$saveTo = t3lib_div::getFileAbsFileName('typo3temp/piwiklatest.zip');
				t3lib_div::writeFileToTypo3tempDir($saveTo,t3lib_div::getURL('http://piwik.org/latest.zip'));
			//make dir for extraction
				$installDir = t3lib_div::getFileAbsFileName('typo3conf/piwik/');
				t3lib_div::mkdir_deep(PATH_site,'typo3conf/piwik/');
			//extract archive
				$zip = new ZipArchive();
				$zip->open($saveTo);
				$zip->extractTo($installDir);
				$zip->close();
				unset($zip);
			//unlink archiv to save space in typo3temp ;)
				t3lib_div::unlink_tempfile($saveTo);
		}

		/**
		 * [Describe function...]
		 *
		 * @return	[type]		...
		 */
		function checkPiwikPatched() {

		}

		/**
		 * [Describe function...]
		 *
		 * @return	[type]		...
		 */
		function makePiwikPatched() {
			copy(t3lib_extMgm::extRelPath('piwikintegration').'piwik_patches/config/config.ini.php',PATH_site.'typo3conf/piwik/piwik/config/config.ini.php');
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
			//unload login
			if($key_login!==false) {
				unset($plugins[$key_login]);
			}
			//load typo3login
				if($key_t3login===false) {
					$plugins[]='TYPO3Login';
				}
				$piwikConfig->Plugins = $plugins;
			
			//create PiwikTables, check wether base tables already exist 
				Piwik::createDatabaseObject();
				$tablesInstalled = Piwik::getTablesInstalled();
				$tablesToInstall = Piwik::getTablesNames();
				if(count($tablesInstalled) == 0) {
					Piwik::createTables();
					Piwik::createAnonymousUser();
				}
				
			
			//set Piwikversion
				$updater = new Piwik_Updater();
				$updater->recordComponentSuccessfullyUpdated('core', Piwik_Version::VERSION);
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
							'idsite'=> $this->getPiwikSiteIdForPid,
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
					$tmpl->runThroughTemplates($rootLine,$template_uid);	// This generates the constants/config + hierarchy info for the template.
					$theConstants = $tmpl->generateConfig_constants();	// The editable constants are returned in an array.
					$tmpl->ext_categorizeEditableConstants($theConstants);	// The returned constants are sorted in categories, that goes into the $tmpl->categories array
					$tmpl->ext_regObjectPositions($tplRow["constants"]);		// This array will contain key=[expanded constantname], value=linenumber in template. (after edit_divider, if any)
				}
				if($tmpl->setup['constants']['usr_piwik_id']) {
					$id = intval($tmpl->setup['constants']['usr_piwik_id']);
				} elseif ($tmpl->setup['constants']['usr_name']) {
					$id =  intval($tmpl->setup['constants']['usr_name']);
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
	}
?>