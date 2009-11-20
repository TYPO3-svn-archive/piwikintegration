<?php
if (!defined ("TYPO3_MODE"))     die ("Access denied.");
if(TYPO3_MODE=='FE') {
	$_EXTCONF = unserialize($_EXTCONF);
	if($_EXTCONF['enableModule']) {
		require_once(t3lib_extMgm::extPath('piwikintegration').'class.tx_piwikintegration.php');
		$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][] = 'tx_piwikintegration->contentPostProc_all'; 
	}
}
?>
