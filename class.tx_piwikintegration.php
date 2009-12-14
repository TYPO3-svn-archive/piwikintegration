<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008     Frank Nägler (typo3@naegler.net),
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
/**
 * Hooks for the 'piwik' extension.
 * 
 * This file is partly based on the piwik extension of Frank Nägler  
 *
 * @author    Frank Nägler <typo3@naegler.net>
 * @author    Kay Strobach <typo3@kay-strobach.de>
 */
require_once('class.tx_piwikintegration_helper.php');
class tx_piwikintegration {
	function init(&$params, &$reference) {
		//init helper object
		$this->piwikHelper = new tx_piwikintegration_helper();
		// process the page with these options
		$this->extConf = $params['pObj']->config['config']['tx_piwik.'];
		//read base url
		$this->baseUrl = $params['pObj']->config['config']['baseURL'];
	}
    /**
     * main processing method
     */
    function contentPostProc_output(&$params, &$reference){
        $this->init($params,$reference);
        $content       = $params['pObj']->content;
		$beUserLogin   = $params['pObj']->beUserLogin;
		
		//check wether there is a BE User loggged in, if yes avoid to display the tracking code!
		if($beUserLogin == 1) {
			return;
		}
		
		//check wether needed parameters are set properly
		if (!($this->extConf['piwik_idsite']) || !($this->extConf['piwik_host'])) {
			return;
		}
		
		$piwikCode     = $this->piwikHelper->getPiwikJavaScriptCodeForSite($this->extConf['piwik_idsite']); 
		$piwikCode     = str_replace('&gt;','>',$piwikCode);
		$piwikCode     = str_replace('&lt;','<',$piwikCode);
		$piwikCode     = str_replace('&quot;','"',$piwikCode);
		$piwikCode     = str_replace('<br />','',$piwikCode);

        $params['pObj']->content = str_replace('</body>','<!-- EXT:piwikintegration independent mode, disable independent mode, if you have 2 trackingcode snippets! -->'.$piwikCode.'<!-- /EXT:piwikintegration --></body>',$params['pObj']->content);
	}
    /**
     * check wether the siteid exists or not!
     */
     function contentPostProc_all(&$params, &$reference){
		$this->init($params,$reference);
		$erg = $GLOBALS['TYPO3_DB']->exec_SELECTquery (
			'*',
			'tx_piwikintegration_site',
			'idsite='.intval($this->extConf['piwik_idsite'])
		);
		$numRows = $GLOBALS['TYPO3_DB']->sql_num_rows($erg);
		//check wether siteid exists
		if($numRows==0) {
			//if not -> create
			$GLOBALS['TYPO3_DB']->exec_INSERTquery(
				'tx_piwikintegration_site',
				array(
					'idsite'   => intval($this->extConf['piwik_idsite']),
					'name'     => 'ID '.intval($this->extConf['piwik_idsite']),
					'main_url' => $this->baseUrl, 
				)
			);
		} elseif($numRows>1) {
			//more than once -> error
			die('piwik idsite table is inconsistent');
		}
	} 
}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/piwikintegration/class.tx_piwikintegration.php"])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/piwikintegration/class.tx_piwikintegration.php"]);
}

?>