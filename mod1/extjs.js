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