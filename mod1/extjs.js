/***************************************************************
*  Copyright notice
*
*  (c) 2010 Kay Strobach (typo3@kay-strobach.de)
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * mod1/extjs.js
 *
 * backendviewport
 *
 * $Id$
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */
 
var piwikViewport = new Ext.Viewport({
	layout:'border',
	renderTo:Ext.getBody(),
	items:[{
		title:'BLub',
		region:'center',
		xtype:'tabpanel',
		activeTab: 0,
		items:[{
			html:'<iframe src="../typo3conf/piwik/piwik/" width="100%" height="100%" frameborder="0"></iframe>',
			title: '###2###',
			bodyStyle:'padding:0;margin:0',
			iconCls: 'x-piwikintegration-btn-piwik'
		},{
			//autoLoad:'mod.php?M=web_txpiwikintegrationM1&id=1&SET[function]=3',
			html:'###piwikAPI###',
			title: '###3###',
			iconCls: 'x-piwikintegration-btn-settings',
			autoScroll:true,
			tbar:[{
				text:'API',
				iconCls:'x-piwikintegration-btn-docs-api-1',
				handler:function() {
					window.open('http://dev.piwik.org/trac/wiki/API/Reference');
				}
			},'-',{
				text:'Tracker',
				iconCls:'x-piwikintegration-btn-docs-api-2',
				handler:function() {
					window.open('http://piwik.org/docs/javascript-tracking/');
				}
			},{
				text:'Goaltracker',
				iconCls:'x-piwikintegration-btn-docs-api-3',
				handler:function() {
					window.open('http://piwik.org/docs/tracking-goals-web-analytics/');
				}
			},{
				text:'Advanced Tracking',
				iconCls:'x-piwikintegration-btn-docs-api-4',
				handler:function() {
					window.open('http://piwik.org/docs/tracking-api/');
				}
			}]
		}]
	}]
});
Ext.get('typo3-docbody').remove(); 