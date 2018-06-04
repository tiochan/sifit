<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage lang
 *
 */

	function load_language($lang) {

		$langFile= SYSHOME . "/include/lang/$lang/messages.inc.php";
		if(file_exists($langFile)) {
			include_once $langFile;
		} else {
			echo "Warning: $lang language not supported.\n<br>";
		}

		$my_langFile= SYSHOME . "/my_include/lang/$lang/messages.inc.php";
		if(file_exists($my_langFile)) {
			include_once $my_langFile;
		} else {
			echo "Warning: $lang language not defined at my_include/lang.\n<br>";
		}
	}

	$sysLang=LANG;
	$sysLangFile= SYSHOME . "/include/lang/$sysLang/messages.inc.php";
	file_exists($sysLangFile) or die("ERROR: Default Language file $sysLangFile not found.\n");

	include_once $sysLangFile;

	// Load additional messages
	$my_includeFile= SYSHOME . "/my_include/lang/$sysLang/messages.inc.php";
	if(file_exists($my_includeFile)) include_once $my_includeFile;
