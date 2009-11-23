<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 - 2007 Ingo Renner <typo3@ingo-renner.com>
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('piwikintegration').'class.tx_piwikintegration_helper.php');


/**
 * main class for the tt_address plugin, outputs addresses either by direct
 * selection or by selection via groups or a combination of both
 *
 * @author Ingo Renner <typo3@ingo-renner.com>
 */
class tx_piwikintegration_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_piwikintegration_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_piwikintegration_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'tx_piwikintegration_pi1';	// The extension key.
	var $pi_checkCHash = true;

	function main($content, $conf) {
		$content = $this->init($conf);
		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * initializes the configuration for the plugin and gets the settings from
	 * the flexform
	 *
	 * @param	array	$conf: array with TS configuration
	 * @return	void
	 */
	function init($conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexForm();
		
		
		$this->extConf['widget']                     = json_decode(base64_decode($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'widget')),true);
		$this->extConf['widget']['period']           = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'period');
		$this->extConf['widget']['moduleToWidgetize']= $this->extConf['widget']['module'];
		$this->extConf['widget']['actionToWidgetize']= $this->extConf['widget']['action'];
		$this->extConf['widget']['idSite']           = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'idsite');
		$this->extConf['height']                     = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'div_height');
		$this->extConf['widget']['date']             = 'yesterday';
		$this->extConf['widget']['viewDataTable']    = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'viewDataTable');
		unset($this->extConf['widget']['module']);
		unset($this->extConf['widget']['action']);
		
		$helper = new tx_piwikintegration_helper();
		
		$content.= '<div id="widgetIframe"><iframe width="100%" height="'.intval($this->extConf['height']).'" src="';
		$content.= $helper->getPiwikBaseURL().'index.php?module=Widgetize&action=iframe'.t3lib_div::implodeArrayForUrl('',$this->extConf['widget']);
		$content.= '&disableLink=1" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"></iframe></div>';

		return $content;
	}



}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/pi1/class.piwikintegration_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/pi1/class.piwikintegration_pi1.php']);
}

?>