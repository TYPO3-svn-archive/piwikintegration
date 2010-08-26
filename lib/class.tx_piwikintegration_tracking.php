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
 * lib/class.tx_piwikintegration_tracking.php
 *
 * tools to get tracking code
 *
 * $Id$
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */
 
 
class tx_piwikintegration_tracking {
	function init(&$params, &$reference) {
		//init helper object
		$this->piwikHelper = new tx_piwikintegration_helper();
		// process the page with these options
		$this->extConf = $params['pObj']->config['config']['tx_piwik.'];
		//read base url
		$this->baseUrl = $params['pObj']->config['config']['baseURL'];
	}
    /**
	 * handler for non cached output processing to insert piwik tracking code
	 * if in independent mode
	 *
	 * @param	pointer    $$params: passed params from the hook
	 * @param	pointer    $reference: to the parent object
	 * @return	void       void
	 */
    function contentPostProc_output(&$params, &$reference){
        $this->init($params,$reference);
        $content       = $params['pObj']->content;
		$beUserLogin   = $params['pObj']->beUserLogin;

		//check wether there is a BE User loggged in, if yes avoid to display the tracking code!
		if($beUserLogin == 1) {
			return;
		}

		//check wether needed parameters are set properly
		if (!($this->extConf['piwik_idsite']) || !($this->extConf['piwik_host'])) {
			return;
		}

		$piwikCode     = $this->piwikHelper->getPiwikJavaScriptCodeForSite($this->extConf['piwik_idsite']);
		$piwikCode     = str_replace('&gt;','>',$piwikCode);
		$piwikCode     = str_replace('&lt;','<',$piwikCode);
		$piwikCode     = str_replace('&quot;','"',$piwikCode);
		$piwikCode     = str_replace('<br />','',$piwikCode);

        $params['pObj']->content = str_replace('</body>','<!-- EXT:piwikintegration independent mode, disable independent mode, if you have 2 trackingcode snippets! -->'.$piwikCode.'<!-- /EXT:piwikintegration --></body>',$params['pObj']->content);
	}
    /**
	 * handler for cached output processing to assure that the siteid is created
	 * in piwik	 
	 *
	 * @param	pointer    $$params: passed params from the hook
	 * @param	pointer    $reference: to the parent object
	 * @return	void       void
	 */
     function contentPostProc_all(&$params, &$reference){
		$this->init($params,$reference);
		if($this->extConf['piwik_idsite']!=0) {
			$erg = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
				'*',
				'tx_piwikintegration_site',
				'idsite='.intval($this->extConf['piwik_idsite'])
			);
			$numRows = $GLOBALS['TYPO3_DB']->sql_num_rows($erg);
			//check wether siteid exists
			if($numRows==0) {
				//if not -> create
				//FIX currency for current Piwik version, since 0.6.3
				#$currency = Piwik_GetOption('SitesManager_DefaultCurrency') ? Piwik_GetOption('SitesManager_DefaultCurrency') : 'USD';
				//FIX timezone for current Piwik version, since 0.6.3
				#$timezone = Piwik_GetOption('SitesManager_DefaultTimezone') ? Piwik_GetOption('SitesManager_DefaultTimezone') : 'UTC';
				
				$GLOBALS['TYPO3_DB']->exec_INSERTquery(
					'tx_piwikintegration_site',
					array(
						'idsite'   => intval($this->extConf['piwik_idsite']),
						'name'     => 'ID '.intval($this->extConf['piwik_idsite']),
						'main_url' => $this->baseUrl,
						#'timezone'   => $timezone,
						#'currency'   => $currency,
						'ts_created' => date('Y-m-d H:i:s',time()),
					)
				);
			} elseif($numRows>1) {
				//more than once -> error
				die('piwik idsite table is inconsistent');
			}
		} else {
			die('Opps please set config.tx_piwik.idSite ... take a look in the manual please.');
		}
	}
	/**
	 * returns js trackingcode for a given idsite
	 *
	 * @param	integer		$siteId: idsite of piwik
	 * @return	string		trackingcode
	 */
	function getPiwikJavaScriptCodeForSite($siteId) {
		tx_piwikintegration_install::getInstaller()->getConfigObject()->initPiwikFrameWork();
		$content=Piwik::getJavascriptCode($siteId, $this->getPiwikBaseURL());
		return $content;
	}

	/**
	 * returns js trackingcode for a given pid
	 *
	 * @param	integer		$uid: uid of a page in TYPO3
	 * @return	string		trackingcode for a given uid
	 */
	function getPiwikJavaScriptCodeForPid($uid) {
		return $this->getPiwikJavaScriptCodeForSite($this->getPiwikSiteIdForPid($uid));
	}

	/**
	 * returns piwikBaseURL
	 *
	 * @return	string		path to piwik url
	 */
	function getPiwikBaseURL() {
		if(TYPO3_MODE == 'BE') {
			include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
			tx_piwikintegration_install::getInstaller()->getConfigObject()->initPiwikFrameWork();
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
	/**
	 * returns the piwik site id for a given page
	 * call it with $this->pageinfo['uid'] as param from a backend module
	 *
	 * @param	integer		$uid: Page ID
	 * @return	integer     piwik site id
	 */
	function getPiwikSiteIdForPid($uid) {
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$path              = tx_piwikintegration_install::getInstaller()->getConfigObject()->initPiwikDatabase();
		$this->tablePrefix = tx_piwikintegration_install::getInstaller()->getConfigObject()->getTablePrefix();

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
				$this->tablePrefix.'site',
				'idsite = '.intval($id),
				'',
				'',
				'0,1'
			);
			if(count($erg)==0) {
				//FIX currency for current Piwik version, since 0.6.3
				$currency = Piwik_GetOption('SitesManager_DefaultCurrency') ? Piwik_GetOption('SitesManager_DefaultCurrency') : 'USD';
				//FIX timezone for current Piwik version, since 0.6.3
				$timezone = Piwik_GetOption('SitesManager_DefaultTimezone') ? Piwik_GetOption('SitesManager_DefaultTimezone') : 'UTC';
				
				$GLOBALS['TYPO3_DB']->exec_INSERTquery(
					$this->tablePrefix.'site',
					array(
						'idsite'     => $id,
						'main_url'   => 'http://'.$_SERVER["SERVER_NAME"],
						'name'       => 'Customer '.$id,
						'timezone'   => $timezone,
						'currency'   => $currency,
						'ts_created' => date('Y-m-d H:i:s',time()),
					)
				);
			}
		$this->piwik_id[$uid] = $id;
		return $this->piwik_id[$uid];
	}
}
