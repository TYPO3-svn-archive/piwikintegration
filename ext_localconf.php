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
 
if (!defined ("TYPO3_MODE"))     die ("Access denied.");

t3lib_extMgm::addPItoST43(
	$_EXTKEY,
	'pi1/class.tx_piwikintegration_pi1.php',
	'_pi1',
	'list_type',
	1
);
$TYPO3_CONF_VARS['EXTCONF']['templavoila']['mod1']['renderPreviewContentClass'][] = 'EXT:piwikintegration/pi1/class.tx_piwikintegration_pi1_templavoila_preview.php:tx_piwikintegration_pi1_templavoila_preview';

if(TYPO3_MODE=='FE') {
	$_EXTCONF = unserialize($_EXTCONF);
	if($_EXTCONF['enableIndependentMode']) {
		require_once(t3lib_extMgm::extPath('piwikintegration').'class.tx_piwikintegration.php');
		$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = 'tx_piwikintegration->contentPostProc_output'; 
	}
}
?>
