<?php

class tx_piwikintegration_div {
		/**
	 * returns the piwik site id for a given page
	 * call it with $this->pageinfo['uid'] as param from a backend module
	 *
	 * @param	integer		$uid: Page ID
	 * @return	integer     piwik site id
	 */
	function getPiwikSiteIdForPid($uid) {
		tx_piwikintegration_install::getInstaller()->getConfigObject()->initPiwikFrameWork();
		$this->tablePrefix = tx_piwikintegration_install::getInstaller()->getConfigObject()->getTablePrefix();
		#throw new Exception($this->tablePrefix);

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
			$GLOBALS['TYPO3_DB']->debugOutput=true;
			$erg = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'*',
				$this->tablePrefix.'site',
				'idsite = '.intval($id),
				'',
				'',
				'0,1'
			);
			if(count($erg)==0) {
				tx_piwikintegration_install::getInstaller()->getConfigObject()->initPiwikFrameWork();
				tx_piwikintegration_install::getInstaller()->getConfigObject()->initPiwikDatabase();
				
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
	/**
	 * This function makes a page statistics accessable for a user
	 * call it with $this->pageinfo['uid'] as param from a backend module
	 *
	 * @param	integer		$uid: pid for which the user will get access
	 * @return	void
	 */
	function correctUserRightsForPid($uid) {
		if($uid <= 0 || $uid!=intval($uid)) {
			throw new Exception('Problem with uid in tx_piwikintegration_helper.php::correctUserRightsForPid');
		}
		$beUserName = $GLOBALS['BE_USER']->user['username'];
		/**
		 * ensure, that the user is added to the database
		 * needed to change user attributes (mail, ...)	
		 * tx_piwikintegration_user		 	 
		 */		 		

		$erg = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			$this->tablePrefix.'user',
			'login="'.$beUserName.'"',
			'',
			'',
			'0,1'
			);
		if(count($erg)!=1) {
			$GLOBALS['TYPO3_DB']->exec_INSERTquery(
					$this->tablePrefix.'user',
					array(
						'login'          => $beUserName,
						'alias'          => $GLOBALS['BE_USER']->user['realName'] ? $GLOBALS['BE_USER']->user['realName'] : $beUserName,
						'email'          => $GLOBALS['BE_USER']->user['email'],
						'date_registered'=> date('Y-m-d H:i:s',time()),
					)
				);
		} else {
			$GLOBALS['TYPO3_DB']->exec_Updatequery(
					$this->tablePrefix.'user',
					'login = "'.mysql_escape_string($beUserName).'"',
					array(
						'alias' => $GLOBALS['BE_USER']->user['realName'] ? $GLOBALS['BE_USER']->user['realName'] : $beUserName,
						'email' => $GLOBALS['BE_USER']->user['email'],
					)
				);		
		}
		/**
		 * ensure, that user's right are added to the database
		 * tx_piwikintegration_access		 
		 */
		if($GLOBALS['BE_USER']->user['admin']!=1) {
			$erg = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					'*',
					$this->tablePrefix.'access',
					'login="'.$beUserName.'" AND idsite='.$this->getPiwikSiteIdForPid($uid),
					'',
					'',
					'0,1'
			);
			if(count($erg)==0) {
				$GLOBALS['TYPO3_DB']->exec_INSERTquery(
					$this->tablePrefix.'access',
					array(
						'login' => $beUserName,
						'idsite'=> $this->getPiwikSiteIdForPid($uid),
						'access'=> 'view',
					)
				);
			}
		}
	}
}