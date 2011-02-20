<?php

abstract class Piwik_KSVisitorImport_Import_Abstract {
	
	public $rows = 0;
	
	/**
	 * GET parameters array of values to be used for the current visit
	 */
	protected $currentget = array();
	
	/**
	 * Unix timestamp to use for the generated visitor 
	 *
	 * @var int Unix timestamp
	 */
	protected $timestampToUse;
	
	/**
	* IdSite to generate visits for (@see setIdSite())
	*
	* @var int
	*/
	public $idSite = 1;
	
	/**
	* Path to read logfile from (@see setPath())
	*
	* @var string
	*/
	public $path = 1;
	
	/**
	 * clientIP
	 *
	 * @var string
	 */
	protected $clientIP = '';
	
	/**
	 * Set the idsite to generate the visits for
	 * 
	 * @param int idSite
	 */
	public function setIdSite($idSite)
	{
		$this->idSite = $idSite;
	}
	
	/**
	 * Set path to read logfile from
	 * 
	 * @param string path
	 */
	public function setPath($path)
	{
		$this->path = $path;
	}
	
	public function __construct()
	{
		$_COOKIE = $_GET = $_POST = array();
		
		// init GET and REQUEST to the empty array
		$this->setFakeRequest();
		
		// I am not sure weather we need this here
		Piwik::createConfigObject(PIWIK_USER_PATH . '/config/config.ini.php');
		Zend_Registry::get('config')->disableSavingConfigurationFileUpdates();
		
		$this->timestampToUse = time();
	}
	
	public function getRows()
	{
		return $this->rows;
	}
	
	public function import()
	{
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
	
	protected function makeEntry(array $entry)
	{
		$_SERVER['HTTP_USER_AGENT'] = $entry['userAgent'];
		$this->clientIP = ip2long($entry['remoteHost']);
		$this->timestampToUse = $entry['unixTimestamp'];
		$this->setCurrentRequest( 'idsite', $this->idSite);
		$this->setCurrentRequest('rec', 1);
		$this->saveVisit();
	}
	
	/**
	 * Saves the visit 
	 * - replaces GET and REQUEST by the fake generated request
	 * - load the Tracker class and call the method to launch the recording
	 * 
	 * This will save the visit in the database
	 */
	protected function saveVisit()
	{
		$this->setFakeRequest();
		$process = new Piwik_KSVisitorImport_Tracker();
		$process->setForceIp($this->clientIP);
		$process->setForceDateTime($this->timestampToUse);
		$process->main();
		unset($process);
	}	
	
	/**
	 * Sets the _GET and _REQUEST superglobal to the current generated array of values.
	 * @see setCurrentRequest()
	 * This method is called once the current action parameters array has been generated from 
	 * the global parameters array
	 */
	protected function setFakeRequest()
	{
		$_GET = $this->currentget;
	}
	
	/**
	 * Sets a value in the current action request array.
	 * 
	 * @param string Name of the parameter to set
	 * @param string Value of the parameter
	 */
	protected function setCurrentRequest($name,$value)
	{
		$this->currentget[$name] = $value;
	}
	
	public function emptyLogTables()
	{
		$db = Zend_Registry::get('db');
		$db->query('TRUNCATE TABLE '.Piwik_Common::prefixTable('log_action'));
		$db->query('TRUNCATE TABLE '.Piwik_Common::prefixTable('log_visit'));
		$db->query('TRUNCATE TABLE '.Piwik_Common::prefixTable('log_link_visit_action'));
	}
}
?>