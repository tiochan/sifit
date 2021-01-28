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

class foreign_key extends field_type
{
	public $table_referenced;
	public $field_referenced;
	public $field_shown;
	public $query_select;		// Contains the query used to get THE referenced value (1 value).
	public $query_all;
	public $query_search;
	public $restriction;
	public $lookup;
	public $db;
	public $allow_null = true;

	/**
	 * The field "field_referenced" of table "table_referenced" is referenced. Field "$field_show" is shown.
	 * Restriction is optional. If indicated is used on where clause.
	 * $selectionURL is optional, by default="". If you indicate any, it would be the page that will be launched to show the results (your own).
	 *
	 * @param string $table_referenced
	 * @param string $field_referenced
	 * @param string $field_shown
	 * @param string $restriction, default is "" (none)
	 * @param string $lookup, default is "" (none)
	 * @return reference
	 */
	public function foreign_key($db, $table_referenced, $field_referenced, $field_shown, $restriction = "", $lookup = "")
	{
		$this->table_referenced = $table_referenced;
		$this->field_referenced = $field_referenced;
		$this->field_shown = $field_shown;
		$this->restriction = $restriction;
		$this->lookup = $lookup;
		$this->db = $db;

		/*			$this->query_select= "select " . $this->field_shown . " from " . $this->table_referenced . " where " . $this->field_referenced . " like '%s' order by " . $this->field_shown;
			if($this->restriction=="") {
				$this->query_all= "select distinct " . $this->field_referenced . ", " . $this->field_shown . " from " . $this->table_referenced . " order by " . $this->field_shown;
				$this->query_search= "select " . $this->field_referenced . " from " . $this->table_referenced . " where " . $db->dbms_add_upper_function($this->field_shown) . " like '%s'";
			} else {
				$this->query_all= "select distinct " . $this->field_referenced . ", " . $this->field_shown . " from " . $this->table_referenced . " where " . $this->restriction . " order by " . $this->field_shown;
				$this->query_search= "select " . $this->field_referenced . " from " . $this->table_referenced . " where " . $db->dbms_add_upper_function($this->field_shown) . " like '%s' and " . $this->restriction;
			}
*/

		// Change done by Juan Berlanga (OpenVAS Integrator).
		// TODO: Check this change.
		$this->query_select = "select " . $this->field_shown . " from " . $this->table_referenced . " where " . $this->field_referenced . " like '%s' order by " . $this->field_shown;
		if ($this->restriction == "") {
			$this->query_all = "select " . $this->field_referenced . ", " . $this->field_shown . " from " . $this->table_referenced . " order by " . $this->field_shown;
			$this->query_search = "select " . $this->field_referenced . " from " . $this->table_referenced . " where " . $db->dbms_add_upper_function($this->field_shown) . " like '%s'";
		} else if ($this->restriction == "distinct") {
			$this->query_all = "select distinct " . $this->field_referenced . ", " . $this->field_shown . " from " . $this->table_referenced . " order by " . $this->field_shown;
			$this->query_search = "select distinct " . $this->field_referenced . " from " . $this->table_referenced . " where " . $db->dbms_add_upper_function($this->field_shown) . " like '%s'";
		} else {
			$this->query_all = "select " . $this->field_referenced . ", " . $this->field_shown . " from " . $this->table_referenced . " where " . $this->restriction . " order by " . $this->field_shown;
			$this->query_search = "select " . $this->field_referenced . " from " . $this->table_referenced . " where " . $db->dbms_add_upper_function($this->field_shown) . " like '%s' and " . $this->restriction;
		}
		// End of Juan Berlanga Change.
	}

	/**
	 *
	 * @param string $field_name
	 * @param bool $readonly
	 * @param bool $for_search
	 */
	public function show($field_name, $readonly, $for_search = false)
	{

		if ($this->lookup == "") {

			$def_value = $this->default_value != null ? $this->default_value : "";

			$query = $this->query_all;

			$res = $this->db->dbms_query($query);

			if (!$res) {
				$value = "ERROR: " . $this->db->dbms_error();
			} else {

				if ($readonly) {
					echo "<input type='hidden' name='$field_name'>";
					echo "<select name='_" . $field_name . "' disabled='1' class='readonly'>\n";
				} else {
					echo "<select class='action' name='$field_name'>\n";
				}

				// Allow to get null values..
				if ($this->allow_null) echo "<option value=''></option>";

				while (($row = $this->db->dbms_fetch_array($res))) {
					//						if($row[0] == $def_value and !$for_search) {
					if ($row[0] == $def_value) {
						echo "<option value='" . $row[0] . "' selected>" . $row[1] . "</option>\n";
					} else {
						echo "<option value='" . $row[0] . "'>" . $row[1] . "</option>\n";
					}
				}

				echo "</select>";

				$this->db->dbms_free_result($res);
			}
		} else {
			if (!$for_search) {

				include_once SYSHOME . "/include/lookups/lookup.inc.php";

				global $MESSAGES;
				echo "<input type='hidden' name='$field_name'>";
				echo "<input name='description_$field_name' readonly='1' class='readonly'>";

				if (!$readonly) {
					$onclick = "lookup_function(\"$this->lookup\",\"$field_name\",\"\");";
					$title = $MESSAGES["SEARCH"];
					if (FE_SHOW_ICONS and defined("FE_LOOKUP_ICON")) {
						echo "&nbsp;<img class='action' src='" . FE_LOOKUP_ICON . "' alt='$title' title='$title' align='absmiddle' onclick='$onclick'>";
					} else {
						echo "<input type='button' class='action' title='$title' value='...' onclick='$onclick'>\n";
					}
				}
			} else {
				echo "<input type='text' name='" . $field_name . "'";
				if ($readonly) echo " readonly='1' ";
				echo ">";
			}
		}
	}

	public function get_query_string($field_name, $field_value)
	{

		global $global_db;


		if ($this->lookup == "") {
			return parent::get_query_string($field_name, $field_value);
		}

		$query = sprintf($this->query_search, strtoupper($field_value));
		if (!($res = $global_db->dbms_query($query))) return "";

		$add = "";
		$vals = "";
		while (list($val) = $global_db->dbms_fetch_row($res)) {
			$vals .= $add . "'" . strtoupper($val) . "'";
			$add = ",";
		}

		if ($vals != "") {
			$comp = $global_db->dbms_add_upper_function($field_name) . " IN (" . $vals . ")";
		} else {
			$comp = "1=0";
		}

		$global_db->dbms_free_result($res);

		return $comp;
	}

	public function set_form_value($form_name, $field_name, $value, $visible = true)
	{
		global $global_db;

		parent::set_form_value($form_name, $field_name, $value, $visible);

		if ($this->lookup != "") {
			$value_referenced = $this->get_value($value);
			html_set_field_value($form_name, "description_" . $field_name, $value_referenced);
		}
		return 1;
	}

	public function show_simply($field_name)
	{
		$this->show($field_name, false, true);
	}

	public function get_value($field_value, $for_show = true)
	{

		if (!is_null($field_value) and ($field_value != "")) {
			$query = sprintf($this->query_select, $field_value);

			$res = $this->db->dbms_query($query);
			if (!$res) {
				$value = "ERROR: " . $this->db->dbms_error();
			} else {
				$row = $this->db->dbms_fetch_array($res);
				$value = htmlspecialchars($row[0]);
				$this->db->dbms_free_result($res);
			}
		} else {
			$value = "";
		}

		return $value;
	}

	public function get_value_from_post($field_name)
	{

		$val = parent::get_value_from_post($field_name);
		if ($val == '') {
			return null;
		}

		return $val;
	}

	public function get_sql_value($field_value)
	{

		if ($field_value == "") return "null";

		return parent::get_sql_value($field_value);
	}
}

class foreign_key_tree extends foreign_key
{
	var $table_referenced;
	var $field_referenced;
	var $field_shown;
	var $query_select;
	var $query_all;
	var $restriction;
	var $selectionURL;
	var $db;

	/**
	 * The field "field_referenced" of table "table_referenced" is referenced. Field "$field_show" is shown.
	 * Restriction is optional. If indicated is used on where clause.
	 * $selectionURL is optional, by default="". If you indicate any, it would be the page that will be launched to show the results (your own).
	 *
	 * @param string $table_referenced
	 * @param string $field_referenced
	 * @param string $field_shown
	 * @param string $restriction, default is "" (none)
	 * @param string $selectionURL, default is "" (none)
	 * @return reference
	 */
	public function foreign_key_tree($db, $table_referenced, $field_referenced, $field_shown, $restriction = "", $selectionURL = "")
	{

		parent::foreign_key($db, $table_referenced, $field_referenced, $field_shown, $restriction, $selectionURL);

		$this->query_select = "select " . $this->field_shown . ", nlevel from " . $this->table_referenced . " where " . $this->field_referenced . " like '%s' order by nleft";
		if ($this->restriction == "") {
			$this->query_all = "select " . $this->field_referenced . ", " . $this->field_shown . ", nlevel from " . $this->table_referenced . " order by nleft";
		} else {
			$this->query_all = "select " . $this->field_referenced . ", " . $this->field_shown . ", nlevel from " . $this->table_referenced . " where " . $this->restriction . "  order by nleft";
		}
	}

	public function show($field_name, $readonly, $for_search = false)
	{

		if ($this->selectionURL == "") {

			$def_value = $this->default_value != null ? $this->default_value : "";

			$query = $this->query_all;
			$res = $this->db->dbms_query($query);

			if (!$res) {
				$value = "ERROR: " . $this->db->dbms_error();
			} else {

				if ($readonly) {
					echo "<input type='hidden' name='$field_name'>";
					echo "<select name='_" . $field_name . "' disabled='1'>\n";
				} else {
					echo "<select class='action' name='$field_name'>\n";
				}

				echo "<option value=''></option>\n";

				while (($row = $this->db->dbms_fetch_array($res))) {
					if ($row[0] == $def_value and !$for_search) {
						echo "<option value='" . $row[0] . "' selected>" . $this->make_ident($row['nlevel']) . $row[1] . "</option>\n";
					} else {
						echo "<option value='" . $row[0] . "'>" . $this->make_ident($row['nlevel']) . $row[1] . "</option>\n";
					}
				}

				echo "</select>";
				$this->db->dbms_free_result($res);
			}
		} else {
			$def = $this->default_value != null ? "value='$this->default_value'" : "";

			$add = $readonly ? " readonly='1' " : "";
			echo "<input type='text' name='" . $field_name . "'$add $def>";
			if (!$readonly) {
				echo "&nbsp;<input type=button onclick=\"javascript:open('" . $this->selectionURL . "','', '')\" value='...'>";
			}
		}
	}

	//----------------------------
	// Indents structure
	//----------------------------
	public function make_ident($level)
	{
		$ident = "";
		if ($level > 1) {
			for ($i = 2; $i <= $level; $i++) {
				$ident = $ident . "&nbsp;&nbsp;&nbsp;&nbsp;";
			}
		}
		return $ident;
	}
}
