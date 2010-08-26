<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Kay Strobach (typo3@kay-strobach.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * lib/class.tx_piwikintegration_extmgm.php
 *
 * functions for the extmgm render forms and react on changes
 *
 * $Id$
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */
 
 
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