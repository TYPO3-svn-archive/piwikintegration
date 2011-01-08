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
 *   The GNU General Public License can be found at
 *   http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
	require_once PIWIK_INCLUDE_PATH.'/plugins/UsersManager/API.php';
/**
 *  Fix some problems with external DB usage  
 */ 
/**
 * for version 3.0.9
 */ 
	include      PIWIK_INCLUDE_PATH.'/../../localconf.php';
	define('TYPO3DB',$typo_db);
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
		/***********************************************************************
		 * read environment and generate prefixes
		 */		 		
			$t3database    = Zend_Registry::get('config')->database->t3dbname;
			$piwikdatabase = Zend_Registry::get('config')->database->dbname;
			if($t3database == $piwikdatabase) {
				$t3database = '';
			}
			if($t3database != '') {
				$t3database = '`'.$t3database.'`.';
			}

		/***********************************************************************
		 * authenticate against the piwik configuration file for emergency access or installer or cronjob!
		 */		 		
			$rootLogin = Zend_Registry::get('config')->superuser->login;
			$rootPassword = Zend_Registry::get('config')->superuser->password;
			$rootToken = Piwik_UsersManager_API::getTokenAuth($rootLogin, $rootPassword);
	
			if($this->login == $rootLogin
				&& $this->token_auth == $rootToken)
			{
				return new Piwik_Auth_Result(Piwik_Auth_Result::SUCCESS_SUPERUSER_AUTH_CODE, $this->login, $this->token_auth );
			}
	
			if($this->token_auth === $rootToken)
			{
				return new Piwik_Auth_Result(Piwik_Auth_Result::SUCCESS_SUPERUSER_AUTH_CODE, $rootLogin, $rootToken );
			}
		/***********************************************************************
		 * Handle login types
		 */
			$beUserId = false;
			//catch normal logins (login form)
			if($this->token_auth && $this->token_auth!='anonymous') {
				if($this->login) {
					$beUserId = Zend_Registry::get('db')->fetchOne(
								'SELECT uid FROM '.$t3database.'`be_users` WHERE tx_piwikintegration_api_code = ?',
								array($this->token_auth)
					);
				}
			//catch typo3 logins
			} elseif(array_key_exists('be_typo_user',$_COOKIE)) {
				$beUserCookie = $_COOKIE['be_typo_user'];
				$beUserId = Zend_Registry::get('db')->fetchOne(
							'SELECT ses_userid FROM '.$t3database.'`be_sessions` WHERE ses_id = ?',
							array($beUserCookie)
				);
			//catch apikey logins
			} elseif((in_array('token_auth',$_REQUEST)) &&($_REQUEST['token_auth']!='')) {
				// fetch UserId, if token is set
				$beUserId = Zend_Registry::get('db')->fetchOne(
							'SELECT uid FROM '.$t3database.'`be_users` WHERE tx_piwikintegration_api_code = ?',
							array($_REQUEST['token_auth'])
				);
			} else {
				$beUserId=false;
			}
		/***********************************************************************
		 * init user from db
		 */		 		
			if($beUserId!==false) {
				// getUserName
				$beUserName = Zend_Registry::get('db')->fetchOne(
							'SELECT username FROM '.$t3database.'`be_users` WHERE uid = ?',
							array($beUserId)
				);
				// get isAdmin
				$beUserIsAdmin = Zend_Registry::get('db')->fetchOne(
							'SELECT admin FROM '.$t3database.'`be_users` WHERE uid = ?',
							array($beUserId)
				);
				// is superuser?
				if($beUserIsAdmin ==1) {
					return new Piwik_Auth_Result(Piwik_Auth_Result::SUCCESS_SUPERUSER_AUTH_CODE, $beUserName, NULL );
				}
				//normal user?
				return new Piwik_Auth_Result(Piwik_Auth_Result::SUCCESS, $beUserName, NULL );
			}

		/***********************************************************************
		 * authenticate anonymous user
		 */
			if($this->login == 'anonymous') {
				return new Piwik_Auth_Result(Piwik_Auth_Result::SUCCESS, 'anonymous', NULL );
			}		 		
		/***********************************************************************
		 * no valid user
		 */		 		
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
	
	static function getTokenAuth($login, $md5Password) {
		$token = Zend_Registry::get('db')->fetchOne(
						'SELECT tx_piwikintegration_api_code FROM `be_users` WHERE username = ?',
						array($login)
			);
		if(md5(substr($token,0,6))==$md5Password) {
			return $token;
		} else {
			return '';
		}
	}
}
