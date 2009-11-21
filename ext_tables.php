<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE') {
	t3lib_extMgm::addModulePath('web_txpiwikintegrationM1', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
		
	t3lib_extMgm::addModule('web', 'txpiwikintegrationM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
}

t3lib_extMgm::addStaticFile($_EXTKEY,'static/piwik_integration/', 'Piwik Integration');

$tempColumns = array (
	'tx_piwikintegration_api_code' => array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:piwikintegration/locallang_db.xml:be_users.tx_piwikintegration_api_code',		
		'config' => array (
			'type' => 'none',
		)
	),
);


t3lib_div::loadTCA('be_users');
t3lib_extMgm::addTCAcolumns('be_users',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('be_users','tx_piwikintegration_api_code;;;;1-1-1');

//add flexform to pi1
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'pi_flexform';
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] = 'layout,select_key,pages,recursive';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY .'_pi1', 'FILE:EXT:piwikintegration/pi1/flexform_ds.xml');

//add pi1 plugin
t3lib_extMgm::addPlugin(
	array(
		'LLL:EXT:piwikintegration/pi1/locallang.xml:piwikintegration_pi1',
		$_EXTKEY.'_pi1'
	)
);
if (TYPO3_MODE=="BE")    {
	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_piwikintegration_pi1_wizicon"] = 
	t3lib_extMgm::extPath($_EXTKEY)."pi1/class.tx_piwikintegration_pi1_wizicon.php";
}


?>