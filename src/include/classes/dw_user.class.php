<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage users
 *
 * Datawindow class for users management.
 */


/*
	Table definition

	+--------------------+--------------+------+-----+---------+----------------+
	| Field              | Type         | Null | Key | Default | Extra          |
	+--------------------+--------------+------+-----+---------+----------------+
	| id_user            | mediumint(9) |      | PRI | NULL    | auto_increment |
	| username           | char(60)     |      | UNI |         |                |
	| password           | char(60)     |      |     |         |                |
	| external           | tinyint(4)   |      |     | 0       |                |
	| name               | char(60)     |      |     |         |                |
	| surname            | char(60)     | YES  |     | NULL    |                |
	| id_group           | mediumint(9) |      |     | 0       |                |
	| email              | char(100)    |      |     |         |                |
	| level              | tinyint(4)   |      |     | 100     |                |
	| send_notifications | tinyint(4)   | YES  |     | NULL    |                |
	+--------------------+--------------+------+-----+---------+----------------+
*/

include_once SYSHOME . "/include/forms/forms.inc.php";
include_once SYSHOME . "/include/forms/form_elements/datawindow.inc.php";
include_once SYSHOME . "/include/forms/form_elements/search_box.inc.php";

class dw_user extends datawindow
{

	public function dw_user(&$optional_db = null)
	{

		global $USER_ID, $USER_GROUP, $USER_LEVEL, $global_db, $MESSAGES;

		$fields = array();

		$restriction = "id_group=$USER_GROUP";
		$group_reference = new foreign_key($global_db, "groups", "id_group", "name", $restriction);

		$restriction = $USER_LEVEL == 0 ? "" : "(id_group=$USER_GROUP or id_group is null)";
		$filter_reference = new foreign_key($global_db, "filters", "id_filter", "name", $restriction, "");

		$list = new listbox();

		$list->lb["0"] = $MESSAGES["SKILL_0"];
		$list->lb["3"] = $MESSAGES["SKILL_3"];
		$list->lb["5"] = $MESSAGES["SKILL_5"];

		$fields[] = new field("id_user", "", "auto", false, true, false, false);
		$fields[] = new field("username", $MESSAGES["USER_FIELD_USERNAME"], "fstring", true, true, true, false);
		$fields[] = new field("password", $MESSAGES["USER_FIELD_PASSWORD"], "password", false, false, false, true);
		$fields[] = new field("external", $MESSAGES["USER_FIELD_EXTERNAL"], "fbool", true, false, true, ($USER_LEVEL <= 3), 0);
		$fields[] = new field("name", $MESSAGES["USER_FIELD_NAME"], "fstring", true, false, true, true);
		$fields[] = new field("surname", $MESSAGES["USER_FIELD_SURNAME"], "fstring", false, false, true, true);
		$fields[] = new field("id_group", $MESSAGES["USER_FIELD_GROUP"], "foreign_key", true, false, true, ($USER_LEVEL < 3), $USER_GROUP, $group_reference);
		$fields[] = new field("email", $MESSAGES["USER_FIELD_EMAIL"], "fstring", true, false, true, true);
		$fields[] = new field("level", $MESSAGES["USER_FIELD_LEVEL"], "listbox", true, false, true, false, $USER_LEVEL, $list);
		$fields[] = new field("hiredate", $MESSAGES["USER_FIELD_HIREDATE"], "fstring", false, false, true, false);
		$fields[] = new field("lang", $MESSAGES["USER_FIELD_LANG"], "list_lang", true, false, true, true, "en");
		$fields[] = new field("send_notifications", $MESSAGES["USER_FIELD_SEND_NOTIFICATIONS"], "fbool", true, false, true, true, 1);

		parent::datawindow("users", $fields, 0, "id_user=$USER_ID", "", false, true, false, $optional_db);
	}

	// Only updates are allowed
	function pre_insert($values)
	{
		return 0;
	}

	function pre_delete($row_id, $values)
	{
		return 0;
	}

	function pre_update($row_id, $old_values, &$new_values)
	{
		global $global_db;
		global $USER_ID;

		$new_values["username"] = $old_values["username"];

		return 1;
	}
}
