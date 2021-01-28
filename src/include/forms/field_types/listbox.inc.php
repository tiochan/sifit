<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage forms
 *
 */

require_once "basic.inc.php";


/**
 * The list box is defined using an associative array: key => value.
 *  - key is the final value set to field.
 *  - value is the literal show to user at web page.
 */
class listbox extends field_type
{

	public $name;
	public $lb;
	public $multiple;


	function listbox($multiple = false)
	{
		$this->lb = array();
		$this->value_reference = "'";
		$this->multiple = $multiple;
	}

	function show($field_name, $readonly, $for_search = false)
	{

		$def_value = "";

		if ($readonly) {
			echo "<input type='hidden' name='$field_name'>";
			echo "<select name='_" . $field_name . "' disabled='1' " . $this->col_modifier . ">\n";
		} else {
			if ($this->multiple) {
				echo "<select class='action' multiple name='" . $field_name . "[]'>\n";
			} else {
				echo "<select class='action' name='$field_name' " . $this->col_modifier . ">\n";
			}
		}

		if ($for_search) {
			echo "<option></option>";
		}

		foreach ($this->lb as $key => $value) {
			echo "<option value='$key'>$value</option>\n";
		}
		echo "</select>";
	}

	function show_simply($field_name)
	{
		$this->show($field_name, false, true);
	}

	function get_value($field_value, $for_show = true)
	{
		if (is_null($field_value)) {
			return "";
		}

		if ($for_show) {
			return (isset($this->lb[$field_value]) ? $this->lb[$field_value] : "? " . $field_value . " ?");
		} else {
			return parent::get_value($field_value, $for_show);
		}
	}

	public function set_form_value($form_name, $field_name, $value, $visible = true)
	{

		if (!$visible) html_set_field_value($form_name, "_" . $field_name, $value);
		return html_set_field_value($form_name, $field_name, $value);
	}
}

class list_dir extends listbox
{

	public $dir;
	public $filter;

	function list_dir($dir, $filter = "")
	{
		$this->dir = "";
		$this->filter = "";

		parent::listbox();

		$this->lb[""] = "";

		if ($dir != "") $this->add_dir($dir, $filter);
	}

	function add_dir($dir, $filter = "")
	{
		$this->dir .= ", $dir";
		$this->filter .= ", $filter";

		$files = read_dir(SYSHOME . $dir, $filter);

		foreach ($files as $file) {
			$this->lb[$file] = $file;
		}
		asort($this->lb);
	}
}

class list_dir_extended extends listbox
{

	public $dir;
	public $filter;

	function list_dir_extended($dir, $filter = "")
	{
		$this->dir = $dir;
		$this->filter = $filter;

		parent::listbox();

		$this->lb[""] = "";

		if ($dir != "") $this->add_dir($dir, $filter);
	}

	function add_dir($dir, $filter = "")
	{
		$this->dir .= ", $dir";
		$this->filter .= ", $filter";

		$files = read_dir(SYSHOME . $dir, $filter);

		foreach ($files as $file) {
			$this->lb[$dir . "/" . $file] = $dir . "/" . $file;
		}
		asort($this->lb);
	}
}

class list_dbms extends listbox
{

	function list_dbms()
	{

		parent::listbox();

		$dir = INC_DIR . "/dbms/";
		$this->lb[""] = "";

		$files = read_dir($dir, ".class.php");

		foreach ($files as $file) {
			if ($file == "dbms.class.php") continue;
			$type = substr($file, 0, strpos($file, ".class.php"));
			$this->lb[$type] = $type;
		}
		asort($this->lb);
	}
}

class list_lang extends listbox
{
	public $dir;
	public $filter;

	function list_lang()
	{

		parent::listbox();

		$files = read_dir(SYSHOME . "/include/lang");

		foreach ($files as $file) {
			if (strpos($file, ".") === 0) continue;
			if (is_dir(SYSHOME . "/include/lang/" . $file)) $this->lb[$file] = $file;
		}
		asort($this->lb);
	}
}

class list_access extends listbox
{

	function show($field_name, $readonly, $for_search = false)
	{

		$values = get_http_param($field_name);

		if ($readonly) {
			echo "<input type='hidden' name='$field_name'>";
			echo "<select name='_" . $field_name . "' disabled='1'>\n";
		} else {
			if ($this->multiple) {
				echo "<select multiple name='" . $field_name . "[]'>\n";
			} else {
				echo "<select name='$field_name'>\n";
			}
		}

		if ($for_search) {
			echo "<option></option>";
		}

		foreach ($this->lb as $key => $value) {

			if (in_array($key, $values)) {
				echo "<option value='$key' selected>$value</option>\n";
			} else {
				echo "<option value='$key'>$value</option>\n";
			}
		}
		echo "</select>";
	}
}

class list_levels extends list_access
{

	function list_levels($multi = false)
	{
		global $MESSAGES;

		parent::listbox($multi);

		$this->lb[-1] = "";

		$this->lb[0] = $MESSAGES["SKILL_0"];
		$this->lb[3] = $MESSAGES["SKILL_3"];
		$this->lb[5] = $MESSAGES["SKILL_5"];
	}
}

class list_groups extends list_access
{

	function list_groups($multi = false)
	{

		global $global_db;

		parent::listbox($multi);
		$this->lb[-1] = "";

		$query = "select id_group, name from groups";
		$res = $global_db->dbms_query($query);

		if (!$global_db->dbms_check_result($res)) return;

		while (list($id, $name) = $global_db->dbms_fetch_row($res)) {
			$this->lb[$id] = $name;
		}

		$global_db->dbms_free_result($res);

		asort($this->lb);
	}
}

/**
 * Create a multiple select list
 *
 * For users of level 5, no rows are given
 * For users of level 3, list all users of their group
 * For users of level 0, list all users
 */
class list_users extends list_access
{

	function list_users($multi = false)
	{

		global $USER_LEVEL;
		global $USER_GROUP;
		global $global_db;

		parent::listbox($multi);

		$this->lb[-1] = "";

		if ($USER_LEVEL == 5) return;

		$query = "select id_user, username from users";
		if ($USER_LEVEL == 3) $query .= " where id_group='$USER_GROUP'";

		$res = $global_db->dbms_query($query);
		if (!$global_db->dbms_check_result($res)) return;

		while (list($id, $name) = $global_db->dbms_fetch_row($res)) {
			$this->lb["$id"] = $name;
		}

		$global_db->dbms_free_result($res);

		asort($this->lb);
	}
}
