<?php
	/**
	* Piwik - Open source web analytics
	* 
	* @link http://piwik.org
    * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
    * @version $Id: ExamplePlugin.php 838 2008-12-17 17:26:15Z matt $
	* 
	* @package Piwik_TYPO3Login
	*/
	/**
	 * need to set database settings before creating database object. 
	 */	 	

require "TYPO3Login/Auth.php";

class Piwik_TYPO3Login extends Piwik_Plugin
{
	public function getInformation()
	{
		return array(
			'name' => 'TYPO3Login',
			'description' => 'HTTP_AUTH Login plugin. It uses the HTTP-based auth to grant access to users on piwik.',
			'author' => 'Kay Strobach',
			'homepage' => 'http://kay-strobach.de/',
		    'version' => '0.1',
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
