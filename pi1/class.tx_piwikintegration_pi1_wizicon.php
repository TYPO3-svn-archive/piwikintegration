<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 	Kay Strobach (typo3@kay-strobach.de),
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
 * @author  Kay Strobach <typo3@kay-strobach.de>
 * @link http://kay-strobach.de
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * 
 */


/**
 * adds the wizard icon.
 *
 * @author	Ingo Renner <typo3@ingo-renner.com>
 */
class tx_piwikintegration_pi1_wizicon {
	
	/**
	 * Adds the tt_address pi1 wizard icon
	 *
	 * @param	array		Input array with wizard items for plugins
	 * @return	array		Modified input array, having the item for tt_address
	 * pi1 added.
	 */
	function proc($wizardItems)	{
		global $LANG;

		$LL = $this->includeLocalLang();

		$wizardItems['plugins_tx_piwikintegration_pi1'] = array(
			'icon'        => t3lib_extMgm::extRelPath('piwikintegration').'pi1/ce_wiz.gif',
			'title'       => $LANG->getLLL('pi1_wizard_title',$LL),
			'description' => $LANG->getLLL('pi1_wizard_description',$LL),
			'params'      => '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=piwikintegration_pi1'
		);

		return $wizardItems;
	}
	
	/**
	 * Includes the locallang file for the 'tt_address' extension
	 *
	 * @return	array		The LOCAL_LANG array
	 */
	function includeLocalLang()	{
		$llFile     = t3lib_extMgm::extPath('piwikintegration').'pi1/locallang.xml';
		$LOCAL_LANG = t3lib_div::readLLXMLfile($llFile, $GLOBALS['LANG']->lang);
		
		return $LOCAL_LANG;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/pi1/class.tx_piwikintegration_pi1_wizicon.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/pi1/class.tx_piwikintegration_pi1_wizicon.php']);
}

?>