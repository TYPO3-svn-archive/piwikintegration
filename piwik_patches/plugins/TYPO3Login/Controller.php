<?php
class Piwik_TYPO3Login_Controller extends Piwik_Controller 
{
	function index()
	{
		header('Location: index.php');
	}
	function logout() {
		header('Location: index.php');
	}
}
?>
