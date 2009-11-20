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
?>