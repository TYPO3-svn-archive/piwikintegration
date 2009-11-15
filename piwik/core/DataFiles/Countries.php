<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: Countries.php 444 2008-04-11 13:38:22Z johmathe $
 * 
 * @package Piwik_UserCountry
 */

/**
 * Country code and continent database.
 * If you want to add a new entry, please email us at hello at piwik.org
 * 
 */
if(!isset($GLOBALS['Piwik_CountryList']))
{
	// Reference: ISO 3166-1 alpha-2
	$GLOBALS['Piwik_CountryList'] = array(
			'xx' => array('unk'),
			'ad' => array('eur'),
			'ae' => array('asi'),
			'af' => array('asi'),
			'ag' => array('ams'),
			'ai' => array('ams'),
			'al' => array('eur'),
			'am' => array('asi'),
			'an' => array('ams'),
			'ao' => array('afr'),
			'aq' => array('oce'),
			'ar' => array('ams'),
			'as' => array('oce'),
			'at' => array('eur'),
			'au' => array('oce'),
			'aw' => array('ams'),
			'ax' => array('eur'),
			'az' => array('asi'),
			'ba' => array('eur'),
			'bb' => array('ams'),
			'bd' => array('asi'),
			'be' => array('eur'),
			'bf' => array('afr'),
			'bg' => array('eur'),
			'bh' => array('asi'),
			'bi' => array('afr'),
			'bj' => array('afr'),
			'bl' => array('ams'),
			'bm' => array('ams'),
			'bn' => array('asi'),
			'bo' => array('ams'),
			'br' => array('ams'),
			'bs' => array('ams'),
			'bt' => array('asi'),
			'bv' => array('oce'),
			'bw' => array('afr'),
			'by' => array('eur'),
			'bz' => array('ams'),
			'ca' => array('amn'),
			'cc' => array('oce'),
			'cd' => array('afr'),
			'cf' => array('afr'),
			'cg' => array('afr'),
			'ch' => array('eur'),
			'ci' => array('afr'),
			'ck' => array('asi'),
			'cl' => array('ams'),
			'cm' => array('afr'),
			'cn' => array('asi'),
			'co' => array('ams'),
			'cr' => array('ams'),
			'cu' => array('ams'),
			'cv' => array('afr'),
			'cx' => array('oce'),
			'cy' => array('eur'),
			'cz' => array('eur'),
			'de' => array('eur'),
			'dj' => array('afr'),
			'dk' => array('eur'),
			'dm' => array('ams'),
			'do' => array('ams'),
			'dz' => array('afr'),
			'ec' => array('ams'),
			'ee' => array('eur'),
			'eg' => array('afr'),
			'eh' => array('afr'),
			'er' => array('afr'),
			'es' => array('eur'),
			'et' => array('afr'),
			'fi' => array('eur'),
			'fj' => array('oce'),
			'fk' => array('ams'),
			'fm' => array('oce'),
			'fo' => array('eur'),
			'fr' => array('eur'),
			'ga' => array('afr'),
			'gb' => array('eur'),
			'gd' => array('ams'),
			'ge' => array('asi'),
			'gf' => array('ams'),
			'gg' => array('eur'),
			'gh' => array('afr'),
			'gi' => array('afr'),
			'gl' => array('amn'),
			'gm' => array('afr'),
			'gn' => array('afr'),
			'gp' => array('ams'),
			'gq' => array('afr'),
			'gr' => array('eur'),
			'gs' => array('eur'),
			'gt' => array('ams'),
			'gu' => array('asi'),
			'gw' => array('afr'),
			'gy' => array('ams'),
			'hk' => array('asi'),
			'hm' => array('oce'),
			'hn' => array('ams'),
			'hr' => array('eur'),
			'ht' => array('ams'),
			'hu' => array('eur'),
			'id' => array('asi'),
			'ie' => array('eur'),
			'il' => array('asi'),
			'im' => array('eur'),
			'in' => array('asi'),
			'io' => array('asi'),
			'iq' => array('asi'),
			'ir' => array('asi'),
			'is' => array('eur'),
			'it' => array('eur'),
			'je' => array('eur'),
			'jm' => array('ams'),
			'jo' => array('asi'),
			'jp' => array('asi'),
			'ke' => array('afr'),
			'kg' => array('asi'),
			'kh' => array('asi'),
			'ki' => array('oce'),
			'km' => array('afr'),
			'kn' => array('ams'),
			'kp' => array('asi'),
			'kr' => array('asi'),
			'kw' => array('asi'),
			'ky' => array('ams'),
			'kz' => array('asi'),
			'la' => array('asi'),
			'lb' => array('asi'),
			'lc' => array('ams'),
			'li' => array('eur'),
			'lk' => array('asi'),
			'lr' => array('afr'),
			'ls' => array('afr'),
			'lt' => array('eur'),
			'lu' => array('eur'),
			'lv' => array('eur'),
			'ly' => array('afr'),
			'ma' => array('afr'),
			'mc' => array('eur'),
			'md' => array('eur'),
			'me' => array('eur'),
			'mf' => array('ams'),
			'mg' => array('afr'),
			'mh' => array('oce'),
			'mk' => array('eur'),
			'ml' => array('afr'),
			'mm' => array('asi'),
			'mn' => array('asi'),
			'mo' => array('asi'),
			'mp' => array('asi'),
			'mq' => array('ams'),
			'mr' => array('afr'),
			'ms' => array('ams'),
			'mt' => array('eur'),
			'mu' => array('afr'),
			'mv' => array('asi'),
			'mw' => array('afr'),
			'mx' => array('ams'),
			'my' => array('asi'),
			'mz' => array('afr'),
			'na' => array('afr'),
			'nc' => array('oce'),
			'ne' => array('afr'),
			'nf' => array('oce'),
			'ng' => array('afr'),
			'ni' => array('ams'),
			'nl' => array('eur'),
			'no' => array('eur'),
			'np' => array('asi'),
			'nr' => array('oce'),
			'nu' => array('oce'),
			'nz' => array('oce'),
			'om' => array('asi'),
			'pa' => array('ams'),
			'pe' => array('ams'),
			'pf' => array('oce'),
			'pg' => array('oce'),
			'ph' => array('asi'),
			'pk' => array('asi'),
			'pl' => array('eur'),
			'pm' => array('amn'),
			'pn' => array('oce'),
			'pr' => array('ams'),
			'ps' => array('asi'),
			'pt' => array('eur'),
			'pw' => array('oce'),
			'py' => array('ams'),
			'qa' => array('asi'),
			're' => array('afr'),
			'ro' => array('eur'),
			'rs' => array('eur'),
			'ru' => array('asi'),
			'rw' => array('afr'),
			'sa' => array('asi'),
			'sb' => array('oce'),
			'sc' => array('afr'),
			'sd' => array('afr'),
			'se' => array('eur'),
			'sg' => array('asi'),
			'sh' => array('afr'),
			'si' => array('eur'),
			'sj' => array('eur'),
			'sk' => array('eur'),
			'sl' => array('afr'),
			'sm' => array('eur'),
			'sn' => array('afr'),
			'so' => array('afr'),
			'sr' => array('ams'),
			'st' => array('afr'),
			'sv' => array('ams'),
			'sy' => array('asi'),
			'sz' => array('afr'),
			'tc' => array('ams'),
			'td' => array('afr'),
			'tf' => array('oce'),
			'tg' => array('afr'),
			'th' => array('asi'),
			'tj' => array('asi'),
			'tk' => array('oce'),
			'tl' => array('asi'),
			'tm' => array('asi'),
			'tn' => array('afr'),
			'to' => array('oce'),
			'tr' => array('eur'),
			'tt' => array('ams'),
			'tv' => array('asi'),
			'tw' => array('asi'),
			'tz' => array('afr'),
			'ua' => array('eur'),
			'ug' => array('afr'),
			'um' => array('oce'),
			'us' => array('amn'),
			'uy' => array('ams'),
			'uz' => array('asi'),
			'va' => array('eur'),
			'vc' => array('ams'),
			've' => array('ams'),
			'vg' => array('ams'),
			'vi' => array('ams'),
			'vn' => array('asi'),
			'vu' => array('oce'),
			'wf' => array('oce'),
			'ws' => array('asi'),
			'ye' => array('asi'),
			'yt' => array('afr'),
			'za' => array('afr'),
			'zm' => array('afr'),
			'zw' => array('afr'),

			// exceptionally reserved
			'ac' => array('afr'), // .ac TLD
			'cp' => array('ams'),
			'dg' => array('asi'),
			'ea' => array('afr'),
			'eu' => array('eur'), // .eu TLD
			'fx' => array('eur'),
			'ic' => array('afr'),
			'su' => array('eur'), // .su TLD
			'ta' => array('afr'),
			'uk' => array('eur'), // .uk TLD

			// transitionally reserved
			'bu' => array('asi'),
			'cs' => array('eur'), // former Serbia and Montenegro
			'nt' => array('asi'),
			'sf' => array('eur'),
			'tp' => array('oce'), // .tp TLD
			'yu' => array('eur'), // .yu TLD
			'zr' => array('afr')
		);
}

