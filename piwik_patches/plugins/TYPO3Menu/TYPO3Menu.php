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


class Piwik_TYPO3Menu extends Piwik_Plugin
{
	public function getInformation()
	{
		return array(
			'name' => 'TYPO3NMenu',
			'description' => 'Style Piwik TYPO3 Link.',
			'author' => 'Kay Strobach',
			'homepage' => 'http://kay-strobach.de/',
		    'version' => '0.1',
			);
	}

	public function getListHooksRegistered()
	{
		return array( 
			'template_js_import' => 'js',
			'template_css_import' => 'css',
		);
	}

	function js()
	{
		if($_GET['module']=='CoreHome') {
			echo '<script type="text/javascript" src="plugins/TYPO3Menu/js/main.js"></script>';
		}
	}

	function css()
	{
		echo "<link rel='stylesheet' type='text/css' href='plugins/TYPO3Menu/css/main.css'>\n";
		echo "<link rel='stylesheet' type='text/css' href='plugins/TYPO3Menu/css/typo3.css'>\n";
	}
}
?>
