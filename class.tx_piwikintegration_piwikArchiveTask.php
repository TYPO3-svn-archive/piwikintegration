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
require_once(t3lib_extMgm::extPath('scheduler', 'class.tx_scheduler_task.php'));
/**
 * scheduler task class
 * 
 * @author  Kay Strobach <typo3@kay-strobach.de>
 * @link http://kay-strobach.de
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 *
 */
class tx_piwikintegration_piwikArchiveTask extends tx_scheduler_Task {
	/**
	 * execute the piwik archive task
	 *
	 * @return	boolean  always returns true
	 */
	public function execute() {
		//set execution time
		ini_set('max_execution_time',0);
		//find piwik
		
		$piwikScriptPath = dirname(dirname(__FILE__)).'/../piwik/piwik';
		define('PIWIK_INCLUDE_PATH'         , $piwikScriptPath);
		define('PIWIK_ENABLE_DISPATCH'      , false);
		define('PIWIK_ENABLE_ERROR_HANDLER' , false);
		define('PIWIK_DISPLAY_ERRORS'       , false);
		ini_set('display_errors',0);
		include_once PIWIK_INCLUDE_PATH . "/index.php";
		include_once PIWIK_INCLUDE_PATH . "/core/API/Request.php";
		
		Piwik_FrontController::getInstance()->init();

		$piwikConfig = parse_ini_file($piwikScriptPath.'/config/config.ini.php',true);

		//log
		$this->writeLog(
			'EXT:piwikintegration cronjob'
		);
		//get API key
		$request = new Piwik_API_Request('
			module=API
			&method=UsersManager.getTokenAuth
			&userLogin='.$piwikConfig['superuser']['login'].'
			&md5Password='.$piwikConfig['superuser']['password'].'
			&format=php
			&serialize=0'
		);
		$TOKEN_AUTH = $request->process();

		//get all piwik siteid's
		$request = new Piwik_API_Request('
			module=API
			&method=SitesManager.getSitesWithAdminAccess
			&token_auth='.$TOKEN_AUTH.'
			&format=php
			&serialize=0'
		);
		$piwikSiteIds = $request->process();

		//log
		$this->writeLog(
			'EXT:piwikintegration got '.count($piwikSiteIds).' siteids and piwik token ('.$TOKEN_AUTH.'), start archiving '
		);
		//create Archive in piwik
		$periods = array(
			'day',
			'week',
			'month',
			#'year',
		);
		//iterate through sites
		//can be done with allSites, but this cannot create the logentries
		foreach($periods as $period) {
			foreach($piwikSiteIds as $siteId) {
				$starttime = microtime(true);
				$request = new Piwik_API_Request('
				            module=API
							&method=VisitsSummary.getVisits
							&idSite='.intval($siteId['idsite']).'
							&period='.$period.'
							&date=last52
							&format=xml
							&token_auth='.$TOKEN_AUTH.'"
				');
				$request->process();
				//log
				$this->writeLog(
					'EXT:piwikintegration period '.$period.' ('.$siteId['idsite'].') '.$siteId['name'].' ('.round(microtime(true)-$starttime,3).'s)'
				);
			}
		}
		//log
		$this->writeLog(
			'EXT:piwikintegration cronjob ended'
		);
		return true;
	}

	/**
	 * write something into the logfile
	 *
	 * @param	string		$message: message for the log
	 * @param	mixed		$data: mixed data to store in the log
	 * @return	void
	 */
	function writeLog($message,$data='') {
		$conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['piwikintegration']);
		if($conf['enableSchedulerLoging']) {
			$GLOBALS['BE_USER']->writeLog(
				4,	//extension
				0,	//no categorie
				0,	//message
				0,	//messagenumber
				$message,
				$data
			);
		}
	}
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/class.tx_piwikintegration_piwikArchiveTask.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/class.tx_piwikintegration_piwikArchiveTask.php']);
}

?>