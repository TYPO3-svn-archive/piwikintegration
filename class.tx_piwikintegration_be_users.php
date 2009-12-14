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
class tx_piwikintegration_be_users {
	function tx_piwikintegration_api_code_wizard($PA, $fobj) {
		  $onClick = 'date = new Date(); document.'.$PA['formName'].'[\''.$PA['itemName'].'\'].value=MD5(date.getTime()+document.location.href);'
			.implode('',$PA['fieldChangeFunc'])    // Necessary to tell TCEforms that the value is updated.
			.'return false;';		
		return '<a href="" onClick="'.$onClick.'" title="refresh or set the api key, unique is evaluated on the server"><img '.t3lib_iconWorks::skinImg('gfx/','import_update.gif').' alt="refresh or set the api key, unique is evaluated on the server"></a>';
	}
}
	
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/class.tx_piwikintegration_be_users.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/class.tx_piwikintegration_be_users.php']);
}

?>