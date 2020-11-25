<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package conf
 * @subpackage output
 *
 */

/**
 * Pattern replacement Configuration
 *
 * This association is used to replace the content of an email.
 * The first array (emo - email Original) contains the original string. Are custom TAGS that will be converted.
 * The second (emh - email Html) is the array that contains the translation for HTML emails
 * The third (emt - email Text) contains the translation for text emails.
 */
//  ORIGINAL                    HTML                        TEXT
//---------------------------   -------------------------   --------------------------
global $emo;					global $emh;				global $emt;
$emo=array();					$emh= array();				$emt=array();
$emo[]='/\n/';					$emh[]= '
';				$emt[]='';
$emo[]='/<HLINE([^>]*)>/i';		$emh[]='<hr${1}>';			$emt[]='
---------------------------------------------------------------------
';
$emo[]='/<LINE_BREAK>/i';		$emh[]= '<br>
';				$emt[]='
';
$emo[]='/<BOLD>/i';				$emh[]= '<b>';				$emt[]='';
$emo[]='/<\/BOLD>/i';			$emh[]= '</b>';				$emt[]='';
$emo[]='/<SUBR>/i';				$emh[]= '<u>';				$emt[]='';
$emo[]='/<\/SUBR>/i';			$emh[]= '</u>';				$emt[]='';
$emo[]='/<ITALIC>/i';			$emh[]= '<i>';				$emt[]='[';
$emo[]='/<\/ITALIC>/i';			$emh[]= '</i>';				$emt[]=']';
$emo[]='/<PARAGRAPH>/i';		$emh[]= '<p>';				$emt[]='';
$emo[]='/<\/PARAGRAPH>/i';		$emh[]= '</p>
';				$emt[]='

';
$emo[]='/<TITLE_1>/i';			$emh[]= '<h1>';				$emt[]='';
$emo[]='/<TITLE_2>/i';			$emh[]= '<h2>';				$emt[]='';
$emo[]='/<TITLE_3>/i';			$emh[]= '<h3>';				$emt[]='';
$emo[]='/<\/TITLE_1>/i';		$emh[]= '</h1>';			$emt[]='

';
$emo[]='/<\/TITLE_2>/i';		$emh[]= '</h2>';			$emt[]='

';
$emo[]='/<\/TITLE_3>/i';		$emh[]= '</h3>';			$emt[]='

';
$emo[]='/<TABLE([^>]*)>/i';		$emh[]= '<table${1}>';		$emt[]='';
$emo[]='/<TH([^>]*)>/i';		$emh[]= '<th${1}>';			$emt[]='';
$emo[]='/<\/TH>/i';				$emh[]= '</th>';			$emt[]='	';
$emo[]='/<TR([^>]*)>/i';		$emh[]= '<tr${1}>';			$emt[]='
';
$emo[]='/<\/TR>/i';				$emh[]= '</tr>';			$emt[]='';
$emo[]='/<TD([^>]*)>/i';		$emh[]= '<td${1}>';			$emt[]='';
$emo[]='/<\/TD>/i';				$emh[]= '</td>';			$emt[]='	';
$emo[]='/<\/TABLE>/i';			$emh[]= '</table>';			$emt[]='
';
$emo[]='/\&nbsp\;/';			$emh[]= '&nbsp;';			$emt[]='';
$emo[]='/<LINK ([^>]*)>/i';		$emh[]= '<a href=${1}>';	$emt[]='${1}';
$emo[]='/<\/LINK>/i';			$emh[]= '</a>';				$emt[]='';
$emo[]='/<FONT ([^>]*)>/i';		$emh[]= '<font ${1}>';		$emt[]='';
$emo[]='/<\/FONT>/i';			$emh[]= '</font>';			$emt[]='';
$emo[]='/<IMAGE ([^>]*)>/i';	$emh[]= '<\IMG ${1}\>';		$emt[]='';
