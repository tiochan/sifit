<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage output
 *
 * Output tags implementation file
 */

	include_once SYSHOME . "/conf/output_tags.conf.php";

	/**
	 * Replace the tags of the str to html tags.
	 *
	 * @param string $str
	 * @return converted_string
	 */
	function tags_to_html($str) {
		global $emo;
		global $emh;

		$ret= preg_replace($emo, $emh, $str);
		return $ret;
	}

	/**
	 * Replace the tags of the str to text chars.
	 *
	 * @param string $str
	 * @return converted_string
	 */
	function tags_to_text($str) {
		global $emo;
		global $emt;

		$ret= preg_replace($emo, $emt, $str);
		return $ret;
	}

	function my_echo($str) {
		if(defined("CLI_MODE") and CLI_MODE) echo tags_to_text($str);
		else echo tags_to_html($str);
	}
?>