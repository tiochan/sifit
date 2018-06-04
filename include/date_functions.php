<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage date functions
 *
 */

	function stringToDate($date){

		$dateElements = split(' ', $date);

		$dateDateElements = split('/', $dateElements[0]);
		$dateTimeElements = split(':', $dateElements[1]);

		// Get unix timestamp for date
		return mktime($dateTimeElements[0], $dateTimeElements[1], $dateTimeElements[2], $dateDateElements[1], $dateDateElements[0], $dateDateElements[2]);
	}

	function getDateDifference($dateFrom, $dateTo, $unit = 'd') {

		$difference = null;

		$dateFromElements = split(' ', $dateFrom);
		$dateToElements = split(' ', $dateTo);

		$dateFromDateElements = split('/', $dateFromElements[0]);
		$dateFromTimeElements = split(':', $dateFromElements[1]);
		$dateToDateElements = split('/', $dateToElements[0]);
		$dateToTimeElements = split(':', $dateToElements[1]);

		// Get unix timestamp for both dates

		$date1 = mktime($dateFromTimeElements[0], $dateFromTimeElements[1], $dateFromTimeElements[2], $dateFromDateElements[1], $dateFromDateElements[0], $dateFromDateElements[2]);
		$date2 = mktime($dateToTimeElements[0], $dateToTimeElements[1], $dateToTimeElements[2], $dateToDateElements[1], $dateToDateElements[0], $dateToDateElements[2]);

		if( $date1 > $date2 )
		{
			return null;
		}

		$diff = $date2 - $date1;

		$days = 0;
		$hours = 0;
		$minutes = 0;
		$seconds = 0;

		if ($diff % 86400 <= 0)  // there are 86,400 seconds in a day
		{
			$days = $diff / 86400;
		}

		if($diff % 86400 > 0)
		{
			$rest = ($diff % 86400);
			$days = ($diff - $rest) / 86400;

			if( $rest % 3600 > 0 )
			{
				$rest1 = ($rest % 3600);
				$hours = ($rest - $rest1) / 3600;

				if( $rest1 % 60 > 0 )
				{
					$rest2 = ($rest1 % 60);
					$minutes = ($rest1 - $rest2) / 60;
					$seconds = $rest2;
				}
				else
				{
					$minutes = $rest1 / 60;
				}
			}
			else
			{
				$hours = $rest / 3600;
			}
		}

		switch($unit)
		{
			case 'd':
			case 'D':

				$partialDays = 0;

				$partialDays += ($seconds / 86400);
				$partialDays += ($minutes / 1440);
				$partialDays += ($hours / 24);

				$difference = $days + $partialDays;

				break;

			case 'h':
			case 'H':

				$partialHours = 0;

				$partialHours += ($seconds / 3600);
				$partialHours += ($minutes / 60);

				$difference = $hours + ($days * 24) + $partialHours;

				break;

			case 'm':
			case 'M':

				$partialMinutes = 0;

				$partialMinutes += ($seconds / 60);

				$difference = $minutes + ($days * 1440) + ($hours * 60) + $partialMinutes;

				break;

			case 's':
			case 'S':

				$difference = $seconds + ($days * 86400) + ($hours * 3600) + ($minutes * 60);

				break;

			case 'a':
			case 'A':

				$difference = array (
				"days" => $days,
				"hours" => $hours,
				"minutes" => $minutes,
				"seconds" => $seconds
				);

				break;
		}

		return $difference;
	}

	function f_isDate($i_date, $format) {
		unset ($pattern);

		switch ($format) {
			case "YYYY-mm-dd" :
				$pattern = "([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})";
				break;
			case "YYYY-mm" :
				$pattern = "([0-9]{4})-([0-9]{1,2})";
				break;
			case "dd-mm-YYYY" :
				$pattern = "([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})";
				break;
			default :
		}

		if (!isset ($pattern))
		return false;

		return ereg($pattern, $i_date);
	}

?>