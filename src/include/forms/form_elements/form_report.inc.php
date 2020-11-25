<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage forms
 *
 */

include_once SYSHOME . "/include/forms/form_elements.inc.php";
include_once INC_DIR . "/reports/reports.class.php";


/**
 * Creates a graph
 *
 */
class form_report extends form_element
{

	protected $report_name;
	protected $report_id = false;
	protected $report;

	function form_report($report_name)
	{

		global $global_db;

		parent::form_element($report_name);

		$this->report_name = $report_name;

		$query = "select id_report from reports where report_name='$report_name'";
		$res = $global_db->dbms_query($query);

		if ($global_db->dbms_check_result($res)) {

			list($this->report_id) = $global_db->dbms_fetch_row($res);
			$global_db->dbms_free_result($res);
			$this->report = new report($this->report_id);
		}
	}

	function show()
	{

		global $USER_LEVEL;

		parent::show();

		if (!$this->visible) return;

		if ($this->report_id === false) {
			html_showError("Report $this->report_name not found.");
			return;
		}

		// Report access: only generic (id_group is null) and own group reports
		if ($USER_LEVEL != 0 and $this->report->id_group != null and $this->report->id_group != $USER_GROUP) {
			html_showError("** Not allowed to see this report **", true);
		} else {

			$content = $this->report->parse_report();
			echo $content;
		}
	}
}
