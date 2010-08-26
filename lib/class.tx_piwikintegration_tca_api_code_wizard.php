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
 * lib/class.tx_piwikintegration_tca_api_code_wizard.php
 *
 * api code wizard for be_user form
 *
 * $Id$
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */
 

class tx_piwikintegration_tca_api_code_wizard {
	function main(&$PA, &$fobj) {
		  $onClick = 'date = new Date(); document.'.$PA['formName'].'[\''.$PA['itemName'].'\'].value=MD5(date.getTime()+document.location.href);'
			.implode('',$PA['fieldChangeFunc'])    // Necessary to tell TCEforms that the value is updated.
			.'return false;';		
		return '<a href="" onClick="'.$onClick.'" title="refresh or set the api key, unique is evaluated on the server"><img '.t3lib_iconWorks::skinImg('gfx/','import_update.gif').' alt="refresh or set the api key, unique is evaluated on the server"></a>';
	}
}