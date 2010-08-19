var tabs = new Ext.TabPanel({
	renderTo:Ext.getBody(),
	activeTab: 0,
	defaults:{
		autoHeight: true
	},
	items:[{	
		autoLoad:'http://heise.de',
		title: '###1###'
		
	},{
		html:'<iframe src="../typo3conf/piwik/piwik/" width="100%" height="100%" frameborder="0"></iframe>',
		title: '###2###',
		bodyStyle:'padding:0;margin:0'
	},{
		autoLoad:'/typo3conf/piwik/piwik/',
		title: '###3###'
	}
	]
});