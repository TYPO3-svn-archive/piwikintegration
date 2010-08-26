<?php
include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
class tx_piwikintegration_flexform {
	function init() {
		$this->tablePrefix = tx_piwikintegration_install::getInstaller()->getConfigObject()->getTablePrefix();
	}
	function getSitesForFlexForm(&$PA,&$fobj) {
		$this->init();
		//fetch anonymous accessable idsites
		$erg = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'idsite',
			$this->tablePrefix.'access',
			'login="anonymous"'
		);

		//build array for selecting more information
		$sites = array();
		foreach($erg as $site) {
			$sites[] = $site['idsite'];
		}
		$accessableSites = implode(',',$sites);
		$erg = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'idsite,name,main_url',
			$this->tablePrefix.'site',
			'idsite IN('.$accessableSites.')',
			'',
			'name, main_url, idsite'
		);
		$PA['items'] = array();

		//render items
		while(($site = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($erg)) !== false) {
			$PA['items'][] = array(
				$site['idsite'].' : '.($site['name'] ? $site['name'].' : '.$site['main_url'] : $site['main_url']),
				$site['idsite'],
				'i/domain.gif',
			);
		}
	}
	static function getWidgetsForFlexForm(&$PA,&$fobj) {
		$PA['items'] = array();
		
		tx_piwikintegration_install::getInstaller()->getConfigObject()->initPiwikDatabase();
		$controller = Piwik_FrontController::getInstance()->init();
		$_GET['idSite']=1;
		$widgets = Piwik_GetWidgetsList();
		

		foreach($widgets as $pluginCat => $plugin) {
			foreach($plugin as $widget) {
				$PA['items'][] = array(
					$pluginCat.' : '.$widget['name'],
					base64_encode(json_encode($widget['parameters'])),
					'i/catalog.gif'
				);
			}
		}
	}
}