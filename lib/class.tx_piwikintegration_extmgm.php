<?php
class tx_piwikintegration_extmgm {
	function emMakeDBList($params) {
		 /* Pull the current fieldname and value from constants */
        $fieldName  = $params['fieldName'];
        $fieldValue = $params['fieldValue'];
        $dbs        = $GLOBALS['TYPO3_DB']->admin_get_dbs();
		$buffer.='<select name="'.$fieldName.'">';
        $buffer.='<option value="">---TYPO3DB---</option>';
		foreach($dbs as $db) {
			$buffer.= '<option value="'.htmlspecialchars($db).'"';
			if($db == $fieldValue) {
				$buffer.=' selected="selected"';
			}
			$buffer.= '>'.htmlspecialchars($db).'</option>';
		}
        $buffer.='</select>';
		return $buffer;
	}
	function emSaveConstants($par) {
		return;
		if($par['extKey'] == 'piwikintegration' && t3lib_div::_POST('submit')) {			
			$newconf = t3lib_div::_POST();
			$newconf = $newconf['data'];
			//init piwik to get table prefix
				$this->initPiwik();
			//walk through changes
			if($this->tableDbPrefix!==$newconf['databaseTablePrefix']) {
				//create shortVars
					if($newconf['databaseTablePrefix'] == '') {
						$newDbPrefix          = '';
						$newDbPrefixForRename = TYPO3_db.'.';
					} else {
						$newDbPrefix          = $newconf['databaseTablePrefix'].'.';
						$newDbPrefixForRename = $newconf['databaseTablePrefix'].'.';
					}
				//get tablenames and rename tables
					$suffix='';
					if($this->tableDbPrefix!='') {
						$suffix = ' FROM '.substr($this->tableDbPrefix,0,-1);
					}
					$erg = $GLOBALS['TYPO3_DB']->admin_query('SHOW TABLES'.$suffix);
					while(false !==($row=$GLOBALS['TYPO3_DB']->sql_fetch_row($erg))) {
						if(substr($row[0],0,20)=='tx_piwikintegration_') {
							$GLOBALS['TYPO3_DB']->admin_query('RENAME TABLE '.$this->tableDbPrefix.$row[0].' TO '.$newDbPrefixForRename.$row[0]);
						}
					}
				//change config
					$piwikConfig = Zend_Registry::get('config');
					$database = $piwikConfig->database->toArray();
					$database['dbname']        = substr($newDbPrefixForRename,0,-1);
					$database['tables_prefix'] = "tx_piwikintegration_";
					$piwikConfig->database = $database;
			}
		}
	}
} 