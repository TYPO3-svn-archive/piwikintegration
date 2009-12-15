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
/**
 * @author  Kay Strobach <typo3@kay-strobach.de>
 * @link http://kay-strobach.de
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * 
 * @package Piwik_TYPO3Menu
 */
 
$(document).ready(function(){
		
	//create ul for menu
		$('#topBars').prepend('<div><ul class="horizontalmenu"></ul></div>');
	
	//fetch the Piwik logo
		$('.horizontalmenu').prepend(
			$('#logo a').children().wrapAll('<a id="logo"></a>').parent().wrapAll('<li></li>').parent()
		);	
	//fetch the topbar	
		$('.horizontalmenu li').append(
			$('.topBarElem:has(a)').children().wrap('<li></li>').parent().wrapAll('<ul></ul>').parent()
		);
	//fetch the widgetbar
		$('.horizontalmenu').append(
			$('ul.nav').children()
		);
	//add date selector
		$('.horizontalmenu').append(
			$('#date').wrap('<a></a>').parent().wrapAll('<li id="dateselector"></li>').parent()
		);
		$('#date').parent().parent().append(
			$('#periodString #periods a').wrap('<li></li>').parent().wrapAll('<ul></ul>').parent()
		);
		$('#date').parent().next().prepend('<li><span id="typo3datepicker"></span></li>');
		$('#date img').remove();
		$("#datepicker").remove();
		$('#periods').remove();
	//add new datepicker based on the piwik datepicker to ensure that it is visible
		$('#typo3datepicker').datepicker({
			onSelect: updateDate,
			showOtherMonths: false,
			dateFormat: 'yy-mm-dd',
			firstDay: 1,
			minDate: new Date(piwik.minDateYear, piwik.minDateMonth - 1, piwik.minDateDay),
			maxDate: new Date(piwik.maxDateYear, piwik.maxDateMonth - 1, piwik.maxDateDay),
			prevText: "",
			nextText: "",
			currentText: "",
			beforeShowDay: highlightCurrentPeriod,
			defaultDate: currentDate,
			changeMonth: true,
			changeYear: true,
			// jquery-ui-i18n 1.7.2 lacks some translations, so we use our own
			dayNamesMin: [
				_pk_translate('CoreHome_DaySu_js'),
				_pk_translate('CoreHome_DayMo_js'),
				_pk_translate('CoreHome_DayTu_js'),
				_pk_translate('CoreHome_DayWe_js'),
				_pk_translate('CoreHome_DayTh_js'),
				_pk_translate('CoreHome_DayFr_js'),
				_pk_translate('CoreHome_DaySa_js')],
			dayNamesShort: [
				_pk_translate('CoreHome_ShortDay_1_js'),
				_pk_translate('CoreHome_ShortDay_2_js'),
				_pk_translate('CoreHome_ShortDay_3_js'),
				_pk_translate('CoreHome_ShortDay_4_js'),
				_pk_translate('CoreHome_ShortDay_5_js'),
				_pk_translate('CoreHome_ShortDay_6_js'),
				_pk_translate('CoreHome_ShortDay_7_js')],
			dayNames: [
				_pk_translate('CoreHome_LongDay_1_js'),
				_pk_translate('CoreHome_LongDay_2_js'),
				_pk_translate('CoreHome_LongDay_3_js'),
				_pk_translate('CoreHome_LongDay_4_js'),
				_pk_translate('CoreHome_LongDay_5_js'),
				_pk_translate('CoreHome_LongDay_6_js'),
				_pk_translate('CoreHome_LongDay_7_js')],
			monthNamesShort: [
				_pk_translate('CoreHome_ShortMonth_1_js'),
				_pk_translate('CoreHome_ShortMonth_2_js'),
				_pk_translate('CoreHome_ShortMonth_3_js'),
				_pk_translate('CoreHome_ShortMonth_4_js'),
				_pk_translate('CoreHome_ShortMonth_5_js'),
				_pk_translate('CoreHome_ShortMonth_6_js'),
				_pk_translate('CoreHome_ShortMonth_7_js'),
				_pk_translate('CoreHome_ShortMonth_8_js'),
				_pk_translate('CoreHome_ShortMonth_9_js'),
				_pk_translate('CoreHome_ShortMonth_10_js'),
				_pk_translate('CoreHome_ShortMonth_11_js'),
				_pk_translate('CoreHome_ShortMonth_12_js')],
			monthNames: [
				_pk_translate('CoreHome_MonthJanuary_js'),
				_pk_translate('CoreHome_MonthFebruary_js'),
				_pk_translate('CoreHome_MonthMarch_js'),
				_pk_translate('CoreHome_MonthApril_js'),
				_pk_translate('CoreHome_MonthMay_js'),
				_pk_translate('CoreHome_MonthJune_js'),
				_pk_translate('CoreHome_MonthJuly_js'),
				_pk_translate('CoreHome_MonthAugust_js'),
				_pk_translate('CoreHome_MonthSeptember_js'),
				_pk_translate('CoreHome_MonthOctober_js'),
				_pk_translate('CoreHome_MonthNovember_js'),
				_pk_translate('CoreHome_MonthDecember_js')]
		});
	//rebuild admin menu
		ul = $('<ul class="horizontalmenu"><li><a></a></li></ul>');
		//add username as root
		ul.children('li').children('a').append(
			$('#topRightBar strong').text()
		)
		//handle logout
		ul.children('li').append(
			$('#topRightBar a:last-child').wrap('<li></li>').parent().wrapAll('<ul></ul>').parent()
		);
		//handle other stuff
		ul.children('li').children('ul').append(
			$('#topRightBar a').wrap('<li></li>').parent()
		);
		$('#topRightBar').html(ul);
		$('#topRightBar ul li ul').css('left',0);
	//make menu working
		//unbind old menu
			$('ul.horizontalmenu li').unbind('mouseenter');
			$('ul.horizontalmenu li').unbind('mouseleave');
		//open event
			$('ul.horizontalmenu li').bind('mouseenter',function(e) {
				pos = $(this).offset();
				$(this).children('ul').css('left'       ,pos['left'])
				$(this).children('ul').css('top'        ,pos['top']+$(this).height());
				$(this).children('ul').css('visibility' ,'visible');
				$(this).children('ul').slideDown(120);
			});
		//close event
			$('ul.horizontalmenu li').bind('mouseleave',function(e) {
				$(this).children('ul').slideUp(120);
			});
			$('ul.horizontalmenu li').bind('click',function(e) {
				$('ul.horizontalmenu li').removeClass('sfHover');
				$(this).addClass('sfHover');
				$('ul.horizontalmenu li:has(.sfHover)').addClass('sfHover');
				modifyHeader();
			});
		//hide items
			$('ul.horizontalmenu li ul').hide();

	//remove now senseless elements
		$('.nav').remove();
		$('#topLeftBar').html(
			$('#languageSelection')
		);
		$('#topRightBar a:first-child').width(80);
		$('.sf-sub-indicator').remove();
	//add header information
		modifyHeader();
});

function modifyHeader() {
	$('#header').children().remove();
	$('#header').append(
		$('ul.horizontalmenu li.sfHover').clone().children().wrapAll('<h2></h2>').parent()
	);
	$('#header a').after(' <span>&gt;</span> ');
	$('#header ul').remove();
	$('#header h2 :last-child').remove();
}