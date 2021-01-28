<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage forms
 *
 */

include_once "listbox.inc.php";

class list_day extends listbox
{

	function list_day()
	{

		parent::listbox();

		$this->lb[""] = "*";
		for ($i = 1; $i <= 31; $i++) {
			$this->lb["$i"] = $i;
		}
	}

	function check($value)
	{
		return (is_numeric($value) and ($value > 0) and ($value <= 31));
	}

	function get_sql_value($field_value)
	{
		if ($field_value == "") {
			return "null";
		} else {
			return "'" . htmlspecialchars($field_value) . "'";
		}
	}
}

class list_month extends listbox
{

	function list_month()
	{

		parent::listbox();

		$this->lb[""] = "*";
		for ($i = 1; $i <= 12; $i++) {
			$this->lb["$i"] = $i;
		}
	}

	function check($value)
	{
		return (is_numeric($value) and ($value > 0) and ($value <= 12));
	}

	function get_sql_value($field_value)
	{
		if ($field_value == "") {
			return "null";
		} else {
			return "'" . htmlspecialchars($field_value) . "'";
		}
	}
}

class list_hour extends listbox
{

	function list_hour()
	{

		parent::listbox();

		$this->lb[""] = "*";
		for ($i = 0; $i <= 23; $i++) {
			$this->lb["$i"] = $i;
		}
	}

	function check($value)
	{
		return (is_numeric($value) and ($value >= 0) and ($value <= 23));
	}

	function get_sql_value($field_value)
	{
		if ($field_value == "") {
			return "null";
		} else {
			return "'" . htmlspecialchars($field_value) . "'";
		}
	}
}

class list_minute extends listbox
{

	function list_minute()
	{

		parent::listbox();

		$this->lb[""] = "*";
		for ($i = 0; $i < 60; $i += 10) {
			$this->lb["$i"] = $i;
		}
	}

	function check($value)
	{
		return (is_numeric($value) and ($value >= 0) and ($value < 60));
	}

	function get_sql_value($field_value)
	{
		if ($field_value == "") {
			return "null";
		} else {
			return "'" . htmlspecialchars($field_value) . "'";
		}
	}
}

class list_time extends listbox
{

	function list_time()
	{

		parent::listbox();


		$this->lb[""] = "--:--";

		for ($h = 0; $h < 10; $h++) {
			$this->lb["0$h:00"] = "0$h:00";
			$this->lb["0$h:30"] = "0$h:30";
		}

		for ($h = 10; $h < 24; $h++) {
			$this->lb["$h:00"] = "$h:00";
			$this->lb["$h:30"] = "$h:30";
		}
	}

	function check($value)
	{
		return (in_array($value, $this->lb));
	}

	function get_sql_value($field_value)
	{
		if ($field_value == "") {
			return "null";
		} else {
			return "'" . htmlspecialchars($field_value) . "'";
		}
	}
}
