<?php
class ext_update {
	function access($what = 'all') {
		return TRUE;
	}
	function main() {
		global $LANG;
		$LANG->includeLLFile('EXT:piwikintegration/locallang.xml');
		$func = trim(t3lib_div::_GP('func'));
		if(t3lib_div::_GP('do_update')) {
			if (method_exists($this, $func)) {
				$flashMessage = t3lib_div::makeInstance(
					't3lib_FlashMessage',
					$this->$func(),
					'',
					t3lib_FlashMessage::OK
			    );
				$buffer.= $flashMessage->render();
			} else {
				$buffer.=$LANG->getLL('methodNotFound');
			}
		}
		
		$flashMessage = t3lib_div::makeInstance(
					't3lib_FlashMessage',
					$LANG->getLL('installedPiwikNeeded'),
					'',
					t3lib_FlashMessage::INFO
			    );
		$buffer.= $flashMessage->render();
		$buffer.= '<h2>'.$LANG->getLL('header.installation').'</h2>';
		$buffer.= '<dl class="typo3-tstemplate-ceditor-constant">';
		$buffer.= $this->getButton('installPiwik',false);
		$buffer.= $this->getButton('updatePiwik',false);
		$buffer.= $this->getButton('patchPiwik');
		$buffer.= $this->getButton('configurePiwik');
		$buffer.= $this->getButton('removePiwik');
		$buffer.= '</dl>';
		$buffer.= '<h2>'.$LANG->getLL('header.tools').'</h2>';
		$buffer.= '<dl class="typo3-tstemplate-ceditor-constant">';
		$buffer.= $this->getButton('truncatePiwikDB');
		$buffer.= $this->getButton('reInitPiwikDB');
		$buffer.= $this->getButton('showPiwikConfig');
		$buffer.= $this->getButton('enableSuggestedPlugins');
		$buffer.= '</dl>';
		return $buffer;
	}
	function installPiwik() {
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$installer =  tx_piwikintegration_install::getInstaller();
		$installer->installPiwik();
		return 'Piwik installed';
	}
	function updatePiwik() {
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$installer =  tx_piwikintegration_install::getInstaller();
		$installer->updatePiwik();
		return 'Piwik installed';
	}
	function removePiwik() {
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$installer =  tx_piwikintegration_install::getInstaller();
		if($installer->removePiwik()) {
			return 'Piwik removed';
		} else {
			return 'Piwik not removed';
		}
		
	}
	function patchPiwik() {
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$installer =  tx_piwikintegration_install::getInstaller();
		$installer->patchPiwik();
		return 'Piwik patched';
	}
	function configurePiwik() {
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$installer =  tx_piwikintegration_install::getInstaller();
		$installer->getConfigObject()->makePiwikConfigured();
		return 'Piwik is configured now';
	}
	function getButton($func,$piwikNeeded=true) {
		global $LANG;
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$params = array('do_update' => 1, 'func' => $func);
		$onClick = "document.location='" . t3lib_div::linkThisScript($params) . "'; return false;";
		
		$button = '<dt class="typo3-tstemplate-ceditor-label">'.$LANG->getLL('action.'.$func).'</dt>';
		$button.= '<dt class="typo3-dimmed">['.$func.']</dt>';
		$button.= '<dd>'.$LANG->getLL('desc.'.$func).'</dd>';
		$button.= '<dd><div class="typo3-tstemplate-ceditor-row">';
			//<a href="javascript:' . htmlspecialchars($onClick) . '">'.$LANG->getLL('DoIt').'</a>
			try{
				if($piwikNeeded) {
					tx_piwikintegration_install::getInstaller()->getConfigObject();
				}
				if(method_exists($this, $func)) {
					$button.= '<input type="submit" value="' . $LANG->getLL('button.DoIt') . '" onclick="' . htmlspecialchars($onClick) . '">';
				} else {
					$button.= 'N/A';
				}
			} catch(Exception $e) {
				$button.='Piwik Libraries not available';
			}
			
		$button.='</div></dd>';
		return $button;
	}
	function truncatePiwikDB() {
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$path   = tx_piwikintegration_install::getInstaller()->getConfigObject()->initPiwikDatabase();
		$tablesInstalled = Piwik::getTablesInstalled();
		$buffer = 'Dropped Tables:';
		foreach($tablesInstalled as $table) {
			$GLOBALS['TYPO3_DB']->admin_query('DROP TABLE `'.$table.'`');
			 $buffer.= $table.', ';
		}
		return $buffer;
	}
	function reInitPiwikDB() {
		$this->truncatePiwikDB();
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$path   = tx_piwikintegration_install::getInstaller()->getConfigObject()->installDatabase();
		return 'Tables dropped an recreated';
	}//*/
	function showPiwikConfig() {
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$path   = tx_piwikintegration_install::getInstaller()->getAbsInstallPath().'piwik/config/config.ini.php';
		$button.= $path;
		$button.= '</b><pre style="width:80%;height:300px;overflow-y:scroll;border:1px solid silver;padding:10px;">';
		$button.= file_get_contents($path);
		$button.= '</pre><b>';
		return $button;
	}
	function enableSuggestedPlugins() {
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$config =  tx_piwikintegration_install::getInstaller()->getConfigObject();
		$config->enablePlugin('TYPO3Login');
		$config->enablePlugin('TYPO3Menu');
		$config->enablePlugin('SecurityInfo');
		$config->enablePlugin('DBStats');
		$config->enablePlugin('AnonymizeIP');
		$config->disablePlugin('Login');
		return 'installed: TYPO3Login, TYPO3Menu, SecurityInfo, DBStats, AnonymizeIP<br />removed: Login';
	}
}