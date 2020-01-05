<?php
/*
	Author: Sebastian Gomez, (tiochan@gmail.com)
	For: Politechnical University of Catalonia (UPC), Spain.

	Cron script example
	You own scripts must be placed at my_include/cron
*/

	define("CLI_REQUIRED",true);
	define("QUIET",true);

	$dir= dirname($_SERVER["PHP_SELF"]);
	define("SYSHOME", $dir . "/../..");

	include_once SYSHOME . "/include/init.inc.php";

	$return_code=0;			// 0: ok, [1,2, ...] : error

	echo "\n\nTEST " . date("Y-m-d h:i:s") . "\n";
	exit($return_code);
?>