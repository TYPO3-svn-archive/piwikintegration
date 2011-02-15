<?php
#require_once PIWIK_INCLUDE_PATH . '/libs/PiwikTracker/PiwikTracker.php';

abstract class Piwik_KSVisitorImport_Import_Abstract {
	var $rows = 0;
	function __construct($idSite,$path) {
		$this->idSite = $idSite;
		$this->path   = $path;
		$this->init();
	}
	function init() {
		
	}
	function getRows() {
		return $this->rows;
	}
	function import() {
		$this->emptyLogTables();
		if(!file_exists($this->path) || !is_file($this->path)) {
			throw new Exception('File doesn´t exist.');
		}
		$this->fileHandle = fopen($this->path,'r');
		while (!feof($this->fileHandle)) {
			if($this->lineHandler(fgets($this->fileHandle))) {
				$this->rows++;
			}			
		}
		fclose ($this->fileHandle);	
	}
	function makeEntry(array $entry) {
		$entry['rec'] = true;
		$_GET = $entry;
		//set timestamp
		Piwik_VisitorGenerator_Visit::setTimestampToUse($entry['unixTimestamp']);
		$_SERVER['HTTP_USER_AGENT']      = $entry['userAgent'];
		$_SERVER['HTTP_CLIENT_IP']       = $entry['remoteHost'];
		#$_SERVER['HTTP_ACCEPT_LANGUAGE'] =
		$process = new Piwik_VisitorGenerator_Tracker();
		$process->main();
		unset($process);
		
		##HTTP Tracking :)
		/*$t = new PiwikTracker( $entry['idsite']);
		$t->setUrl($entry['url']);
		$t->setForceVisitDateTime('');
		$t->setIp($entry['remoteHost']);
		#$t->setLocalTime();
		$t->setResolution( 1024, 768 );
		$t->setBrowserHasCookies(true);
		$t->doTrackPageView($entry['url']);
		#$t->setPlugins($flash = true, $java = true, $director = false);
		unset($t);*/
	}
	function emptyLogTables() {
		$db = Zend_Registry::get('db');
		$db->query('TRUNCATE TABLE '.Piwik_Common::prefixTable('log_action'));
		$db->query('TRUNCATE TABLE '.Piwik_Common::prefixTable('log_visit'));
		$db->query('TRUNCATE TABLE '.Piwik_Common::prefixTable('log_link_visit_action'));
	}
}
?>