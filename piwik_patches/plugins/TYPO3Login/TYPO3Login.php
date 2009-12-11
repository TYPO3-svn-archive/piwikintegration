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
 * @package Piwik_TYPO3Login
 */
 
require "TYPO3Login/Auth.php";

class Piwik_TYPO3Login extends Piwik_Plugin
{
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

	function getListHooksRegistered()
	{
		$hooks = array(
			'FrontController.initAuthenticationObject'	=> 'initAuthenticationObject',
			);
		return $hooks;
	}
	
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
?>
