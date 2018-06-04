<?php

	$escape_results=false;


//	$regexp_styles= '/<link rel=\"stylesheet\" type=\"text\/css\"[^`]*?href=\"([^`]*?)\" \/>/';
//	$regexp_wrapper= '/<div id=\"baners\">([^`]*?)<\/div>/';
	$regexp_styles= '/<link.*?type=\"text\/css\"[^`]*?href=\"([^`]*?)\" \/>/';
	$regexp_wrapper= '/<div.*?id=\"baners\"[^`]*?>([^`]*?)<\/div>/';


	$content= file_get_contents("http://www.upc.edu");


	$ret= preg_match_all($regexp_styles, $content, $results_styles, PREG_PATTERN_ORDER);
//	$ret= preg_match($regexp_styles, $content, $results_styles);

	$ret= preg_match_all($regexp_wrapper, $content, $results_wrappers, PREG_PATTERN_ORDER);
//	$ret= preg_match($regexp_wrapper, $content, $results_wrappers);


	if($escape_results) {

		echo "<h1>styles</h1>";
		foreach($results_styles as $result) {
			foreach($result as $res) {
				echo "<pre>";
				echo htmlspecialchars($res);
				echo "</pre>";
				echo "<hr>";
			}
		}

		echo "<h1>wrappers</h1>";
		foreach($results_wrappers as $result) {
			foreach($result as $res) {
				echo "<pre>";
				echo htmlspecialchars($res);
				echo "</pre>";
				echo "<hr>";
			}
		}

	} else {

		echo "<html><head>";
		foreach($results_styles[0] as $style) {
			echo $style;
		}

		echo "</head><body>";

		echo "<h1>wrappers</h1>";
		foreach($results_wrappers[0] as $wrapper) {
			echo $wrapper;
		}
		echo "</body></html>";
	}
?>