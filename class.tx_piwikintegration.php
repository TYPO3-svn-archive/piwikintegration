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
class tx_piwikintegration	 {

    /**
     * main processing method
     */
    function contentPostProc_output(&$params, &$reference){
        $this->piwikHelper = new tx_piwikintegration_helper();
		// process the page with these options
        $content       = $params['pObj']->content;
		$this->extConf = $params['pObj']->config['config']['tx_piwik.'];
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
}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/piwikintegration/class.tx_piwikintegration.php"])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/piwikintegration/class.tx_piwikintegration.php"]);
}

?>
