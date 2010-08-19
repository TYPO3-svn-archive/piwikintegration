<?php
	class tx_piwikintegration_helper {
		var $piwik_id = array();
		function checkPiwikInstalled() {
			if(file_exists(t3lib_div::getFileAbsFileName('typo3conf/piwik/'))) {
				return true;
			} else {
				return false;
			}
		}
		function makePiwikInstalled() {
			//download piwik into typo3temp
			//can be hardcoded, because latest piwik is always on the same url ;) thanks guys
				$saveTo = t3lib_div::getFileAbsFileName('typo3temp/piwiklatest.zip');
				t3lib_div::writeFileToTypo3tempDir($saveTo,t3lib_div::getURL('http://piwik.org/latest.zip'));
			//make dir for extraction
				$installDir = t3lib_div::getFileAbsFileName('typo3conf/piwik/');
				t3lib_div::mkdir_deep(Path_site,'typo3conf/piwik/');
			//extract archive
				$zip = new ZipArchive();
				$zip->open($saveTo);
				$zip->extractTo($installDir);
				$zip->close();
				unset($zip);
			//unlink archiv to save space in typo3temp ;)
				t3lib_div::unlink_tempfile($saveTo);
			//copy patch files in piwikdir
				t3lib_div::upload_copy_move(t3lib_extMgm::extPath('piwikintegration').'piwik_patches',$installDir.'/piwik');
		}
		function checkPiwikPatched() {
		
		}
		function makePiwikPatched() {
		
		}
		function makePiwikConfigured() {
		
		}
		/**
		 * This function makes a page statistics accessable for a user
		 *	call it with $this->pageinfo['uid'] as param from a backend module		 
		 */		 		
		function correctUserRightsForPid($uid) {
			if($uid <= 0 || $uid!=intval($uid)) {
				throw new Exception('Problem with uid in tx_piwikintegration_helper.php::correctUserRightsForPid');
			}
			/**
			 * ensure, that user is added to database
			 */							
			if($GLOBALS['BE_USER']->user['admin']!=1) {
				$beUserName = $GLOBALS['BE_USER']->user['username'];
				$erg = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
						'*',
						'tx_piwikintegration_access',
						'login="'.$beUserName.'" AND idsite='.$this->getPiwikSiteIdForPid($uid),
						'',
						'',
						'0,1'
				);
				if(count($erg)==0) {
					$GLOBALS['TYPO3_DB']->exec_INSERTquery(
						'tx_piwikintegration_access',
						array(
							'login' => $beUserName,
							'idsite'=> $this->getPiwikSiteIdForPid,
							'access'=> 'view'
						)
					);
				}
			}
		}
		/**
		 *	returns the piwik site id for a given page
		 *	call it with $this->pageinfo['uid'] as param from a backend module		 
		 */		 		
		function getPiwikSiteIdForPid($uid) {
			if($uid <= 0 || $uid!=intval($uid)) {
				throw new Exception('Problem with uid in tx_piwikintegration_helper.php::getPiwikSiteIdForPid');
			}

			if(isset($this->piwik_id[$uid])) {
				return $this->piwik_id[$uid];
			}
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
				$tmpl->runThroughTemplates($rootLine,$template_uid);	// This generates the constants/config + hierarchy info for the template.
				$theConstants = $tmpl->generateConfig_constants();	// The editable constants are returned in an array.
				$tmpl->ext_categorizeEditableConstants($theConstants);	// The returned constants are sorted in categories, that goes into the $tmpl->categories array
				$tmpl->ext_regObjectPositions($tplRow["constants"]);		// This array will contain key=[expanded constantname], value=linenumber in template. (after edit_divider, if any)
			}
			if($tmpl->setup['constants']['usr_piwik_id']) {
				$id = intval($tmpl->setup['constants']['usr_piwik_id']);
			} elseif ($tmpl->setup['constants']['usr_name']) {
				$id =  intval($tmpl->setup['constants']['usr_name']);
			} else {
				$id = 0;
			}
			$this->piwik_id[$uid] = $id;
			return $this->piwik_id[$uid];
		}
	}
	$t = new tx_piwikintegration_helper();
	$t->makePiwikInstalled(); 
?>