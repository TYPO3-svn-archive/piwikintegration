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
				$buffer.= '<b>'.$this->$func().'</b>';
			} else {
				$buffer.=$LANG->getLL('methodNotFound');
			}
		}
		$buffer.= '<h2>'.$LANG->getLL('header.installation').'</h2>';
		$buffer.= '<dl class="typo3-tstemplate-ceditor-constant">';
		$buffer.= $this->getButton('installPiwik');
		$buffer.= $this->getButton('updatePiwik');
		$buffer.= $this->getButton('patchPiwik');
		$buffer.= $this->getButton('configurePiwik');
		$buffer.= $this->getButton('removePiwik');
		$buffer.= '</dl>';
		$buffer.= '<h2>'.$LANG->getLL('header.tools').'</h2>';
		$buffer.= '<dl class="typo3-tstemplate-ceditor-constant">';
		$buffer.= $this->getButton('truncatePiwikDB');
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
	function getButton($func) {
		global $LANG;
		$params = array('do_update' => 1, 'func' => $func);
		$onClick = "document.location='" . t3lib_div::linkThisScript($params) . "'; return false;";
		
		$button = '<dt class="typo3-tstemplate-ceditor-label">'.$LANG->getLL('action.'.$func).'</dt>';
		$button.= '<dt class="typo3-dimmed">['.$func.']</dt>';
		$button.= '<dd>'.$LANG->getLL('desc.'.$func).'</dd>';
		$button.= '<dd><div class="typo3-tstemplate-ceditor-row">';
			//<a href="javascript:' . htmlspecialchars($onClick) . '">'.$LANG->getLL('DoIt').'</a>
			if(method_exists($this, $func)) {
				$button.= '<input type="submit" value="' . $LANG->getLL('button.DoIt') . '" onclick="' . htmlspecialchars($onClick) . '">';
			} else {
				$button.= 'N/A';
			}
			
		$button.='</div></dd>';
		return $button;
	}
	/*function truncatePiwikDB() {
		
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