<?php
/*
	Author: Sebastian Gomez, (tiochan@gmail.com)
	For: Politechnical University of Catalonia (UPC), Spain.

	Download tool
*/

	$AUTH_REQUIRED=true;
	$AUTH_LVL=5;			// ADMINS ONLY

	define("QUIET",true);

	include_once "../include/init.inc.php";
	include_once INC_DIR . "http_functions.php";


	$file=get_http_param("file");
	if(!$file) {
		?> <script>alert("file not set"); self.close()</script> <?php
		exit;
	}

	$final_file= "/tmp/" . basename($file);

	if(!file_exists($final_file)) {
		?> <script>alert("file not found"); self.close()</script> <?php
		exit;
	}

	download_file($final_file);
