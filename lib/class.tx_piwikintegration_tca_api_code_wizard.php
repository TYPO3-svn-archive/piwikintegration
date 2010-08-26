<?php

class tx_piwikintegration_tca_api_code_wizard {
	function main(&$PA, &$fobj) {
		  $onClick = 'date = new Date(); document.'.$PA['formName'].'[\''.$PA['itemName'].'\'].value=MD5(date.getTime()+document.location.href);'
			.implode('',$PA['fieldChangeFunc'])    // Necessary to tell TCEforms that the value is updated.
			.'return false;';		
		return '<a href="" onClick="'.$onClick.'" title="refresh or set the api key, unique is evaluated on the server"><img '.t3lib_iconWorks::skinImg('gfx/','import_update.gif').' alt="refresh or set the api key, unique is evaluated on the server"></a>';
	}
}