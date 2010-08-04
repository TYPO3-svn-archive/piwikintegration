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
require PIWIK_INCLUDE_PATH.'/plugins/TYPO3Login/Auth.php';

/**
 * Add widgets
 */ 
class Piwik_TYPO3Login_Controller extends Piwik_Controller {
	function rssAllNews() {
		$rss = new Piwik_ExampleRssWidget_Rss('http://news.typo3.org/rss.xml');
		$rss->showDescription(true);
		echo $rss->get();
	}
	function rssAllTeamNews() {
		$rss = new Piwik_ExampleRssWidget_Rss('http://news.typo3.org/news/teams/rss.xml');
		$rss->showDescription(true);
		echo $rss->get();
	}
	function rssCommunity() {
		$rss = new Piwik_ExampleRssWidget_Rss('http://news.typo3.org/news/community/rss.xml');
		$rss->showDescription(true);
		echo $rss->get();
	}
	function rssContentRenderingGroup() {
		$rss = new Piwik_ExampleRssWidget_Rss('http://news.typo3.org/news/teams/content-rendering-group/rss.xml');
		$rss->showDescription(true);
		echo $rss->get();
	}
	function rssDevelopment() {
		$rss = new Piwik_ExampleRssWidget_Rss('http://news.typo3.org/news/development/rss.xml');
		$rss->showDescription(true);
		echo $rss->get();
	}
	function rssExtensions() {
		$rss = new Piwik_ExampleRssWidget_Rss('http://news.typo3.org/news/extensions/rss.xml');
		$rss->showDescription(true);
		echo $rss->get();
	}
	function rssSecurity() {
		$rss = new Piwik_ExampleRssWidget_Rss('http://news.typo3.org/news/teams/security/rss.xml');
		$rss->showDescription(true);
		echo $rss->get();
	}
	function rssTypo3Org() {
		$rss = new Piwik_ExampleRssWidget_Rss('http://news.typo3.org/news/teams/typo3org/rss.xml');
		$rss->showDescription(true);
		echo $rss->get();
	}
	function rssTypo3Associaton() {
		$rss = new Piwik_ExampleRssWidget_Rss('http://news.typo3.org/news/typo3-association/rss.xml');
		$rss->showDescription(true);
		echo $rss->get();
	}
}
 
Piwik_AddWidget('TYPO3 Widgets', 'TYPO3 All News',                'TYPO3Login', 'rssAllNews');
Piwik_AddWidget('TYPO3 Widgets', 'TYPO3 All Team News',           'TYPO3Login', 'rssAllTeamNews');
Piwik_AddWidget('TYPO3 Widgets', 'TYPO3 Community',               'TYPO3Login', 'rssCommunity');
Piwik_AddWidget('TYPO3 Widgets', 'TYPO3 Content Rendering Group', 'TYPO3Login', 'rssContentRenderingGroup');
Piwik_AddWidget('TYPO3 Widgets', 'TYPO3 Development',             'TYPO3Login', 'rssDevelopment');
Piwik_AddWidget('TYPO3 Widgets', 'TYPO3 Extensions',              'TYPO3Login', 'rssExtensions');
Piwik_AddWidget('TYPO3 Widgets', 'TYPO3 Security',                'TYPO3Login', 'rssSecurity');
Piwik_AddWidget('TYPO3 Widgets', 'TYPO3.Org',                     'TYPO3Login', 'rssTypo3Org');
Piwik_AddWidget('TYPO3 Widgets', 'TYPO3 Associaton',              'TYPO3Login', 'rssTypo3Associaton');


/**
 * Class for authentification plugin
 * 
 * @author  Kay Strobach <typo3@kay-strobach.de>
 * @link http://kay-strobach.de
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 *
 * @package Piwik_TYPO3Login
 */
class Piwik_TYPO3Login extends Piwik_Plugin
{
	/**
	 * get extension information
	 *
	 * @return	array		with information
	 */
	public function getInformation()
	{
		include(PIWIK_INCLUDE_PATH.'/piwikintegration.php');
		return array(
			'name' => 'TYPO3Login',
			'description' => 'TYPO3 Auth Login plugin. It uses the TYPO3 session and permission data to grant access to users on piwik.',
			'author' => 'Kay Strobach',
			'homepage' => 'http://kay-strobach.de/',
		    'version' => $piwikPatchVersion,
			);
	}

	/**
	 * returns registered hooks
	 *
	 * @return	array		array of hooks
	 */
	function getListHooksRegistered()
	{
		$hooks = array(
			'FrontController.initAuthenticationObject'	=> 'initAuthenticationObject',
			);
		return $hooks;
	}

	/**
	 * init the authentification object
	 *
	 * @param	mixed		$notification: some data from the api, which is not needed
	 * @return	void
	 */
	function initAuthenticationObject($notification)
	{
		$auth = new Piwik_TYPO3Login_Auth();
     	Zend_Registry::set('auth', $auth);

     			$action = Piwik::getAction();
		if(Piwik::getModule() === 'API'
			&& (empty($action) || $action == 'index'))
		{
			return;
		}

		$authCookieName = Zend_Registry::get('config')->General->login_cookie_name;
		$authCookieExpiry = time() + Zend_Registry::get('config')->General->login_cookie_expire;
		$authCookie = new Piwik_Cookie($authCookieName, $authCookieExpiry);
		$defaultLogin = 'anonymous';
		$defaultTokenAuth = 'anonymous';
		if($authCookie->isCookieFound())
		{
			$defaultLogin = $authCookie->get('login');
			$defaultTokenAuth = $authCookie->get('token_auth');
		}
		$auth->setLogin($defaultLogin);
		$auth->setTokenAuth($defaultTokenAuth);
	}

}
//XClass to avoid errors in extmanager of TYPO3 - senseless so far
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/piwik_patches/plugins/TYPO3Login/TYPO3Login.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/piwik_patches/plugins/TYPO3Login/TYPO3Login.php']);
}
?>