<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @version $Id: Controller.php 3192 2010-09-28 15:11:02Z vipsoft $
 *
 * @category Piwik_Plugins
 * @package Piwik_VisitorGenerator
 */

/**
 *
 * @package Piwik_VisitorGenerator
 */
class Piwik_KSVisitorImport_Controller extends Piwik_Controller {
	private $logfiletypes = array(
		array(
			'name'    => 'Apache Default Logfile format (without referrer and Useragent)',
			'handler' => 'Piwik_KSVisitorImport_Import_ApacheDefault'
		),
		array(
			'name'    => 'Apache Extended Logfile format, with referer and User Agent',
			'handler' => 'Piwik_KSVisitorImport_Import_ApacheExtended'
		),
		array(
			'name'    => 'Google CSV Logfile format',
			'handler' => 'Piwik_KSVisitorImport_Import_GoogleCsv'
		),

	);

	public function index($message='') {
		Piwik::checkUserIsSuperUser();

		$sitesList = Piwik_SitesManager_API::getInstance()->getSitesWithAdminAccess();

		$view = Piwik_View::factory('index');
		$this->setBasicVariablesView($view);
		$view->assign('sitesList'   , $sitesList);
		$view->assign('logfilesList', $this->logfiletypes);
		$view->assign('message',      $message);
		$view->nonce = Piwik_Nonce::getNonce('Piwik_KSVisitorImport.generate');

		$view->menu = Piwik_GetAdminMenu();
		echo $view->render();
	}

	public function generate($start=null,$stop=null) {
		// Only admin is allowed to do this!
		Piwik::checkUserIsSuperUser();
		$message = '';
		try {
			$this->checkTokenInUrl();
	
			$GET = $_GET;
			$POST = $_POST;
			$COOKIE = $_COOKIE;
			$REQUEST = $_REQUEST;
			
			$nonce = Piwik_Common::getRequestVar('form_nonce', '', 'string', $_POST);
			if(Piwik_Common::getRequestVar('choice', 'no') != 'yes' ||
					!Piwik_Nonce::verifyNonce('Piwik_KSVisitorImport.generate', $nonce))
			{
				return $this->index('KSVisitorImport_Error_choice');
				#Piwik::redirectToModule('KSVisitorImport', 'index');
			}
			
			//change logic here
			
			$path        = Piwik_Common::getRequestVar('path',        '', 'string');
			$logfiletype = Piwik_Common::getRequestVar('logfiletype', '', 'string');
	
			// get idSite from POST with fallback to GET
			$idSite = Piwik_Common::getRequestVar('idSite', false,   'int', $_GET);
			$idSite = Piwik_Common::getRequestVar('idSite', $idSite, 'int', $_POST);
	
			Piwik::setMaxExecutionTime(0);
			
			// There is a dns, why not? It takes some time for the lookups, but at least you get the provider info
			// If you want to unload the provider plugin (most probably for performance reasons), then uncomment the related lines
			
			#$loadedPlugins = Piwik_PluginsManager::getInstance()->getLoadedPlugins();
			#$loadedPlugins = array_keys($loadedPlugins);
			// we have to unload the Provider plugin otherwise it tries to lookup the IP for a hostname, and there is no dns server here
			#if(Piwik_PluginsManager::getInstance()->isPluginActivated('Provider')) {
			#	Piwik_PluginsManager::getInstance()->unloadPlugin('Provider');
			#}
	
			// we set the DO NOT load plugins so that the Tracker generator doesn't load the plugins we've just disabled.
			// if for some reasons you want to load the plugins, comment this line, and disable the plugin Provider in the plugins interface
			#Piwik_PluginsManager::getInstance()->doNotLoadPlugins();
			#Piwik_PluginsManager::getInstance()->doNotLoadAlwaysActivatedPlugins();
			
			$timer        = new Piwik_Timer;
			if(!array_key_exists($logfiletype, $this->logfiletypes)) {
				throw new Exception ('KSVisitorImport_Error_logfiletype');
			}
			$importerCls  = $this->logfiletypes[$logfiletype]['handler']; 
			$importerName = $this->logfiletypes[$logfiletype]['name'];
			
			//######################################################################
			$importer     = new $importerCls($idSite,$path);
			if(!Piwik_Common::getRequestVar('keepLogs', false,   'int', $_POST)) {
				$importer->emptyLogTables();
			}
			if(Piwik_Common::getRequestVar('debug', false,   'int', $_POST)) {
				$GLOBALS['PIWIK_TRACKER_DEBUG'] = true;
			}
			
			$importer->setIdSite( $idSite );
			$importer->setPath( $path );
			
			$importer->import();
			//######################################################################
		} catch(Exception $e) {
			return $this->index('KSVisitorImport_Error_file');
		}
		// Recover all super globals
		$_GET = $GET;
		$_POST = $POST;
		$_COOKIE = $COOKIE;
		$_REQUEST = $REQUEST;
		
		// Reload plugins
		#Piwik_PluginsManager::getInstance()->loadPlugins($loadedPlugins);

		#Piwik_Common::runScheduledTasks(time());
		// Init view
		$view = Piwik_View::factory('generate');
		$view->menu = Piwik_GetAdminMenu();
		$this->setBasicVariablesView($view);
		$view->assign('path',        $path);
		$view->assign('rows',        $importer->getRows());
		$view->assign('logfiletype', $importerCls);
		$view->assign('logfilename', $importerName);
		$view->assign('timer',       $timer);
		echo $view->render();
	}
}
