<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage http
 *
 * Time and chronometer functions.
 *
 */

	function show_request($result) {
		global $remote_server;

		echo "<br><table bgcolor=#C0C0C0 border=1><tr><td>";
		echo "<center><h3>----- RESPONSE FROM SERVER $remote_server -----</h3></center>";
		echo "<font color=#800080>$result</font>";

		echo "<center><h3>----- END OF RESPONSE -----<h3></center>";
		echo "</td></tr></table>";
	}


	function send_http_post($server, $port, $script, $protocol, $vars, &$result) {
		global $SERVER_NAME;

		switch($protocol) {
			case "http":
				$conn_str="tcp://";
				break;
			case "https":
				$conn_str="ssl://";
				break;
			default:
				return 0;
		}

		$path= $protocol . "://" . $server . $script;

		$data="";
		if(is_array($vars) && count($vars) > 0) {
			$separador="";
			foreach($vars as $key => $value) {
				$data.="$separador$key=" . urlencode($value);
				$separador="&";
			}
		}

		// Enviamos los datos...
		$sock=fsockopen($conn_str . $server, $port, $errno, $errstr, 30);
		if(!$sock) {
			$result= "ALERT: Can't connect with remote server $server. $errno : $errstr";
			return false;
		}


		fputs($sock, "POST $path HTTP/1.0\r\n");
		fputs($sock, "Host: $SERVER_NAME\r\n");
		fputs($sock, "Content-type: application/x-www-form-urlencoded\r\n");
		fputs($sock, "Content-length: " . strlen($data) . "\r\n");
		fputs($sock, "Accept: */*\r\n");
		fputs($sock, "\r\n");
		fputs($sock, "$data\r\n");
		fputs($sock, "\r\n");

		// Esperamos la respuesta del servidor remoto...
		$result= "";
		while (!feof($sock)) $result .= fread($sock,32000);

		fclose($sock);

		$result= addslashes($result);

		return true;
	}

	function send_http_post_files($server, $port, $script, $protocol, $vars, $files, &$result) {
		global $SERVER_NAME;

		switch($protocol) {
			case "http":
				$conn_str="tcp://";
				break;
			case "https":
				$conn_str="ssl://";
				break;
			default:
				return 0;
		}

		$path= $protocol . "://" . $server . $script;

		srand((double)microtime()*1000000);
		$boundary = "---------------------------".substr(md5(rand(0,32000)),0,10);

		$data="";
		if(is_array($vars) && count($vars) > 0) {

			foreach($vars as $key => $value) {
				$data.="
--$boundary
Content-Disposition: form-data; name=\"$key\"

$value";
			}
		}

		if(is_array($files) && count($files) > 0) {
			foreach($files as $file_i) {
				$content_type= $file_i['content-type'];
				$file_name= $file_i['file'];
				if(!file_exists($file_name)) {
					$result= "Error, file " . $file_i['file'] . " doesn't exists.";
					return false;
				}
				$file_base_name= basename($file_name);
				$var_name= "file";
				$content_file = join("", file($file_name));
				//echo "$content_type $file_base_name $content_file<br>";
				$data.="
--$boundary
Content-Disposition: form-data; name=\"$var_name\"; filename=\"$file_base_name\"
Content-Type: $content_type

$content_file";
			}
		}

/* Posible error de violaci�n del rfc2046, multi-part
$data.="
--$boundary

--\r\n\r\n";
*/
$data.="
--$boundary--\r\n\r\n";

		$msg=
"POST $path HTTP/1.0
Host: $SERVER_NAME
Content-Type: multipart/form-data; boundary=$boundary
Content-Length: ".strlen($data)."\r\n\r\n";

		// Enviamos los datos...
		//$sock=fopen("stream.dat","w+") or die("Error opening file.");

		$sock=fsockopen($conn_str . $server, $port, $errno, $errstr, 30);
		if($sock===false) {
			$result= "ALERT: Can't connect with remote server $server. $errno : $errstr";
			return false;
		}

		fputs($sock, $msg);
		fputs($sock, $data);

		// Esperamos la respuesta del servidor remoto...
		$result= "";
		while (!feof($sock)) $result .= @fread($sock,32000);

		fclose($sock);

		$result= addslashes($result);
		return true;
	}

	function send_http_post_files_cert($server, $port, $script, $protocol, $vars, $files, &$result) {
		global $SERVER_NAME;

		switch($protocol) {
			case "http":
				$conn_str="tcp://";
				break;
			case "https":
				$conn_str="ssl://";
				break;
			default:
				return 0;
		}

		$path= $protocol . "://" . $server . $script;

		srand((double)microtime()*1000000);
		$boundary = "---------------------------".substr(md5(rand(0,32000)),0,10);

		$data="";
		if(is_array($vars) && count($vars) > 0) {

			foreach($vars as $key => $value) {
				$data.="
--$boundary
Content-Disposition: form-data; name=\"$key\"

$value";
			}
		}

		if(is_array($files) && count($files) > 0) {
			foreach($files as $file_i) {
				$content_type= $file_i['content-type'];
				$file_name= $file_i['file'];
				if(!file_exists($file_name)) {
					$result= "Error, file " . $file_i['file'] . " doesn't exists.";
					return false;
				}
				$file_base_name= basename($file_name);
				$var_name="file";
				$content_file = join("", file($file_name));
				//echo "$content_type $file_base_name $content_file<br>";
				$data.="
--$boundary
Content-Disposition: form-data; name=\"$var_name\"; filename=\"$file_base_name\"
Content-Type: $content_type

$content_file";
			}
		}

$data.="
--$boundary--\r\n\r\n";

		$msg=
"POST $path HTTP/1.0
Host: $SERVER_NAME
Content-Type: multipart/form-data; boundary=$boundary
Content-Length: ".strlen($data)."\r\n\r\n";

		// Enviamos los datos...
		$context = stream_context_create();
		$result = stream_context_set_option($context, 'ssl', 'local_cert', '/usr/local/apache2/htdocs/diastasis/transfer/keys.pem');
		$result = stream_context_set_option($context, 'ssl', 'passphrase', '');

		$sock=fsockopen($conn_str . $server, $port, $errno, $errstr, 30, $context);
		if($sock===false) {
			$result= "ALERT: Can't connect with remote server $server. $errno : $errstr";
			return false;
		}

		fputs($sock, $msg);
		fputs($sock, $data);

		// Esperamos la respuesta del servidor remoto...
		$result= "";
		while (!feof($sock)) $result .= @fread($sock,32000);

		fclose($sock);

		$result= addslashes($result);
		return true;
	}

	function download_file($file) { // $file = include path

        if(file_exists($file)) {
            header('Content-Description: File Transfer', false);
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            //ob_clean();
            //flush();
            readfile($file);
            exit;
        }
    }
?>