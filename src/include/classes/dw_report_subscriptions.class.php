<?php

/**
 * @author Jorge Novoa (jorge.novoa@upcnet.es)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage reports
 *
 */

/*

	Table definition

	+-----------+--------------+------+-----+---------+----------------+
	| Field     | Type         | Null | Key | Default | Extra          |
	+-----------+--------------+------+-----+---------+----------------+
	| id_rs     | mediumint(9) | NO   | PRI | NULL    | auto_increment |
	| id_user   | mediumint(9) | NO   | MUL |         |                |
	| id_report | mediumint(9) | NO   |     |         |                |
	+-----------+--------------+------+-----+---------+----------------+


*/


include_once "../include/init.inc.php";
include_once INC_DIR . "/forms/field_types/listbox.inc.php";
include_once INC_DIR . "/forms/field_ext.inc.php";
include_once INC_DIR . "/forms/form_elements/datawindow_ext.inc.php";


class dw_report_subscriptions extends datawindow_ext
{

	public function dw_report_subscriptions(&$optional_db = null)
	{

		global $USER_LEVEL, $global_db, $MESSAGES, $USER_GROUP, $USER_ID;

		$db = is_null($optional_db) ? $global_db : $optional_db;

		$null_reference = null;


		// Datawindow Query
		$qry = new datawindow_query();


		$report_restriction = $USER_LEVEL == 0 ? "" : "(id_group is null or id_group='" . $USER_GROUP . "')";
		$report_reference = new foreign_key($db, "reports", "id_report", "report_name", $report_restriction);

		// Fields
		$fields = array();
		$fields[] = new field_ext("report_subscription.id_rs", "", "auto", false, true, 0, false);

		if ($USER_LEVEL == 0) {
			$users_reference = new foreign_key($db, "users", "id_user", "username");
			$fields[] = new field_ext("report_subscription.id_user", $MESSAGES["SUBSCRIPTIONS_FIELD_USER"], "foreign_key", true, false, 1, true, $null_reference, $users_reference);
		} else {
			$fields[] = new field_ext("report_subscription.id_user", $MESSAGES["SUBSCRIPTIONS_FIELD_USER"], "integer", false, false, 0, false, $USER_ID);
		}
		$fields[] = new field_ext("report_subscription.id_report", $MESSAGES["SUBSCRIPTIONS_FIELD_REPORT"], "foreign_key", true, false, 2, true, $null_reference, $report_reference);

		// Creation of table and add it to query
		$can_insert = ($USER_LEVEL <= 5);
		$can_update = ($USER_LEVEL == 0);
		$can_delete = ($USER_LEVEL <= 5);
		$table_rs = new datawindow_table("report_subscription", $fields, 0, $can_insert, $can_update, $can_delete);
		$qry->add_table($table_rs);

		if ($USER_LEVEL != 0) {
			$qry->add_custom_restriction("report_subscription.id_user='" . $USER_ID . "'");
		}

		parent::datawindow_ext($qry);
	}

	public function show()
	{
		global $CURRENT_USER;

		if ($CURRENT_USER->send_notifications == 0) html_showWarning("You have disabled the email notifications");

		parent::show();
	}

	function pre_insert(&$values)
	{

		global $USER_ID;
		global $USER_LEVEL;

		if ($USER_LEVEL != 0) {
			$values["report_subscription.id_user"] = $USER_ID;
		}

		return 1;
	}
}
