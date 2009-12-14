<?php

########################################################################
# Extension Manager/Repository config file for ext "piwikintegration".
#
# Auto generated 14-12-2009 20:21
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
	'version' => '2.2.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
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
			'piwik' => '',
			'scheduler' => '',
		),
	),
	'_md5_values_when_last_written' => 'a:41:{s:9:"ChangeLog";s:4:"54ba";s:10:"README.txt";s:4:"ee2d";s:29:"class.tx_piwikintegration.php";s:4:"0733";s:38:"class.tx_piwikintegration_be_users.php";s:4:"ceae";s:36:"class.tx_piwikintegration_helper.php";s:4:"0bbb";s:46:"class.tx_piwikintegration_piwikArchiveTask.php";s:4:"f40f";s:16:"ext_autoload.php";s:4:"56fe";s:21:"ext_conf_template.txt";s:4:"efdc";s:12:"ext_icon.gif";s:4:"d803";s:17:"ext_localconf.php";s:4:"b2f8";s:14:"ext_tables.php";s:4:"6ae5";s:14:"ext_tables.sql";s:4:"d7e4";s:13:"locallang.xml";s:4:"679f";s:16:"locallang_db.xml";s:4:"f8e2";s:14:"doc/manual.sxw";s:4:"1035";s:19:"doc/wizard_form.dat";s:4:"65a4";s:20:"doc/wizard_form.html";s:4:"fd83";s:13:"mod1/conf.php";s:4:"2359";s:14:"mod1/index.php";s:4:"3af8";s:18:"mod1/locallang.xml";s:4:"8d49";s:22:"mod1/locallang_mod.xml";s:4:"e238";s:22:"mod1/mod_template.html";s:4:"ee0a";s:19:"mod1/moduleicon.gif";s:4:"d803";s:14:"pi1/ce_wiz.gif";s:4:"0ee1";s:37:"pi1/class.tx_piwikintegration_pi1.php";s:4:"8fdc";s:57:"pi1/class.tx_piwikintegration_pi1_templavoila_preview.php";s:4:"fa7f";s:45:"pi1/class.tx_piwikintegration_pi1_wizicon.php";s:4:"d0f6";s:19:"pi1/flexform_ds.xml";s:4:"ab44";s:17:"pi1/locallang.xml";s:4:"ff18";s:35:"piwik_patches/config/config.ini.php";s:4:"af0b";s:41:"piwik_patches/plugins/TYPO3Login/Auth.php";s:4:"212a";s:47:"piwik_patches/plugins/TYPO3Login/Controller.php";s:4:"6a71";s:47:"piwik_patches/plugins/TYPO3Login/TYPO3Login.php";s:4:"0156";s:46:"piwik_patches/plugins/TYPO3Menu/Controller.php";s:4:"16b2";s:45:"piwik_patches/plugins/TYPO3Menu/TYPO3Menu.php";s:4:"89cd";s:44:"piwik_patches/plugins/TYPO3Menu/css/main.css";s:4:"7eb9";s:45:"piwik_patches/plugins/TYPO3Menu/css/typo3.css";s:4:"04d2";s:42:"piwik_patches/plugins/TYPO3Menu/js/main.js";s:4:"48a4";s:57:"piwik_patches/plugins/TYPO3Menu/pics/login-box-header.png";s:4:"43f3";s:38:"static/piwik_integration/constants.txt";s:4:"d41d";s:34:"static/piwik_integration/setup.txt";s:4:"ab5e";}',
	'suggests' => array(
	),
);

?>