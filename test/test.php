<?php
/*
$w = stream_get_wrappers();
echo 'openssl: ',  extension_loaded  ('openssl') ? 'yes':'no', "\n";
echo 'http wrapper: ', in_array('http', $w) ? 'yes':'no', "\n";
echo 'https wrapper: ', in_array('https', $w) ? 'yes':'no', "\n";
echo 'wrappers: ', var_export($w);

exit;
*/

$url="https://www.infojobs.net/ofertas-trabajo/barcelona/vilafranca-del-penedes";
//$url="http://www.ccma.cat/324/";
//$url='https://example.com/';
$opts = array(
	'https' => array(
		'method'=>"GET",
		'header' => "" .
		'User-Agent:"Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:45.0) Gecko/20100101 Firefox/45.0;' .
			'Content-Type: text/html; charset=utf-8;' .
			'Accept:"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"'
	),
	'ssl' => array(
		"verify_peer" => false,
		"allow_self_signed" => true,
	)
);

$context = stream_context_create($opts);
echo file_get_contents($url,false,$context);