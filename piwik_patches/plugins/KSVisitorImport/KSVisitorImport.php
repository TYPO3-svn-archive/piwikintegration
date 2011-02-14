<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @version $Id: VisitorGenerator.php 3300 2010-11-07 01:33:12Z vipsoft $
 *
 * @category Piwik_Plugins
 * @package Piwik_VisitorGenerator
 */

/**
 * 
 * @package Piwik_VisitorGenerator
 */
class Piwik_KSVisitorImport extends Piwik_Plugin {

	public function getInformation() {
		$info = array(
				'description' => 'Imports Alternative Logfiles',
				'author' => 'Kay Strobach',
				'author_homepage' => 'http://www.kay-strobach.de/',
				'version' => '1.0.0',
		);
		return $info;
	}

	public function getListHooksRegistered() {
		return array(
				'AdminMenu.add' => 'addMenu',
		);
	}

	public function addMenu() {
		Piwik_AddAdminMenu(
				'Visitor Import',
				array('module' => 'KSVisitorImport', 'action' => 'index'),
				Piwik::isUserIsSuperUser(),
				$order = 11
		);
	}
}
