<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * @package sifit
 * @subpackage reports
 *
 * Tags definition class
 *
 */

include_once INC_DIR . "/reports/tags/tag_element.class.php";


class tag_script extends tag_element
{

	protected $show_connection = false;


	public function get_value()
	{

		$this->replace_parameters();

		$command = str_replace("\r", "", $this->value);
		$rand_name = "/tmp/rand_file_" . rand(1000, 9999) . ".tmp";
		file_put_contents($rand_name, $command);
		chmod($rand_name, 0700);
		exec($rand_name, $output, $exit_code);
		$output = implode("\n", $output);

		unlink($rand_name);

		if ($exit_code) {
			$message = "** Error ** The command exited with a exit code $exit_code. Check your output.<br>\n" .
				"Try redirecting the error output to the standard ouput to see the content.<br>\n";
		} else $message = "";

		return ($message . $output);
	}

	protected function show_extra_help()
	{
?>
		<p>
			To define a new script, the first line must contains the shebang used to allow the system to execute the content.<br>
			For instance:<br>
			<i>#!/bin/sh</i><br>
			<i>#!/usr/bin/env python</i><br>
			<i>#!/usr/local/bin/python</i><br>
			<br>
			In some cases, to debug your script you can redirect the error output to the standard output:<br>
			- For shell script you can use:<br>
			<i>exec 2>&1</i>
			<br>
			<!--
			<b>Extra parameters</b><br>
			<br>
			You can also add some extra parameters into the <i>'additional information field'</i>, separated by ';'.<br>
			There are some examples of extra parameters:<br>
			<br>
			<table class='data_box_rows'>
				<tr>
					<th class='data_box_rows'>Parameter</th>
					<th class='data_box_rows'>Default</th>
					<th class='data_box_rows'>Description</th>
				</tr>
				<tr>
					<td class='data_box_cell'>CSV=[true|false]</td>
					<td class='data_box_cell'>false</td>
					<td class='data_box_cell'>If you need values to be returned as CSV string, instead HTML table.</td>
				</tr>
				<tr>
					<td class='data_box_cell'>SHOW_NO_DATA=[true|false]</td>
					<td class='data_box_cell'>false</td>
					<td class='data_box_cell'>If true and the query does not retuns any data, a string with this situation is shown.</td>
				</tr>
				<tr>
					<td class='data_box_cell'>SHOW_FIELD_NAMES=[true|false]</td>
					<td class='data_box_cell'>false</td>
					<td class='data_box_cell'>For CSV output, if true then the first row is set with the field names..</td>
				</tr>
			</table>
			-->
		</p>
<?php
	}
}
