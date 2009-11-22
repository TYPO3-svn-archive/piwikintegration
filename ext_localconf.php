<?php
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
	if($_EXTCONF['enableModule']) {
		require_once(t3lib_extMgm::extPath('piwikintegration').'class.tx_piwikintegration.php');
		$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][] = 'tx_piwikintegration->contentPostProc_all'; 
	}
}
?>
