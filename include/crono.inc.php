<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage crono
 *
 * Time and chronometer functions.
 *
 */

	$time_start=0.0;
	$time_end=0.0;

	function getmicrotime(){
		 list($usec, $sec) = explode(" ",microtime());
		 return ((float)$usec + (float)$sec);
	}

	function crono_start() {
		global $time_start;

		$time_start = getmicrotime();
		return $time_start;
	}

	function crono_stop() {
		global $time_start;
		global $time_end;

		$time_end = getmicrotime();
		$time = round($time_end - $time_start, 3);

		return $time;
	}
?>