<?php
	function & get_tags($string) {

		// PREG_OFFSET_CAPTURE
		$regexp1='#\{([a-z0-9\,\.\-\_\|\=\;\<\>\'\"\@\#\%\&\(\)\$\/ ]*?)}#is';
		$regexp1='#\{(.\.?[^{][^}])}#is';
		$regexp1='#\{(.[^\{\}]*)}#is';

		$ret= preg_match_all($regexp1, $string, $tags, PREG_PATTERN_ORDER );

		if(!$ret) return null;
		else return $tags[1];
	}

	function & get_vars($string) {

		// PREG_OFFSET_CAPTURE
		$regexp='#\[\$([a-z0-9\.\-_|=;\$ ]*?)]#is';
		$regexp1='#\[\$(.?[^{]*[^}])]#is';
		$regexp2='#\{\$(.[^\{\}]*)}#is';

		preg_match_all($regexp1, $string, $vars1, PREG_PATTERN_ORDER );
		preg_match_all($regexp2, $string, $vars2, PREG_PATTERN_ORDER );

		$vars= array_merge($vars1[1], $vars2[1]);
		return $vars;
	}


	$str="{Z_TEST_SHELL_DATA_GENERIC|DO-CU-MENT=resultats4.txt} mas alguna .,- cpsa  símbolos raros !\"f099!·$%&/()=?";
	echo "STR: $str<br/>";
	echo "<pre>";
	print_r(get_tags($str));
	print_r(get_vars($str));

	$str="Hola, dentro hay un tag {hola esto es un tag|y este es un {\$parametro} escondido.} dentro del otro.";

	echo "STR: $str<br/>";

	echo "<pre>";
	print_r(get_tags($str));

	$str="{GENERIC_QUERY_LOCAL|QUERY=SELECT vote_date, vote FROM day_poll WHERE id_user='1' AND vote_date >= '2012/12/01'}";
	echo "STR: $str<br/>";
	echo "<pre>";
	print_r(get_tags($str));


	$str='Hola, dentro hay una variable [$variable1|y este es un parametro escondido] y otra variable {$var2}.';

	echo "STR: $str<br/>";

	echo "<pre>";
	print_r(get_vars($str));

?>