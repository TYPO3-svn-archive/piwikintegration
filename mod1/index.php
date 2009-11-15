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
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

#error_reporting(E_ALL);
#ini_set('display_errors',2048);

$LANG->includeLLFile('EXT:piwikintegration/mod1/locallang.xml');
require_once(PATH_t3lib . 'class.t3lib_scbase.php');
require_once(PATH_t3lib . 'class.t3lib_page.php');
require_once(t3lib_extMgm::extPath('piwikintegration').'class.tx_piwikintegration.php');
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
				 * @return	void
				 */
				function init()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

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
				 * @return	[type]		...
				 */
				function main()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					// Access check!
					// The page will show only if there is a valid page and if this page may be viewed by the user
					$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
					$access = is_array($this->pageinfo) ? 1 : 0;
				
						// initialize doc
					$this->doc = t3lib_div::makeInstance('template');
					$this->doc->setModuleTemplate(t3lib_extMgm::extPath('piwikintegration') . 'mod1//mod_template.html');
					$this->doc->backPath = $BACK_PATH;
					$docHeaderButtons = $this->getButtons();

					if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

							// Draw the form
						$this->doc->form = '<form action="" method="post" enctype="multipart/form-data">';

							// JavaScript
						$this->doc->JScode = '
							<script language="javascript" type="text/javascript">
								script_ended = 0;
								function jumpToUrl(URL)	{
									document.location = URL;
								}
							</script>
						';
						$this->doc->postCode='
							<script language="javascript" type="text/javascript">
								script_ended = 1;
								if (top.fsMod) top.fsMod.recentIds["web"] = 0;
							</script>
						';
							// Render content:
						$this->moduleContent();
					} else {
							// If no access or if ID == zero
						$docHeaderButtons['save'] = '';
						$this->content.=$this->doc->spacer(10);
					}

						// compile document
					$markers['FUNC_MENU'] = t3lib_BEfunc::getFuncMenu($this->id, 'SET[function]', $this->MOD_SETTINGS['function'], $this->MOD_MENU['function']);
					$markers['CONTENT'] = $this->content;

							// Build the <body> for the module
					$this->content = $this->doc->startPage($LANG->getLL('title'));
					$this->content.= $this->doc->moduleBody($this->pageinfo, $docHeaderButtons, $markers);
					$this->content.= $this->doc->endPage();
					$this->content = $this->doc->insertStylesAndJS($this->content);
				
				}

				/**
				 * Prints out the module HTML
				 *
				 * @return	void
				 */
				function printContent()	{

					$this->content.=$this->doc->endPage();
					echo $this->content;
				}

				/**
				 * Generates the module content
				 *
				 * @return	void
				 */
				function moduleContent()	{
					global $BACK_PATH,$TYPO3_CONF_VARS, $BE_USER,$LANG;
					if($this->getPiwikId()) {
						$this->correctUserRights();
						tx_piwikintegration::correctPiwikConfiguration();

						switch((string)$this->MOD_SETTINGS['function'])	{
							case 1:
								$date    = 'yesterday'; 
								$content.='<style type="text/css">';
									$content.='.widgetIframe    {width:400px;display:inline;float:left;border:1px solid #B2B9C5; margin:5px;background-color:white; clear:none;}
											   .dashboardcol    {width:410px;float:left;}
											   .widgetIframe h2 {background-color:white; display:inline;margin:5px;}';
								$content.='</style>';
								$widgets  = array (
									#'overview'          => '<div id="widgetIframe"><iframe width="100%" height="350" src="'.t3lib_extMgm::extRelPath('piwikintegration').'piwik/index.php?module=Widgetize&action=iframe&moduleToWidgetize=VisitsSummary&actionToWidgetize=index&idSite=5&period=week&date='.$date.'&disableLink=1" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"></iframe></div>',
									#'live'              => '<div id="widgetIframe"><iframe width="100%" height="350" src="'.t3lib_extMgm::extRelPath('piwikintegration').'piwik/index.php?module=Widgetize&action=iframe&moduleToWidgetize=Live&actionToWidgetize=widget&idSite=5&period=week&date='.$date.'&disableLink=1" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"></iframe></div>',
									#'browserfamilies'   => '<div id="widgetIframe"><iframe width="100%" height="350" src="'.t3lib_extMgm::extRelPath('piwikintegration').'piwik/index.php?module=Widgetize&action=iframe&moduleToWidgetize=UserSettings&actionToWidgetize=getBrowserType&idSite='.$this->getPiwikId().'&period=week&date='.$date.'&disableLink=1" scrolling="auto" frameborder="0" marginheight="0" marginwidth="0"></iframe></div>',
									#'countries'         => '<div id="widgetIframe"><iframe width="100%" height="350" src="'.t3lib_extMgm::extRelPath('piwikintegration').'piwik/index.php?module=Widgetize&action=iframe&moduleToWidgetize=GeoIP&actionToWidgetize=getGeoIPCountry&idSite='.$this->getPiwikId().'&period=week&date='.$date.'&disableLink=1" scrolling="auto" frameborder="0" marginheight="0" marginwidth="0"></iframe></div>',
									

									'visitors'          => '<div id="widgetIframe"><iframe width="100%" height="350" src="'.t3lib_extMgm::extRelPath('piwikintegration').'piwik/index.php?module=Widgetize&action=iframe&columns[]=nb_visits&moduleToWidgetize=VisitsSummary&actionToWidgetize=getEvolutionGraph&idSite='.$this->getPiwikId().'&period=day&date='.$date.'&disableLink=1" scrolling="auto" frameborder="0" marginheight="0" marginwidth="0"></iframe></div>',
									'frequencyoverview' => '<div id="widgetIframe"><iframe width="100%" height="350" src="'.t3lib_extMgm::extRelPath('piwikintegration').'piwik/index.php?module=Widgetize&action=iframe&moduleToWidgetize=VisitFrequency&actionToWidgetize=getSparklines&idSite='.$this->getPiwikId().'&period=day&date='.$date.'&disableLink=1" scrolling="auto" frameborder="0" marginheight="0" marginwidth="0"></iframe></div>',									
									'pages'             => '<div id="widgetIframe"><iframe width="100%" height="350" src="'.t3lib_extMgm::extRelPath('piwikintegration').'piwik/index.php?module=Widgetize&action=iframe&moduleToWidgetize=Actions&actionToWidgetize=getActions&idSite='.$this->getPiwikId().'&period=day&date='.$date.'&disableLink=1" scrolling="auto" frameborder="0" marginheight="0" marginwidth="0"></iframe></div>',
									'keywords'          => '<div id="widgetIframe"><iframe width="100%" height="350" src="'.t3lib_extMgm::extRelPath('piwikintegration').'piwik/index.php?module=Widgetize&action=iframe&moduleToWidgetize=Referers&actionToWidgetize=getKeywords&idSite='.$this->getPiwikId().'&period=day&date='.$date.'&disableLink=1" scrolling="auto" frameborder="0" marginheight="0" marginwidth="0"></iframe></div>',


								);
								$content.='<div class="dashboard">';
								$i        = 0;
								$cols     = 2;
								$colsData = array();
								foreach($widgets as $widgetHeader => $widget) {
									$colsData[$i].='<div class="widgetIframe">';
									$colsData[$i].='<h2>'.htmlspecialchars($LANG->getLL('dashboard_'.$widgetHeader)).'</h2>';
									$colsData[$i].=$widget;
									$colsData[$i].='</div>';
									$i++;
									if($i>=$cols) {
										$i = 0;
									}
								}
								foreach($colsData as $col) {
									$content.='<div class="dashboardcol">';
									$content.=$col;
									$content.='</div>';
								}
								$content.='</div>';
								#$this->content.=$this->doc->section($LANG->getLL('function1'),$content,0,1);
								$this->content.=$content;
							break;
							case 2:
								
								/**
								 * display iframe with piwik
								 */								 														
								$this->content.='<object id="piwik" type="text/html" data="'.t3lib_extMgm::extRelPath('piwikintegration').'piwik/index.php?module=CoreHome&action=index&period=week&date=yesterday&idSite='.$this->getPiwikId().'" width="100%" height="97%"><p>Oops! That didn´t work...</p></object>';
						break;
							case 3:
								if(t3lib_div::_POST('submit')=='Update') {
									$newCode = md5(microtime());
									$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
										'be_users',
										'uid='.intval($BE_USER->user['uid']),
										array(
											'tx_piwikintegration_api_code' => $newCode,
										)
									);
									$BE_USER->user['tx_piwikintegration_api_code'] = $newCode;
								}
								$content.='Your API Code: '.$BE_USER->user['tx_piwikintegration_api_code'].'<br />';
								$content.='Your Piwik URL: http://'.$_SERVER['SERVER_NAME'].'/'.t3lib_extMgm::siteRelPath('piwikintegration').'piwik/';
								$this->content.=$this->doc->section($LANG->getLL('function3'),$content,0,1);
								
							break;
						}
					} else {
						$content = '<p>Please choose a page from the tree on the left, which has at least a template with the constant usr_piwik_id.</p>';
						$this->content.=$this->doc->section('Tipp:',$content,0,1);
					}
				}
				protected function correctUserRights() {
					/**
					 * ensure, that user is added to database
					 */							
					if($GLOBALS['BE_USER']->user['admin']!=1) {
						$beUserName = $GLOBALS['BE_USER']->user['username'];
						$erg = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
								'*',
									'tx_piwikintegration_access',
								'login="'.$beUserName.'" AND idsite='.$this->getPiwikId(),
								'',
								'',
								'0,1'
						);
						if(count($erg)==0) {
							$GLOBALS['TYPO3_DB']->exec_INSERTquery(
								'tx_piwikintegration_access',
								array(
									'login' => $beUserName,
									'idsite'=> $this->getPiwikId(),
									'access'=> 'view'
								)
							);
						}
					}
				}
				function refreshGeoIpData() {
					tx_piwikintegration::refreshGeoIpData();
				}
				/**
				 * Create the panel of buttons for submitting the form or otherwise perform operations.
				 *
				 * @return	array	all available buttons as an assoc. array
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
					$buttons['save'] = '<input type="image" class="c-inputButton" name="submit" value="Update"' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/savedok.gif', '') . ' title="' . $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:rm.saveDoc', 1) . '" />';


						// Shortcut
					if ($GLOBALS['BE_USER']->mayMakeShortcut())	{
						$buttons['shortcut'] = $this->doc->makeShortcutIcon('', 'function', $this->MCONF['name']);
					}

					return $buttons;
				}
				function getPiwikId() {
					if(isset($this->piwik_id)) {
						return $this->piwik_id;
					}
					$template_uid = 0;
					$pageId = $this->pageinfo['uid'];
					$tmpl = t3lib_div::makeInstance("t3lib_tsparser_ext");	// Defined global here!
					$tmpl->tt_track = 0;	// Do not log time-performance information
					$tmpl->init();
			
					$tplRow = $tmpl->ext_getFirstTemplate($pageId,$template_uid);
					if (is_array($tplRow) || 1)	{	// IF there was a template...
							// Gets the rootLine
						$sys_page = t3lib_div::makeInstance("t3lib_pageSelect");
						$rootLine = $sys_page->getRootLine($pageId);
						$tmpl->runThroughTemplates($rootLine,$template_uid);	// This generates the constants/config + hierarchy info for the template.
						$theConstants = $tmpl->generateConfig_constants();	// The editable constants are returned in an array.
						$tmpl->ext_categorizeEditableConstants($theConstants);	// The returned constants are sorted in categories, that goes into the $tmpl->categories array
						$tmpl->ext_regObjectPositions($tplRow["constants"]);		// This array will contain key=[expanded constantname], value=linenumber in template. (after edit_divider, if any)
					}
					if($tmpl->setup['constants']['usr_piwik_id']) {
						$id = intval($tmpl->setup['constants']['usr_piwik_id']);
					} elseif ($tmpl->setup['constants']['usr_name']) {
						$id =  intval($tmpl->setup['constants']['usr_name']);
					} else {
						$id = 0;
					}
					$this->piwik_id = $id;
					return $this->piwik_id;
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
