<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Kay Strobach <info@kay-strobach.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
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

$LANG->includeLLFile('EXT:piwikintegration/mod1/locallang.xml');
require_once(PATH_t3lib . 'class.t3lib_scbase.php');
require_once(PATH_t3lib . 'class.t3lib_page.php');
require_once(t3lib_extMgm::extPath('piwikintegration').'lib/class.tx_piwikintegration_install.php');
require_once(t3lib_extMgm::extPath('piwikintegration').'lib/class.tx_piwikintegration_div.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
// DEFAULT initialization of a module [END]



/**
 * Module 'Statistics' for the 'piwikintegration' extension.
 *
 * @author	Kay Strobach <info@kay-strobach.de>
 * @package	TYPO3
 * @subpackage	tx_piwikintegration
 */
	class  tx_piwikintegration_module1 extends t3lib_SCbase {
		var $pageinfo;

		/**
		 * Initializes the Module
 		 *
		 * @return	void
		 */
		function init()	{
			global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
			$this->piwikHelper = t3lib_div::makeInstance('tx_piwikintegration_div');
			parent::init();

			/*
			if (t3lib_div::_GP('clear_all_cache'))	{
				$this->include_once[] = PATH_t3lib.'class.t3lib_tcemain.php';
			}
			*/
		}

		/**
		 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
		 *
		 * @return	void
		 */
		function menuConfig()	{
			global $LANG;
			$this->MOD_MENU = Array (
				'function' => Array (
					'1' => $LANG->getLL('function1'),
					'2' => $LANG->getLL('function2'),
					'3' => $LANG->getLL('function3'),
				)
			);
			parent::menuConfig();
		}

		/**
		 * Main function of the module. Write the content to $this->content
		 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
		 *
		 * @return	void
		 */
		function main()	{
			global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

			// Access check!
			// The page will show only if there is a valid page and if this page may be viewed by the user
			$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
			$access = is_array($this->pageinfo) ? 1 : 0;			// initialize doc
			$this->doc = t3lib_div::makeInstance('template');
			$this->doc->backPath = $BACK_PATH;

			$set = t3lib_div::_GP('SET');

			$this->doc->setModuleTemplate(t3lib_extMgm::extPath('piwikintegration') . 'mod1/mod_template.html');
			$this->doc->getPageRenderer()->loadExtJS();
			$this->doc->getPageRenderer()->addCssFile(t3lib_extMgm::extRelPath('piwikintegration') . 'mod1/ext-icons.css');
			$this->doc->extJScode = file_get_contents(t3lib_extMgm::extPath('piwikintegration') . 'mod1/extjs.js');
			
			$this->doc->extJScode = str_replace('###1###'       ,$LANG->getLL('function1'),$this->doc->extJScode);
			$this->doc->extJScode = str_replace('###2###'       ,$LANG->getLL('function2'),$this->doc->extJScode);
			$this->doc->extJScode = str_replace('###3###'       ,$LANG->getLL('function3'),$this->doc->extJScode);
			$this->doc->extJScode = str_replace('###piwikAPI###',$this->getPiwikApi()     ,$this->doc->extJScode);
			if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

					// Draw the form
				$this->doc->form = '<form action="" method="post" enctype="multipart/form-data" name="editForm">';

				$this->doc->postCode='
					<script language="javascript" type="text/javascript">
						script_ended = 1;
						if (top.fsMod) top.fsMod.recentIds["web"] = 0;
					</script>
				';
					// Render content:
				$this->checkEnvironment();
			} else {
					// If no access or if ID == zero
				$docHeaderButtons['save'] = '';
				$this->content.=$this->doc->spacer(10);
			}

				// compile document
			$markers['CONTENT'] = $this->content;
					// Build the <body> for the module
			$this->content = $this->doc->startPage($LANG->getLL('title'));
			$this->content.= $this->doc->moduleBody($this->pageinfo, $docHeaderButtons, $markers);
			$this->content.= $this->doc->endPage();
			$this->content = $this->doc->insertStylesAndJS($this->content);

		}
		function getPiwikApi() {
			$content.='Your API Code: '.$BE_USER->user['tx_piwikintegration_api_code'].'<br />';
			#$content.='Your Piwik URL: '.$this->piwikHelper->getPiwikBaseURL();
			$content.='<h3>JavaScriptCode for Piwik</h3>';
			#$content.='<p><code>'.$this->piwikHelper->getPiwikJavaScriptCodeForPid($this->pageinfo['uid']).'</code></p>';
			return $content;
		}
		/**
		 * Prints out the module HTML
		 *
		 * @return	void
		 */
		function printContent()	{
			#$this->content.=$this->doc->endPage();
			echo $this->content;
		}

		/**
		 * Generates the module content
		 *
		 * @return	void
		 */
		function checkEnvironment()	{
			global $BACK_PATH,$TYPO3_CONF_VARS, $BE_USER,$LANG;
			//check if piwik is installed
			
			if(!tx_piwikintegration_install::getInstaller()->checkInstallation()) {
				tx_piwikintegration_install::getInstaller()->installPiwik();
				$flashMessage = t3lib_div::makeInstance(
				    't3lib_FlashMessage',
				    'Piwik installed',
				    'Piwik is now installed / upgraded, wait a moment, to let me reload the page ;)',
				    t3lib_FlashMessage::OK
				);
				t3lib_FlashMessageQueue::addMessage($flashMessage);

				#$this->content ='<html><head><meta http-equiv="refresh" content="1" /></head><body></body></html>';
				//need to die here because of a bug in TYPO3 4.2, the reload will reset the autoloading and all will work fine
				#die($this->content);
				return;
			} elseif(!$this->pageinfo['uid']) {
			    $flashMessage = t3lib_div::makeInstance(
					't3lib_FlashMessage',
					$LANG->getLL('selectpage_description'),
					$LANG->getLL('selectpage_tip'),
					t3lib_FlashMessage::NOTICE
			    );
			    t3lib_FlashMessageQueue::addMessage($flashMessage);
			    #$this->doc->pushFlashMessage($flashMessage);
			    return;
			} elseif($this->piwikHelper->getPiwikSiteIdForPid($this->pageinfo['uid'])) {
				if(!tx_piwikintegration_install::getInstaller()->checkPiwikPatched()) {
					//prevent lost configuration and so the forced repair.
					$exclude = array(
						'config/config.ini.php',
					);
					$this->piwikHelper->makePiwikPatched($exclude);
				}
				return true;
			} else {
				$this->content.='<div class="typo3-message message-warning"><div class="message-header message-left">'.$LANG->getLL('selectpage_tip').'</div>'.$LANG->getLL('selectpage_description').'</div>';
			}
			return false;
		}
		/**
		 * Create the panel of buttons for submitting the form or otherwise perform operations.
		 *
		 * @return	array		all available buttons as an assoc. array
		 */
		protected function getButtons()	{

			$buttons = array(
				'csh' => '',
				'shortcut' => '',
				'save' => ''
			);
				// CSH
			$buttons['csh'] = t3lib_BEfunc::cshItem('_MOD_web_func', '', $GLOBALS['BACK_PATH']);

				// SAVE button
			#$buttons['save'] = '<input type="image" class="c-inputButton" name="submit" value="Update"' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/savedok.gif', '') . ' title="' . $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:rm.saveDoc', 1) . '" />';


				// Shortcut
			if ($GLOBALS['BE_USER']->mayMakeShortcut())	{
				$buttons['shortcut'] = $this->doc->makeShortcutIcon('', 'function', $this->MCONF['name']);
			}

			return $buttons;
		}
	}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_piwikintegration_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>