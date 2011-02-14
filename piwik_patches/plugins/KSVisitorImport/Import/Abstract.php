<?php
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
		#$process->infoArray = $entry;
		$process->main();
		unset($process);
	}
	function emptyLogTables() {
		$db = Zend_Registry::get('db');
		$db->query('TRUNCATE TABLE '.Piwik_Common::prefixTable('log_action'));
		$db->query('TRUNCATE TABLE '.Piwik_Common::prefixTable('log_visit'));
		$db->query('TRUNCATE TABLE '.Piwik_Common::prefixTable('log_link_visit_action'));
	}
}
?>