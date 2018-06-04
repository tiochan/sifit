<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage info
 *
 * Time and chronometer functions.
 *
 */

	function show_status_info() {
		echo "<h3>REQUEST INFORMATION</h3>";
		echo "Variable status for <b>" . $_SERVER['PHP_SELF'] . "</b><br><br>";
		echo "<table border=1>";
		echo "<tr><td bgcolor=#E0E0E0 colspan=2><b>Global variables</b></td></tr>";
		echo "<tr><td>SERVER_NAME</td><td>". $_SERVER['SERVER_NAME'] . "</td></tr>";
		echo "<tr><td>REQUEST_METHOD</td><td>". $_SERVER['REQUEST_METHOD'] . "</td></tr>";
		echo "<tr><td>QUERY_STRING</td><td>". $_SERVER['QUERY_STRING'] . "</td></tr>";
//		echo "<tr><td>HTTP_ACCEPT</td><td>". $_SERVER['HTTP_ACCEPT'] . "</td></tr>";
//		echo "<tr><td>HTTP_ACCEPT_CHARSET</td><td>". $_SERVER['HTTP_ACCEPT_CHARSET'] . "</td></tr>";
//		echo "<tr><td>HTTP_ACCEPT_ENCODING</td><td>". $_SERVER['HTTP_ACCEPT_ENCODING'] . "</td></tr>";
//		echo "<tr><td>HTTP_ACCEPT_LANGUAGE</td><td>". $_SERVER['HTTP_ACCEPT_LANGUAGE'] . "</td></tr>";
//		echo "<tr><td>HTTP_CONNECTION</td><td>". $_SERVER['HTTP_CONNECTION'] . "</td></tr>";
		echo "<tr><td>HTTP_HOST</td><td>". $_SERVER['HTTP_HOST'] . "</td></tr>";
//		echo "<tr><td>HTTP_REFERER</td><td>". $_SERVER['HTTP_REFERER'] . "</td></tr>";
//		echo "<tr><td>HTTP_USER_AGENT</td><td>". $_SERVER['HTTP_USER_AGENT'] . "</td></tr>";

		echo "<tr><td bgcolor=#E0E0E0 colspan=2><b>REQUEST (" . count($_REQUEST) . ")</b></td></tr>";
		foreach ($_REQUEST as $key => $value)
			echo "<tr><td>$key</td><td>$value</td></tr>";

		echo "<tr><td bgcolor=#E0E0E0 colspan=2><b>FILES (" . count($_FILES) . ")</b></td></tr>";
		foreach ($_FILES as $key => $value) {
			echo "<tr><td colspan=2>$key</td></tr>";
			foreach ($_FILES[$key] as $key2 => $value2) {
				echo "<tr><td>$key2</td><td>$value2</td></tr>";
			}
		}

		echo "<tr><td bgcolor=#E0E0E0 colspan=2><b>GET (" . count($_GET) . ")</b></td></tr>";
		foreach ($_GET as $key => $value)
			echo "<tr><td>$key</td><td>$value</td></tr>";

		echo "<tr><td bgcolor=#E0E0E0 colspan=2><b>POST (" . count($_POST) . ")</b></td></tr>";
		foreach ($_POST as $key => $value)
			echo "<tr><td>$key</td><td>$value</td></tr>";

		echo "<tr><td bgcolor=#E0E0E0 colspan=2><b>APACHE REQUEST HEADERS</b></td></tr>";

		$headers = apache_request_headers();

		foreach ($headers as $header => $value) {
			 echo "<tr><td>$header</td><td>$value</td></tr>";
		}

		echo "<tr><td bgcolor=#E0E0E0 colspan=2><b>APACHE RESPONSE HEADERS</b></td></tr>";

		$headers = apache_response_headers();

		foreach ($headers as $header => $value) {
			 echo "<tr><td>$header</td><td>$value</td></tr>";
		}

		echo "</table>";
	}

?>