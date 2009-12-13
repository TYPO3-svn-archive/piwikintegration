<?php
require_once(t3lib_extMgm::extPath('scheduler', 'class.tx_scheduler_task.php'));
class tx_piwikintegration_piwikArchiveTask extends tx_scheduler_Task {
	public function execute() { 
		$piwikScriptPath = dirname(dirname(__FILE__)).'/../piwik/piwik';
		define('PIWIK_INCLUDE_PATH', $piwikScriptPath);
		define('PIWIK_ENABLE_DISPATCH', false);
		define('PIWIK_ENABLE_ERROR_HANDLER', false);
		require_once PIWIK_INCLUDE_PATH . "/index.php";
		require_once PIWIK_INCLUDE_PATH . "/core/API/Request.php";
		Piwik_FrontController::getInstance()->init();
		
		$piwikConfig = parse_ini_file($piwikScriptPath.'/config/config.ini.php');
		
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
		//create Archive in piwik
		$periods = array('day','week','year');
		foreach($periods as $period) {
			$request = new Piwik_API_Request('
			            module=API
						&method=VisitsSummary.getVisits
						&idSite=all
						&period='.$period.'
						&date=last52
						&format=xml
						&token_auth='.$TOKEN_AUTH.'"
			');
			$request->process();
		}

		return true;
	}
}
?>