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
require_once "UsersManager/API.php";
/**
 * Provide authentification service against TYPO3 for piwik
 *
 * @author  Kay Strobach <typo3@kay-strobach.de>
 * @link http://kay-strobach.de
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @package Piwik_TYPO3Login
 */
class Piwik_TYPO3Login_Auth implements Piwik_Auth
{
	protected $login = null;
	protected $token_auth = null;

	/**
	 * returns extension name
	 *
	 * @return	string		extensionname
	 */
	public function getName()
	{
	        return 'TYPO3Login';
	}

	/**
	 * authenticate the user
	 *
	 * @return	object		Piwik_Auth_Result
	 */
	public function authenticate()
	{
		$rootLogin = Zend_Registry::get('config')->superuser->login;
		/**
		 * TYPO3 cookie
		 */
		if(array_key_exists('be_typo_user',$_COOKIE)) {
			$beUserCookie = $_COOKIE['be_typo_user'];
		} else {
			$beUserCookie  = false;
		}
		if($beUserCookie!==false) {
			// fetch UserId, if cookie is set
			$beUserId = Zend_Registry::get('db')->fetchOne(
						'SELECT ses_userid FROM be_sessions WHERE ses_id = ?',
						array($beUserCookie)
			);
		} elseif((in_array('token_auth',$_REQUEST)) &&($_REQUEST['token_auth']!='')) {
			// fetch UserId, if token is set
			$beUserId = Zend_Registry::get('db')->fetchOne(
						'SELECT uid FROM be_users WHERE tx_piwikintegration_api_code = ?',
						array($_REQUEST['token_auth'])
			);
		} else {
			$beUserId=false;
		}
		if($beUserId!==false) {
			// getUserName
			$beUserName = Zend_Registry::get('db')->fetchOne(
						'SELECT username FROM be_users WHERE uid = ?',
						array($beUserId)
			);
			// get isAdmin
			$beUserIsAdmin = Zend_Registry::get('db')->fetchOne(
						'SELECT admin FROM be_users WHERE uid = ?',
						array($beUserId)
			);
			// is superuser?
			if($beUserIsAdmin ==1) {
				return new Piwik_Auth_Result(Piwik_Auth_Result::SUCCESS_SUPERUSER_AUTH_CODE, $beUserName, NULL );
			}
			//normal user?
			return new Piwik_Auth_Result(Piwik_Auth_Result::SUCCESS, $beUserName, NULL );
		}
		if($this->login == 'anonymous') {
			return new Piwik_Auth_Result(Piwik_Auth_Result::SUCCESS, 'anonymous', NULL );
		}

		// no valid user
		return new Piwik_Auth_Result( Piwik_Auth_Result::FAILURE, $this->login, $this->token_auth );
	}

	/**
	 * set login name of the current session
	 *
	 * @param	string		$login: login username
	 * @return	void
	 */
	public function setLogin($login)
	{
		$this->login = $login;
	}

	/**
	 * set authentification token
	 *
	 * @param	string		$token_auth: piwik token
	 * @return	void
	 */
	public function setTokenAuth($token_auth)
	{
		$this->token_auth = $token_auth;
	}
}
//XClass to avoid errors in extmanager of TYPO3 - senseless so far
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/piwik_patches/plugins/TYPO3Login/Auth.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/piwik_patches/plugins/TYPO3Login/Auth.php']);
}

?>