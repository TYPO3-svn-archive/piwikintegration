<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008     Frank N�gler (typo3@naegler.net),
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
 * Hooks for the 'piwik' extension.
 * 
 * This file is partly based on the piwik extension of Frank Nägler  
 *
 * @author    Frank Nägler <typo3@naegler.net>
 * @author    Kay Strobach <typo3@kay-strobach.de>
 */
class tx_piwikintegration	 {

    /**
     * main processing method
     */
    function contentPostProc_all(&$params, &$reference){
        // process the page with these options
        $content       = $params['pObj']->content;
		$this->extConf = $params['pObj']->config['config']['tx_piwikintegration.'];
		
        if(!$this->getPiwikIDSite()) return;
        
        $trackingCode = '
			<!-- Piwik -->
			<a href="http://piwik.org" title="Web analytics" onclick="window.open(this.href);return(false);">
			<script language="javascript" src="'.$this->getPiwikHost().'piwik.js" type="text/javascript"></script>
			<script type="text/javascript">
			<!--
				piwik_action_name = '.$this->getPiwikActionName().';
				piwik_idsite = '.$this->getPiwikIDSite().';
				piwik_url = \''.$this->getPiwikHost().'piwik.php\';
				'.$this->getPiwikDownloadExtensions().$this->getPiwikTrackerPause().$this->getPiwikInstallTracker().'
				piwik_log(piwik_action_name, piwik_idsite, piwik_url);
			//-->
			</script><object>
			<noscript><img src="'.$this->getPiwikHost().'piwik.php?idsite='.$this->getPiwikIDSite().'" style="border:0" alt="piwik" />
			</noscript></object></a>
			<!-- /Piwik --> 
        ';
        
        $params['pObj']->content = str_replace('</body>', $trackingCode.'</body>', $content);

    }
	function getPiwikIDSite() {
		$id=false;
		if($this->extConf['piwik_siteId']) {
			$id = $this->extConf['piwik_siteId'];
		} elseif($this->extConf['piwik_siteId2']) {
			$id = $this->extConf['piwik_siteId2'];
		} else {}
		if($id!==false) {
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
		}
		//create site in piwik db, if not existent
		return $id;
	}
	function getPiwikHost() {
		return t3lib_extMgm::siteRelPath('piwikintegration').'piwik/';
	}
	function getPiwikActionName() {
        if (strtoupper($this->extConf['_action_name']) == 'TYPO3') {
            return "'" . $GLOBALS['TSFE']->cObj->data['title'] . "'";
        }
        
        if (strlen($this->extConf['_action_name'])) {
            return $this->extConf['_action_name'];
        }
        return "''";
    }
	function getPiwikDownloadExtensions() {
        if (strlen($this->piwikOptions['_download_extensions'])) {
            return 'piwik_download_extensions = \''.$this->piwikOptions['_download_extensions'].'\';'."\n";
        }
        return '';
    }
    function getPiwikTrackerPause() {
        if (strlen($this->piwikOptions['_tracker_pause'])) {
            return 'piwik_tracker_pause = '.$this->piwikOptions['_tracker_pause'].';'."\n";
        }
        return '';
    }
    
    function getPiwikInstallTracker() {
        if (strlen($this->piwikOptions['_install_tracker'])) {
            return 'piwik_install_tracker = '.$this->piwikOptions['_install_tracker'].';'."\n";
        }
        return '';
    }
    static function correctPiwikConfiguration() {
		global $typo_db_host,
			$typo_db_username,
			$typo_db_password,
			$typo_db;
		//load files from piwik
		if(!defined('PIWIK_INCLUDE_PATH')) {
			define('PIWIK_INCLUDE_PATH', dirname(__FILE__).'/piwik/');
		}
		set_include_path(PIWIK_INCLUDE_PATH 
					. PATH_SEPARATOR . PIWIK_INCLUDE_PATH . '/libs/'
					. PATH_SEPARATOR . PIWIK_INCLUDE_PATH . '/plugins/'
					. PATH_SEPARATOR . PIWIK_INCLUDE_PATH . '/core/'
					. PATH_SEPARATOR . get_include_path());
		#error_reporting(E_ALL);
		#ini_set('display_errors', 1);
		require_once('core/Piwik.php');
		require_once('core/Config.php');
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
		#$piwikConfig->Plugins = new Zend_Config($plugins);
		$piwikConfig->Plugins = $plugins;
		
		#check tables and evt. create them
		#problem plugin tables ...
		$existingTables = $GLOBALS['TYPO3_DB']->admin_get_tables();
		$neededTables   = self::getCreateSQL();
		foreach($neededTables as $table=>$tableQuery) {
			if(!array_key_exists($table,$existingTables)) {
				$GLOBALS['TYPO3_DB']->admin_query($tableQuery);
			}
		}
	}
	/**
	 *
	 */	 	
	function getCreateSQL() {
		if(!defined('PIWIK_INCLUDE_PATH')) {
			define('PIWIK_INCLUDE_PATH', dirname(__FILE__).'/piwik/');
		}
		set_include_path(PIWIK_INCLUDE_PATH 
					. PATH_SEPARATOR . PIWIK_INCLUDE_PATH . '/libs/'
					. PATH_SEPARATOR . PIWIK_INCLUDE_PATH . '/plugins/'
					. PATH_SEPARATOR . PIWIK_INCLUDE_PATH . '/core/'
					. PATH_SEPARATOR . get_include_path());
		#error_reporting(E_ALL);
		#ini_set('display_errors', 1);
		require_once('core/Piwik.php');
		require_once('core/Config.php');
		Piwik::createConfigObject(PIWIK_INCLUDE_PATH.'config/config.ini.php');

		return Piwik::getTablesCreateSql();
	}
	/**
	 *
	 */	 	
	function refreshGeoIpData() {
		if(!defined('PIWIK_INCLUDE_PATH')) {
			define('PIWIK_INCLUDE_PATH', dirname(__FILE__).'/piwik/');
		}
		chdir(PIWIK_INCLUDE_PATH.'plugins/GeoIP/libs/');
		system('wget -N -q http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz');
		system("gunzip GeoLiteCity.dat.gz");  
	}
     /**
     * a stub for backwards compatibility with extending classes that might use it
     *
     * @return    bool    always false
     */
    function is_backend() {
        return false;
    }
}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/piwikintegration/class.tx_piwikintegration.php"])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/piwikintegration/class.tx_piwikintegration.php"]);
}

?>
