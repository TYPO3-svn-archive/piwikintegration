$(document).ready(function(){
		
	//create ul for menu
		$('#topBars').prepend('<div><ul id="typo3menu"></ul></div>');
	
	//fetch the Piwik logo
		$('#typo3menu').prepend(
			$('#logo a').children().wrapAll('<a id="logo"></a>').parent().wrapAll('<li></li>').parent()
		);	
	//fetch the topbar	
		$('#typo3menu li').append(
			$('.topBarElem:has(a)').children().wrap('<li></li>').parent().wrapAll('<ul></ul>').parent()
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
		$('ul#typo3menu li').bind('click',function(e) {
			$('ul#typo3menu li').removeClass('sfHover');
			$(this).addClass('sfHover');
			$('ul#typo3menu li:has(.sfHover)').addClass('sfHover');
			modifyHeader();
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
		$('.sf-sub-indicator').remove();
	//add header information
		modifyHeader();
});

function modifyHeader() {
	$('#header').children().remove();
	$('#header').append(
		$('ul#typo3menu li.sfHover').clone().children().wrapAll('<h2></h2>').parent()
	);
	$('#header a').after(' <span>&gt;</span> ');
	$('#header ul').remove();
	$('#header h2 :last-child').remove();
}