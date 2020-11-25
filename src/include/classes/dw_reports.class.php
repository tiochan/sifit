<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 *
 * @package lib
 * @subpackage reports
 *
 * Datawindow class for report management.
 */

/*
	Table definition

	+-------------+--------------+------+-----+---------+----------------+
	| Field       | Type         | Null | Key | Default | Extra          |
	+-------------+--------------+------+-----+---------+----------------+
	| id_report   | mediumint(9) | NO   | PRI | NULL    | auto_increment |
	| report_name | varchar(60)  | YES  | MUL | NULL    |                |
	| id_group    | mediumint(9) | YES  |     | NULL    |                |
	| content     | text         | YES  |     | NULL    |                |
	| periodicity | tinyint(1)   | YES  |     | NULL    |                |
	+-------------+--------------+------+-----+---------+----------------+


*/

include_once INC_DIR . "/forms/field_types/listbox.inc.php";
include_once INC_DIR . "/forms/field.inc.php";
include_once INC_DIR . "/forms/form_elements/datawindow.inc.php";
include_once INC_DIR . "/forms/form_elements/search_box.inc.php";
include_once INC_DIR . "/classes/lb_periodicity.class.php";
include_once INC_DIR . "/classes/dw_report_tags.class.php";


class dw_reports extends datawindow
{

	public function dw_reports(&$optional_db = null)
	{

		global $USER_GROUP, $USER_LEVEL, $MESSAGES;
		global $global_db;


		$db = is_null($optional_db) ? $global_db : $optional_db;


		$periodicity = new report_periodicity();

		$group_restriction = "";
		if ($USER_LEVEL > 0) $group_restriction = "id_group= $USER_GROUP";

		$group_reference = new foreign_key($global_db, "groups", "id_group", "name", $group_restriction);


		$default_group = ($USER_LEVEL > 0) ? $USER_GROUP : null;

		$fields = array();
		$fields[] = new field("id_report", "", "auto", false, true, false, false);
		$fields[] = new master_field(HOME . "/tools/report_preview.php", $fields[0], "report_name", $MESSAGES["REPORT_FIELD_NAME"], "fstring", true, true, true, true);
		$fields[1]->add_parameter("show_header=true");
		$fields[] = new field("id_group", $MESSAGES["REPORT_FIELD_GROUP"], "foreign_key", false, false, true, ($USER_LEVEL == 0), $default_group, $group_reference);
		$fields[] = new field("content", $MESSAGES["REPORT_FIELD_CONTENT"], "html_report", false, false, false, ($USER_LEVEL <= 3));
		$fields[] = new field("description", $MESSAGES["REPORT_FIELD_DESCRIPTION"], "text", false, false, true, ($USER_LEVEL <= 3));
		$fields[] = new field("periodicity", $MESSAGES["REPORT_FIELD_PERIODICITY"], "listbox", true, false, true, ($USER_LEVEL <= 3), null, $periodicity);

		$fields[1]->new_window = true;

		$restriction = ($USER_LEVEL > 0) ? "((id_group= $USER_GROUP) or (id_group is null))" : "";

		$sb = new search_box(array($fields[1], $fields[4], $fields[5]), "report_search", $MESSAGES["SEARCH"]);

		$can_insert = ($USER_LEVEL <= 3);
		$can_update = ($USER_LEVEL <= 5);
		$can_delete = ($USER_LEVEL <= 3);
		$query_adds = "";

		parent::datawindow("reports", $fields, 0, $restriction, $query_adds, $can_insert, $can_update, $can_delete);
		$this->add_search_box($sb);
		$this->allow_save_and_continue = true;
	}
	/*
		function post_show_row($values) {
			echo $this->create_row_action("Preview","preview_report",$values["row_id"],ICONS . "/file.png");
		}
*/
	protected function pre_show_row(&$values, &$can_update, &$can_delete)
	{
		global $USER_LEVEL;
		global $USER_GROUP;

		$can_update = ($USER_LEVEL == 0) || (($USER_LEVEL == 3) and ($USER_GROUP == $values["id_group"]));
		$can_delete = $can_update;

		return 1;
	}

	public function pre_insert(&$values)
	{

		global $USER_LEVEL, $USER_GROUP;

		if ($USER_LEVEL != 0) $values["id_group"] = $USER_GROUP;
		return 1;
	}

	public function pre_update($row_id, $old_values, &$new_values)
	{

		global $USER_LEVEL, $USER_GROUP;

		if ($USER_LEVEL != 0) $new_values["id_group"] = $USER_GROUP;
		return 1;
	}

	public function action_preview_report($row_id)
	{

		//			html_showInfo("Mostrando el report $row_id");
	}
	/*
		public function make_report($content) {

			$report = new generate_report($content);
		}
*/
}

class html_report extends html
{

	protected $list_tags;

	function html_report()
	{

		$this->list_tags = new list_tags_fck();
		parent::html();
	}

	function show($field_name, $readonly)
	{

		if (!$readonly) $this->list_tags->show($field_name, false);
		parent::show($field_name, $readonly);
	}
}
