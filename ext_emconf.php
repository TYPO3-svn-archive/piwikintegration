<?php

########################################################################
# Extension Manager/Repository config file for ext "piwikintegration".
#
# Auto generated 04-10-2012 20:55
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
	'version' => '3.9.2',
	'dependencies' => '',
	'conflicts' => 'dbal',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
	'state' => 'stable',
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
			'typo3' => '4.4.0-6.0.99',
		),
		'conflicts' => array(
			'typo3' => '3.0.0-4.3.99',
			'dbal' => '1.0.0-99.0.0',
		),
		'suggests' => array(
			'piwik' => '2.0.0-2.999.0',
		),
	),
	'_md5_values_when_last_written' => 'a:69:{s:9:"ChangeLog";s:4:"54ba";s:10:"README.txt";s:4:"ee2d";s:20:"class.ext_update.php";s:4:"8ca0";s:21:"ext_conf_template.txt";s:4:"c7d2";s:12:"ext_icon.gif";s:4:"d803";s:17:"ext_localconf.php";s:4:"d471";s:14:"ext_tables.php";s:4:"56f8";s:14:"ext_tables.sql";s:4:"d7e4";s:13:"locallang.xml";s:4:"2045";s:16:"locallang_db.xml";s:4:"f8e2";s:34:"Classes/Hooks/BeUserProcessing.php";s:4:"426e";s:22:"Classes/Lib/Config.php";s:4:"8786";s:19:"Classes/Lib/Div.php";s:4:"2ff9";s:22:"Classes/Lib/Extmgm.php";s:4:"6a32";s:23:"Classes/Lib/Install.php";s:4:"79f2";s:34:"Classes/SchedulerTasks/Archive.php";s:4:"4246";s:29:"Classes/Tracking/Tracking.php";s:4:"0ead";s:14:"doc/manual.sxw";s:4:"dc33";s:19:"doc/wizard_form.dat";s:4:"65a4";s:20:"doc/wizard_form.html";s:4:"fd83";s:13:"mod1/conf.php";s:4:"1bb0";s:18:"mod1/ext-icons.css";s:4:"4a8e";s:13:"mod1/extjs.js";s:4:"5aa9";s:14:"mod1/index.php";s:4:"a475";s:18:"mod1/locallang.xml";s:4:"d637";s:22:"mod1/locallang_mod.xml";s:4:"e238";s:22:"mod1/mod_template.html";s:4:"0128";s:19:"mod1/moduleicon.gif";s:4:"d803";s:15:"mod1/notice.png";s:4:"bc50";s:26:"mod1/img/default_green.gif";s:4:"1e24";s:27:"mod1/img/default_purple.gif";s:4:"78eb";s:24:"mod1/img/default_red.gif";s:4:"dc05";s:27:"mod1/img/default_yellow.gif";s:4:"401f";s:24:"mod1/img/information.png";s:4:"6856";s:24:"mod1/img/window-open.png";s:4:"75f4";s:14:"pi1/ce_wiz.gif";s:4:"0ee1";s:42:"pi1/class.tx_piwikintegration_flexform.php";s:4:"0c13";s:37:"pi1/class.tx_piwikintegration_pi1.php";s:4:"0858";s:57:"pi1/class.tx_piwikintegration_pi1_templavoila_preview.php";s:4:"b618";s:45:"pi1/class.tx_piwikintegration_pi1_wizicon.php";s:4:"a351";s:19:"pi1/flexform_ds.xml";s:4:"7c66";s:17:"pi1/locallang.xml";s:4:"4ba7";s:35:"piwik_patches/config/config.ini.php";s:4:"17d9";s:52:"piwik_patches/plugins/KSVisitorImport/Controller.php";s:4:"0162";s:57:"piwik_patches/plugins/KSVisitorImport/KSVisitorImport.php";s:4:"cf3e";s:49:"piwik_patches/plugins/KSVisitorImport/Tracker.php";s:4:"aeb9";s:47:"piwik_patches/plugins/KSVisitorImport/Visit.php";s:4:"6d64";s:57:"piwik_patches/plugins/KSVisitorImport/Import/Abstract.php";s:4:"8917";s:62:"piwik_patches/plugins/KSVisitorImport/Import/ApacheDefault.php";s:4:"192a";s:63:"piwik_patches/plugins/KSVisitorImport/Import/ApacheExtended.php";s:4:"19da";s:58:"piwik_patches/plugins/KSVisitorImport/Import/GoogleCsv.php";s:4:"b6ce";s:49:"piwik_patches/plugins/KSVisitorImport/lang/de.php";s:4:"99b9";s:49:"piwik_patches/plugins/KSVisitorImport/lang/en.php";s:4:"8d6f";s:60:"piwik_patches/plugins/KSVisitorImport/templates/generate.tpl";s:4:"2ce9";s:57:"piwik_patches/plugins/KSVisitorImport/templates/index.tpl";s:4:"aee9";s:58:"piwik_patches/plugins/KSVisitorImport/templates/styles.css";s:4:"ab50";s:41:"piwik_patches/plugins/TYPO3Login/Auth.php";s:4:"7af4";s:47:"piwik_patches/plugins/TYPO3Login/Controller.php";s:4:"e310";s:47:"piwik_patches/plugins/TYPO3Login/TYPO3Login.php";s:4:"6f5a";s:46:"piwik_patches/plugins/TYPO3Menu/Controller.php";s:4:"354f";s:45:"piwik_patches/plugins/TYPO3Menu/TYPO3Menu.php";s:4:"a728";s:44:"piwik_patches/plugins/TYPO3Menu/css/main.css";s:4:"867b";s:45:"piwik_patches/plugins/TYPO3Menu/css/typo3.css";s:4:"b6d8";s:42:"piwik_patches/plugins/TYPO3Menu/js/main.js";s:4:"25cb";s:57:"piwik_patches/plugins/TYPO3Menu/pics/login-box-header.png";s:4:"43f3";s:49:"piwik_patches/plugins/TYPO3Widgets/Controller.php";s:4:"5842";s:51:"piwik_patches/plugins/TYPO3Widgets/TYPO3Widgets.php";s:4:"f250";s:38:"static/piwik_integration/constants.txt";s:4:"85c1";s:34:"static/piwik_integration/setup.txt";s:4:"760c";}',
	'suggests' => array(
	),
);

?>