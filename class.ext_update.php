<?php
class ext_update {
	function access($what = 'all') {
		return TRUE;
	}
	function main() {
		$func = trim(t3lib_div::_GP('func'));
		if(t3lib_div::_GP('do_update')) {
			if (method_exists($this, $func)) {
				$buffer.= '<b>'.$this->$func().'</b>';
			} else {
				$buffer.='method not found - perhaps not yet implemented?';
			}
		}
		$buffer.= '<h2>installation</h2>';
		$buffer.= $this->getButton('installPiwik'   ,'reinstall piwik');
		$buffer.= $this->getButton('patchPiwik'     ,'patch piwik');
		$buffer.= $this->getButton('removePiwik'    ,'remove piwik');
		$buffer.= $this->getButton('configurePiwik' ,'configure piwik');
		$buffer.= '<h2>collection</h2>';
		$buffer.= $this->getButton('truncatePiwikDB','truncate database');
		return $buffer;
	}
	function installPiwik() {
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$installer =  tx_piwikintegration_install::getInstaller();
		$installer->installPiwik();
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
	function configurePiwik() {
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$installer =  tx_piwikintegration_install::getInstaller();
		$installer->getConfigObject()->makePiwikConfigured();
		return 'Piwik is configured now';
	}
	function getButton($func, $lbl = 'DO IT') {
		$params = array('do_update' => 1, 'func' => $func);
		$onClick = "document.location='" . t3lib_div::linkThisScript($params) . "'; return false;";
		$button = '<input type="submit" value="' . $lbl . '" onclick="' . htmlspecialchars($onClick) . '">';
		$button.= '<br />';
		return $button;
	}
}