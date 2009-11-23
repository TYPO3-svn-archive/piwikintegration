<?php

require_once(t3lib_extMgm::extPath('piwikintegration').'class.tx_piwikintegration_helper.php');

class tx_piwikintegration_pi1_templavoila_preview {
	function renderPreviewContent_preProcess($row, $table, &$alreadyRendered, &$reference) {
		if(($row['CType'] == 'list') && ($row['list_type'] == 'piwikintegration_pi1')) {
			$content = '<strong>Piwik in FE</strong>';
			$content = $reference->link_edit($content, $table, $row['uid']);
			$piFlexForm = t3lib_div::xml2array($row['pi_flexform']);
			foreach ( $piFlexForm['data'] as $sheet => $data ) {
			   foreach ( $data as $lang => $value ) {
			       foreach ( $value as $key => $val ) {
			           $conf[$key] = $piFlexForm['data'][$sheet]['lDEF'][$key]['vDEF'];
			       }
			   }
			}
			
			$this->extConf = array(
				'widget' => json_decode(base64_decode($conf['widget']),true),
				'height' => $conf['div_height']
			);
			$this->extConf['widget']['idSite']           = $conf['idsite'];
			$this->extConf['widget']['period']           = $conf['period'];
			$this->extConf['widget']['date']             = 'yesterday';
			$this->extConf['widget']['viewDataTable']    = $conf['viewDataTable']; 
			$this->extConf['widget']['moduleToWidgetize']= $this->extConf['widget']['module'];
			$this->extConf['widget']['actionToWidgetize']= $this->extConf['widget']['action'];
			unset($this->extConf['widget']['module']);
			unset($this->extConf['widget']['action']);
			
			$helper = new tx_piwikintegration_helper();
			$obj.= '<object width="100%" type="text/html" height="'.intval($this->extConf['height']).'" data="';
			$obj.= '../../../../typo3conf/piwik/piwik/index.php?module=Widgetize&action=iframe'.t3lib_div::implodeArrayForUrl('',$this->extConf['widget']);
			$obj.= '&disableLink=1"></object>';
			
			$content.=$obj;
			
			$alreadyRendered = true;
		
			return $content;
		}
	}
}
?>