<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 	Kay Strobach (typo3@kay-strobach.de),
*
*  All rights reserved
*
*  This script is free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; version 2 of the License.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * @author  Kay Strobach <typo3@kay-strobach.de>
 * @link http://kay-strobach.de
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * 
 */
 
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

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/pi1/class.tx_piwikintegration_pi1_templavoila_preview.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/pi1/class.tx_piwikintegration_pi1_templavoila_preview.php']);
}

?>