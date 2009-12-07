$(document).ready(function(){
		
	//create ul for menu
		$('#topBars').append('<div><ul id="typo3menu"><li><a href="#">General</a></li></ul></div>');
		
	//fetch the topbar	
		$('#typo3menu li:first-child').append(
			$('.topBarElem:has(a)').children().wrap('<li></li>').parent().wrapAll('<ul></ul>').parent()
		);
	//fetch the Piwik logo
		$('#typo3menu').prepend(
			$('#logo').wrap('<li></li>').parent()
		);
	
	//fetch the widgetbar
		$('#typo3menu').append(
			$('ul.nav').children()
		);
	
	//make menu
		//unbind old menu
		$('ul#typo3menu li').unbind('mouseenter');
		$('ul#typo3menu li').unbind('mouseleave');
		
		//open event
		$('ul#typo3menu li').bind('mouseenter',function(e) {
			pos = $(this).offset();
			$(this).children('ul').css('left'       ,pos['left'])
			$(this).children('ul').css('top'        ,pos['top']+$(this).height());
			$(this).children('ul').css('visibility' ,'visible');
			$(this).children('ul').slideDown('fast');
		});
		//close event
		$('ul#typo3menu li').bind('mouseleave',function(e) {
			$(this).children('ul').slideUp('fast');
		});
	
		//hide items
			$('ul#typo3menu li ul').hide();
	//add date selector
		$('#typo3menu').append(
				$('#periodString').wrap('<li></li>').parent()
		);
	//remove now senseless elements
		$('.nav').remove();
		//$('#topLeftBar').remove();
		$('#header').remove();
	
});
