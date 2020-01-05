<?php

$test_json=array(
	"color" => "red",
	"cosas" => "grande"
);

$tag_regexps= array(
	'#\{\{([a-z0-9\,\.\-\_\|\=\;\<\>\'\"\@\#\%\&\(\)\$\/ ]*?)}}#is',
	'#\{\{([a-z0-9\,\.\-\_\|\=\;\<\>\'\"\@\#\%\&\(\)\$\/ ][^\{\}]*?)}}#is',
	'#\{\{(.\.?[^{][^}])}}#is',
	'#\{\{([^\$\s].[^\{\}]*)\}\}#is'
);

$var_regexps= array(
	'#\[\$([a-z0-9\.\-_|=;\$ ]*?)]#is',
	'#\[\$(.?[^{]*[^}])]#is',
	'#\{\$(.[^\{\}]*)}#is',
	'#\[\$([^\s].[^\{\}]*)\]#is',
	'#\{\$([^\s].[^\{\}]*)\}#is'
);

$strings= array(
	"{{php_code|VALUE=json_decode(\"{{system_command|VALUE=curl -s -u sebastian-gomez:{{GITHUB_TOKEN}} 'https://github.schibsted.io/api/v3/repos/spt-infrastructure/cre-internal-doc/issues?state=open&milestone=3'}}\"}}",
	"{{Z_TEST_SHELL_DATA_GENERIC|DO-CU-MENT=resultats4.txt}} mas alguna .,- cpsa  símbolos raros !\"f099!·$%&/()=?",
	"Hola, dentro hay un tag {{hola esto es un tag|y este es un {{\$parametro}} escondido.}} dentro del otro.",
	"Hola, dentro hay un tag {{hola esto es un tag|y este es un tag_reemplazado escondido.}} dentro del otro.",
	"{{GENERIC_QUERY_LOCAL|QUERY=SELECT vote_date, vote FROM day_poll WHERE id_user='1' AND vote_date >= '2012/12/01'}}",
	"{{GENERIC_QUERY_LOCAL|QUERY=SELECT vote_date, vote FROM day_poll WHERE id_user='1' AND vote_date >= '{{MONTH_DAY_1'}}}}",
	'Hola, dentro hay una variable [$variable1|y este es un parametro escondido] y otra variable {{$var2}}.',
	"
	var div_id='{{\$DIV_ID}}';
	var div_obj=document.getElementById(div_id);
	if(div_obj == null) { 
		console.log(\"Error: Div id not found '\" + div_id + \"'\");
		exit; 
	}
	var value={{\$VALUE}};
	alert('Value is ' + value + ' and DIV ID is ' + div_id);
	if(value < {{\$WARN_LIMIT}}) {
		div_obj.style.backgroundColor=\"{{\$NORMAL_BG_COLOR}}\";
		div_obj.style.color=\"{{\$NORMAL_FG_COLOR}}\";
	} else if (value < {{\$CRITICAL_LIMIT}}) {
		div_obj.style.backgroundColor=\"{{\$WARN_BG_COLOR}}\"; 
		div_obj.style.color=\"{{\$WARN_FG_COLOR}}\"; 
	} else { 
		div_obj.style.backgroundColor=\"{{\$CRITICAL_BG_COLOR}}\";
		div_obj.style.color=\"{{\$CRITICAL_FG_COLOR}}\";
	}
	{{TAG_TEST_DETECTION}}",
	"[\$MIN],[\$MAX]",
	"{{\$MIN}},{{\$MAX}}",
	json_encode($test_json)
);


function get_tags($string, $regexp) {

	// PREG_OFFSET_CAPTURE

	$ret= preg_match_all($regexp, $string, $tags, PREG_PATTERN_ORDER );

	if(!$ret) return null;
	else return $tags[1];
}


function print_array($array) {
	echo "<pre>";
	print_r($array);
	echo "</pre>";
}


echo "<h1>REGULAR EXPRESSION TESTER</h1>";
foreach($strings as $str) {

	echo "<hr/><div style='background-color: yellow'><b>String:</b> $str</div><br>";
	echo "<h2>TAGs</h2>";
	$cont=0;
	echo "<table>";
	foreach($tag_regexps as $regexp) {
		echo "<tr>";
		echo "<td># $cont&nbsp;&nbsp;&nbsp;</td>";
		echo "<td>";
		print_array(get_tags($str, $regexp));
		echo "</td><td>";
		echo "&nbsp;&nbsp;&nbsp;'" . htmlspecialchars($regexp) . "'\n";
		echo "</td></tr>";
		$cont++;
	}
	echo "</table>";

	echo "<h2>VARs</h2>";
	$cont=0;
	echo "<table>";
	foreach($var_regexps as $regexp) {
		echo "<tr>";
		echo "<td># $cont&nbsp;&nbsp;&nbsp;</td>";
		echo "<td>";
		print_array(get_tags($str, $regexp));
		echo "</td><td>";
		echo "&nbsp;&nbsp;&nbsp;'" . htmlspecialchars($regexp) . "'\n";
		echo "</td></tr>";
		$cont++;
	}
	echo "</table>";
}
?>
