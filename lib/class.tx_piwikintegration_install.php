<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010     Kay Strobach (typo3@kay-strobach.de),
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

class tx_piwikintegration_install {
	/**
	 * cache for checking if piwik is installed, as many functions require
	 * a valid installation, otherwise problems will occur.	 
	 */	 	
	protected $installed   = null;
	/**
	 * path were piwik will be installed
	 */	 	
	protected $installPath = 'typo3conf/piwik/';
	
	private static $installer   = null;
	/**
	 * get Singleton function
	 */	 	
	static function getInstaller() {
		if(self::$installer == null) {
			self::$installer = new tx_piwikintegration_install();
		}
		return self::$installer;
	}
	/**
	 * private constructor to get a singleton
	 */	 	
	private function __construct() {
		try {
			$this->checkInstallation();
		} catch(Exception $e) {
			echo 'There was a Problem: '.$e->getMessage();
		}
	}
	/**
	 *
	 */
	public function checkInstallation() {
		if(file_exists(t3lib_div::getFileAbsFileName($this->installPath))) {
			return true;
		} else {
			return false;
		}
	}
	public function installPiwik() {
		try {
			$zipArchivePath=$this->downloadLatestPiwik();
			$this->extractDownloadedPiwik($zipArchivePath);
			$this->patchPiwik();
			$this->configureDownloadedPiwik();
		} catch(Exception $e) {
			echo 'There was a Problem: '.$e->getMessage();
		}
	}
	public function getAbsInstallPath() {
		return t3lib_div::getFileAbsFileName($this->installPath);
	}
	public function getRelInstallPath() {
		return $this->installPath;
	}
	private function downloadLatestPiwik() {
		//download piwik into typo3temp
		//can be hardcoded, because latest piwik is always on the same url ;) thanks guys
		$zipArchivePath = t3lib_div::getFileAbsFileName('typo3temp/piwiklatest.zip');
		t3lib_div::writeFileToTypo3tempDir($zipArchivePath,t3lib_div::getURL('http://piwik.org/latest.zip'));
		if(@filesize($zipArchivePath)===FALSE) {
			throw new Exception('Installation invalid, typo3temp '.$zipArchivePath.' can´t be created for some reason');
		}
		if(@filesize($zipArchivePath)<10) {
			throw new Exception('Installation invalid, typo3temp'.$zipArchivePath.' is smaller than 10 bytes, download definitly failed');
		}
		return $zipArchivePath;
	}
	private function extractDownloadedPiwik($zipArchivePath) {
		//make dir for extraction
			t3lib_div::mkdir_deep(PATH_site,$this->installPath);
			if(!is_writeable(PATH_site.$this->installPath)) {
				throw new Exception($this->installPath.' must be writeable');
			}
		//extract archive
			if(class_exists('ZipArchive')) {
				$zip = new ZipArchive();
				$zip->open($zipArchivePath);
				$zip->extractTo($this->getAbsInstallPath());
				$zip->close();
				unset($zip);
			} elseif(!(TYPO3_OS=='WIN' || $GLOBALS['TYPO3_CONF_VARS']['BE']['disable_exec_function']))	{
				$cmd = $GLOBALS['TYPO3_CONF_VARS']['BE']['unzip_path'].'unzip -qq "'.$zipArchivePath.'" -d "'.$installDir.'"';
				exec($cmd);
			} else {
				throw new Exception('There is no valid unzip wrapper, i need either the class ZipArchiv from php or a *nix system with unzip path set.');
			}
		//unlink archiv to save space in typo3temp ;)
			t3lib_div::unlink_tempfile($zipArchivePath);

	}
	public function checkPiwikPatched() {
		$_EXTKEY = 'piwikintegration';
		@include(t3lib_extMgm::extPath('piwikintegration').'ext_emconf.php');
		@include($this->getAbsInstallPath().'/piwik/piwikintegration.php');
		if($EM_CONF['piwikintegration']['version'] != $piwikPatchVersion) {
			return false;
		}
		return true;
	}
	public function patchPiwik($force) {
		if(!is_writeable($this->getAbsInstallPath())) {
			throw new Exception('Installation is invalid, '.$this->getAbsInstallPath().' was not writeable for applying the patches');
		}
		//recursive directory copy is not supported under windows ... so i implement is myself!!!
		$source = t3lib_extMgm::extPath('piwikintegration').'piwik_patches/';
		$dest   = $this->getAbsInstallPath().'piwik/';
		$cmd    = array();
		$t = t3lib_div::getAllFilesAndFoldersInPath(
			array(),
			$source,
			'',
			true,
			99
		);
		foreach($t as $entry) {
			$shortEntry = str_replace($source,'',$entry);
			if($shortEntry!='' && $shortEntry!='.') {
				if(!in_array($shortEntry, $exclude)) {
					if(is_dir($entry)) {
						$cmd['newfolder'][] = array(
							'data'   => basename($shortEntry),
							'target' => dirname($dest.$shortEntry),
						);
						@mkdir($dest.$shortEntry);
					} elseif(is_file($entry)) {
						$cmd['copy'][] = array(
							'data'   => $entry,
							'target' => $dest.$shortEntry,
						);
						@copy($entry,$dest.$shortEntry);
					}
				}
			}
		}
		//store information about the last patch process
		$_EXTKEY = 'piwikintegration';
		@include(t3lib_extMgm::extPath('piwikintegration').'ext_emconf.php');
		$data = '<?php $piwikPatchVersion = "'.$EM_CONF['piwikintegration']['version'].'"; '.chr(63).'>';
		file_put_contents($this->getAbsInstallPath().'piwik/piwikintegration.php',$data);
	}
	private function configureDownloadedPiwik() {
		$this->getConfigObject()->makePiwikConfigured();
	}
	public function getConfigObject() {
		if($this->checkInstallation()) {
			include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_config.php'));
			return tx_piwikintegration_config::getConfigObject();
		} else {
			throw new Exception('Piwik is not installed!');
		}
	}
	public function removePiwik() {
		return t3lib_div::rmdir($this->getAbsInstallPath(),true);
	}
}