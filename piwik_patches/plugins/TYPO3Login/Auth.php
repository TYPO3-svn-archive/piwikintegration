<?php
require_once "UsersManager/API.php";
/**
 * @package Piwik
 */
class Piwik_TYPO3Login_Auth implements Piwik_Auth
{
	protected $login = null;
	protected $token_auth = null;
	
	public function getName()
	{
	        return 'TYPO3Login';
	}
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

	public function setLogin($login)
	{
		$this->login = $login;
	}
	
	public function setTokenAuth($token_auth)
	{
		$this->token_auth = $token_auth;
	}
}
?>
