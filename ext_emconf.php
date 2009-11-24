<?php

########################################################################
# Extension Manager/Repository config file for ext "piwikintegration".
#
# Auto generated 24-11-2009 09:36
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Piwik Backend integration',
	'description' => 'Uses EXT:piwik to inserts Data in the HTML header and gives BE-Users the right to see the data for their sites. Autoupdate of Piwik will work as TYPO3-Admin!',
	'category' => 'module',
	'shy' => 0,
	'version' => '2.0.1',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Kay Strobach',
	'author_email' => 'typo3@kay-strobach.de',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.0-6.0.0',
			'typo3' => '4.0.0-4.3.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'piwik' => '1.0.2-3.0.0',
		),
	),
	'_md5_values_when_last_written' => 'a:31:{s:9:"ChangeLog";s:4:"54ba";s:10:"README.txt";s:4:"ee2d";s:29:"class.tx_piwikintegration.php";s:4:"e172";s:36:"class.tx_piwikintegration_helper.php";s:4:"a8c3";s:21:"ext_conf_template.txt";s:4:"42cb";s:12:"ext_icon.gif";s:4:"d803";s:17:"ext_localconf.php";s:4:"02a3";s:14:"ext_tables.php";s:4:"b4e6";s:14:"ext_tables.sql";s:4:"d7e4";s:16:"locallang_db.xml";s:4:"f8e2";s:14:"doc/manual.sxw";s:4:"1b21";s:19:"doc/wizard_form.dat";s:4:"65a4";s:20:"doc/wizard_form.html";s:4:"fd83";s:13:"mod1/conf.php";s:4:"2359";s:14:"mod1/index.php";s:4:"3004";s:18:"mod1/locallang.xml";s:4:"8d49";s:22:"mod1/locallang_mod.xml";s:4:"e238";s:22:"mod1/mod_template.html";s:4:"ee0a";s:19:"mod1/moduleicon.gif";s:4:"d803";s:14:"pi1/ce_wiz.gif";s:4:"0ee1";s:37:"pi1/class.tx_piwikintegration_pi1.php";s:4:"8405";s:57:"pi1/class.tx_piwikintegration_pi1_templavoila_preview.php";s:4:"4c21";s:45:"pi1/class.tx_piwikintegration_pi1_wizicon.php";s:4:"febf";s:19:"pi1/flexform_ds.xml";s:4:"ab44";s:17:"pi1/locallang.xml";s:4:"ff18";s:35:"piwik_patches/config/config.ini.php";s:4:"af0b";s:41:"piwik_patches/plugins/TYPO3Login/Auth.php";s:4:"108a";s:47:"piwik_patches/plugins/TYPO3Login/Controller.php";s:4:"3ba9";s:47:"piwik_patches/plugins/TYPO3Login/TYPO3Login.php";s:4:"8695";s:38:"static/piwik_integration/constants.txt";s:4:"d41d";s:34:"static/piwik_integration/setup.txt";s:4:"ab5e";}',
	'suggests' => array(
	),
);

?>