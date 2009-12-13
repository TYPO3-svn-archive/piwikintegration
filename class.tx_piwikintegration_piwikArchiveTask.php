<?php
require_once(t3lib_extMgm::extPath('scheduler', 'class.tx_scheduler_task.php'));
class tx_piwikintegration_piwikArchiveTask extends tx_scheduler_Task {
	public function execute() { 
		//set execution time
		ini_set('max_execution_time',0);
		//find piwik
		$piwikScriptPath = dirname(dirname(__FILE__)).'/../piwik/piwik';
		define('PIWIK_INCLUDE_PATH', $piwikScriptPath);
		define('PIWIK_ENABLE_DISPATCH', false);
		define('PIWIK_ENABLE_ERROR_HANDLER', false);
		require_once PIWIK_INCLUDE_PATH . "/index.php";
		require_once PIWIK_INCLUDE_PATH . "/core/API/Request.php";
		Piwik_FrontController::getInstance()->init();
		
		$piwikConfig = parse_ini_file($piwikScriptPath.'/config/config.ini.php');

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
			&token_auth='.$TOKEN_AUTH.'"
			&format=php
			&serialize=0'
		);
		$piwikSiteIds = $request->process();
		//log
		$this->writeLog(
			'EXT:piwikintegration got '.count($piwikSiteIds).' siteid´s and piwik token, start archiving '
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
?>