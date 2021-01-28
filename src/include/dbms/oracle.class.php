<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage dbms
 *
 * Class for ORACLE DBMS functions.
 */

include_once INC_DIR . "dbms/dbms.class.php";

// Deprecated: dl("oci8.so");
!extension_loaded("oci8") and die("oci8 extension not loaded.");



class oracle_class extends dbms_class
{

	function __construct()
	{
		$this->prevent_errors = false;
	}

	function __destruct()
	{
		if ($this->dbms_is_connected()) @$this->dbms_close();
	}

	final public function dbms_connect($server, $username, $password, $persistent = false, $new_link = false)
	{

		// For oracle, connection will be done when selecting database
		$this->sgbd_link = 1;

		$this->connected = true;
		$this->server = $server;
		$this->username = $username;
		$this->password = $password;
		$this->persistent = $persistent;

		return $this->sgbd_link;
	}


	final public function dbms_is_connected()
	{

		return (isset($this->sgbd_link) and $this->sgbd_link);
	}


	final public function dbms_reconnect()
	{

		if ($this->db_name = "") return false;

		if ($this->persistent) {
			$this->sgbd_link = oci_pconnect($this->username, $this->password, "//" . $this->server . "/" . $this->db_name);
		} else {
			$this->sgbd_link = oci_connect($this->username, $this->password, "//" . $this->server . "/" . $this->db_name);
		}

		if ($this->sgbd_link) $this->connected = true;
		else $this->connected = false;

		return $this->sgbd_link;
	}


	final public function dbms_select_db($database)
	{

		if (!$this->dbms_is_connected()) return 0;

		if ($this->persistent) {
			$this->sgbd_link = oci_pconnect($this->username, $this->password, "//" . $this->server . "/" . $database);
		} else {
			$this->sgbd_link = oci_connect($this->username, $this->password, "//" . $this->server . "/" . $database);
		}
		$db_ret = $this->sgbd_link;

		/*if(DEBUG) oci_internal_debug(1);
			else oci_internal_debug(0);*/

		$this->db_name = $database;
		return $db_ret;
	}

	final public function dbms_select_last_db()
	{

		if (!$this->dbms_is_connected()) return 0;

		if (trim($this->db_name) == "") return 0;

		// Oracle connection is done against a database.
		return true;
	}


	final public function dbms_close()
	{

		if (!$this->dbms_is_connected()) return 0;

		@oci_close($this->sgbd_link);
		$this->connected = false;

		return 1;
	}


	final public function &dbms_query($sql_query, $seq = null)
	{

		if (!$this->dbms_is_connected()) return 0;

		$this->show_queryInfo($sql_query);
		$this->log_query($sql_query);

		$this->res = false;

		//$sql_query=$this->dbms_escape_string($sql_query);
		if (!empty($seq)) $sql_query = str_replace("#SEQ#", $seq . ".nextval", $sql_query);

		$pos = stripos($sql_query, "select");

		if ($pos === false or $pos != 0) {
			// Oracle cant manage literals > 4000 bytes. In these cases must use bind variables.
			// Check for literals and replace with bind variables:
			$bind_vars = array();

			$this->dbms_convert_to_bind($sql_query, $bind_vars);
			if ($statement = oci_parse($this->sgbd_link, $sql_query)) {

				foreach ($bind_vars as $key => $value) {
					oci_bind_by_name($statement, $key, $value);
				}
			}
		} else {
			if (!($statement = oci_parse($this->sgbd_link, $sql_query))) return false;
		}

		if (!oci_execute($statement, OCI_DEFAULT)) return false;

		$this->res = $statement;

		$this->last_was_select = !(stripos($sql_query, "select") === false);

		if (!$this->res) $this->show_queryError($sql_query, $this->dbms_error());

		return $this->res;
	}

	final public function dbms_check_result($res)
	{

		if (!$this->dbms_is_connected()) return 0;

		return ($res and ($this->dbms_num_rows($res) > 0));
	}


	final public function dbms_error()
	{

		if (!$this->dbms_is_connected()) return 0;

		return $ret = oci_error();
	}


	final public function dbms_data_seek($res, $row)
	{

		if (!$this->dbms_is_connected()) return 0;

		return false;
	}


	final public function dbms_free_result($res)
	{

		if (!$this->dbms_is_connected()) return 0;

		$ret = !myempty($res) ? oci_free_statement($res) : true;
		return $ret;
	}

	final public function dbms_row_count($table, $where = "")
	{

		if (!isset($this->sgbd_link)) return 0;

		$query = "select count(*) from $table";
		if ($where != "") $query .= " where $where";

		if (!$res = $this->dbms_query($query)) return false;
		if (!(list($rows) = $this->dbms_fetch_row($res))) return false;
		$this->dbms_free_result($res);

		return $rows;
	}

	final public function dbms_num_rows($res)
	{

		if (!$this->dbms_is_connected()) return 0;

		// Oracle final public function oci_num_rows returns the affected rows of last executed query.
		// If the query was a select, this final public function will return the number of fetched rows.
		return $this->last_was_select ? 1 : oci_num_rows($res);
	}


	final public function dbms_num_fields($res)
	{

		if (!$this->dbms_is_connected()) return 0;

		return oci_num_fields($res);
	}

	final public function dbms_field_name($res, $field)
	{

		if (!$this->dbms_is_connected()) return 0;

		// In oracle, first field is 1, not 0.
		return oci_field_name($res, $field + 1);
	}

	final public function dbms_field_type($res, $field)
	{

		if (!$this->dbms_is_connected()) return 0;

		// In oracle, first field is 1, not 0.
		return oci_field_type($res, $field + 1);
	}


	final public function &dbms_fetch_row($res)
	{

		if (!$this->dbms_is_connected()) return 0;

		$ret = oci_fetch_array($res, OCI_NUM + OCI_RETURN_NULLS);

		return $ret;
	}

	final public function &dbms_fetch_array($res)
	{

		if (!$this->dbms_is_connected()) return 0;

		$ret = null;
		$ret1 = oci_fetch_array($res, OCI_BOTH + OCI_RETURN_NULLS);
		if ($ret1) {
			$ret2 = array_change_key_case($ret1, CASE_LOWER);
			$ret = array_merge($ret1, $ret2);
		}

		return $ret;
	}

	final public function &dbms_fetch_object($res)
	{

		if (!$this->dbms_is_connected()) return 0;

		if ($ret = oci_fetch_object($res)) {
			// Convert UPPERCASE TO LOWERCASE.. nasty Oracle!
			$vars = get_object_vars($ret);
			foreach ($vars as $key => $value) {
				$low_key = strtolower($key);
				$ret->$low_key = $ret->$key;
			}
		}

		return $ret;
	}

	final public function dbms_affected_rows()
	{

		if (!$this->dbms_is_connected()) return 0;

		return oci_num_rows($this->res);
	}

	final public function &dbms_get_tables()
	{

		if (!$this->dbms_is_connected()) return 0;

		die("Function not defined... still. Can you do it?");
	}

	final public function &dbms_get_cols($table)
	{

		if (!$this->dbms_is_connected()) return 0;

		die("Function not defined... still. Can you do it?");
	}


	final public function dbms_begin_transaction()
	{

		if (!$this->dbms_is_connected()) return 0;

		// Oracle starts transaction implicitly.
		return true;
	}

	final public function dbms_commit()
	{

		if (!$this->dbms_is_connected()) return 0;

		return oci_commit($this->sgbd_link);
	}

	final public function dbms_rollback()
	{

		if (!$this->dbms_is_connected()) return 0;

		return oci_rollback($this->sgbd_link);
	}

	final public function &dbms_primary_keys($table)
	{

		if (!$this->dbms_is_connected()) return 0;

		die("method dbms_primary_keys not defined!");
	}

	final public function dbms_concat($separator, $fields)
	{

		if (!$this->dbms_is_connected()) return 0;

		$arg_list = func_get_args();

		$pre = "";
		$ret = "";
		for ($i = 1; $i < count($arg_list); $i++) {
			$ret .= $pre . $arg_list[$i];
			$pre = " || '$separator' || ";
		}

		return $ret;
	}

	final public function dbms_to_date($date, $format = "")
	{

		// Oracle: select to_date('30/12/2006 23:50:51','DD/MM/YYYY HH24:MI:SS') from dual
		if ($format == "") {
			switch (DATE_FORMAT) {
				case "d-m-y":
					$format = "DD/MM/YYYY HH24:MI:SS";
					break;
				case "m-d-y":
					$format = "MM/DD/YYYY HH24:MI:SS";
					break;
				case "y-m-d":
					$format = "YYYY/MM/DD HH24:MI:SS";
					break;
				default:
					$format = "DD/MM/YYYY HH24:MI:SS";
					break;
			}
		}

		$ret = "TO_DATE('" . $date . "','" . $format . "')";

		return $ret;
	}

	final public function dbms_date_to_string($date, $format = "", $use_time = true)
	{

		// Oracle: to_char(data_creacio,'DD/MM/YYYY HH24:MI:SS')
		if ($format == "") {
			switch (DATE_FORMAT) {
				case "d-m-y":
					$format = "DD/MM/YYYY HH24:MI:SS";
					break;
				case "m-d-y":
					$format = "MM/DD/YYYY HH24:MI:SS";
					break;
				case "y-m-d":
					$format = "YYYY/MM/DD HH24:MI:SS";
					break;
				default:
					$format = "DD/MM/YYYY HH24:MI:SS";
					break;
			}
		}

		$ret = "TO_CHAR(" . $date . ",'" . $format . "')";

		return $ret;
	}

	final public function dbms_limit($query, $numrows, $offset = 0)
	{

		if (!$this->dbms_is_connected()) return 0;

		$ret = "select * from ( " .
			" select a.*, rownum as tmp_row_number " .
			" from ( $query ) a where rownum <= " . ($numrows + $offset) . " " .
			") where tmp_row_number >= " . ($offset + 1);

		return $ret;
	}

	final public function dbms_sequence($sequence_name)
	{

		if (!$this->dbms_is_connected()) return 0;

		return $sequence_name . ".nextval";
	}

	final public function dbms_convert_to_bind(&$sql_query, &$bind_vars)
	{

		$replacement_string = "{[]}";

		$cont = 0;
		$sql_query = str_replace("''", $replacement_string, $sql_query);
		$offset = 0;
		while (($start = strpos($sql_query, "'", $offset)) !== false) {

			//				$end= strpos($sql_query,"'",$start + 1);
			if (($end = strpos($sql_query, "'", $start + 1)) === false) break;

			// Limit is 4000, if greater the must use bind vars.
			if (($end - $start) < 4000) {
				$offset = $end + 1;
				continue;
			}

			$inicio = substr($sql_query, 0, $start);
			$literal = substr($sql_query, $start + 1, $end - $start - 1);
			$fin = substr($sql_query, $end + 1);

			$var_name = ":var_$cont";
			$cont++;
			$sql_query = $inicio . $var_name . $fin;
			$bind_vars[$var_name] = str_replace($replacement_string, "''", $literal);
		}
		$sql_query = str_replace($replacement_string, "''", $sql_query);
	}

	final public function dbms_now()
	{

		if (!$this->dbms_is_connected()) return 0;

		$aux = $this->dbms_query("select to_char(sysdate,'dd/mm/yy hh24:mi:ss') from dual");
		list($ret) = $this->dbms_fetch_row($aux);

		return $ret;
	}

	final public function dbms_escape_string($string)
	{

		if (!$this->dbms_is_connected()) return 0;

		$strings_to_escape = array(
			"'" => "''"
		);

		$ret = stripslashes($string);

		foreach ($strings_to_escape as $to_search => $to_replace) {
			$ret = str_replace($to_search, $to_replace, $ret);
		}

		return $ret;
	}

	final public function dbms_add_upper_function($field_name)
	{

		return "UPPER($field_name)";
	}

	final public function show_queryError($sql_query, $sql_error)
	{
		if (!$this->prevent_errors) return;
		if (DEBUG) html_showInfo("<table border=1><th colspan=2><font size='+1'>Error in query:</font></th><tr><td><b>Query</b></td><td><font color='gray'>$sql_query</font></td></tr><tr><td><b>Error</b></td><td><font color='red'>$sql_error</font></td></tr></table>");
	}

	final public function dbms_query_append($query, $sentence, $conector = "and")
	{

		if (trim($query) == "") return $sentence;
		if (trim($sentence) == "") return $query;
		return "$query $conector $sentence";
	}

	final public function dbms_parse_search_query($field_name, $field_value)
	{

		$query_str = "";
		$parts = explode(" OR ", $field_value);
		$or_sep = "";

		foreach ($parts as $val) {

			$subparts = explode(" AND ", $val);
			$and_sep = "";
			$sub_query_str = "";

			foreach ($subparts as $subval) {
				$sub_query_str .= $and_sep . $field_name . " like '" . $subval . "'";
				$and_sep = " AND ";
			}

			$query_str .= $or_sep . "(" . $sub_query_str . ")";
			$or_sep = " OR ";
		}

		return $query_str;
	}

	final public function dbms_create_database($database_name)
	{

		die("method dbms_create_database not defined!");
	}

	final public function dbms_drop_database($database_name)
	{

		die("method dbms_drop_database not defined!");
	}

	final public function dbms_grant_database_access($database_name, $table_name, $user, $passwd, $priv)
	{

		die("method dbms_grant_database_access not defined!");
	}

	final public function dbms_is_valid_object_name($name)
	{

		global $translate;

		foreach ($translate as $key => $value) {
			if (strpos($name, $key) !== false) return false;
		}

		for ($i = 0; $i < strlen($name); $i++) {
			$ord = ord($name[$i]);

			if (($ord >= 65 and $ord <= 90) or
				($ord >= 97 and $ord <= 122) or
				($ord >= 48 and $ord <= 57) or
				($name[$i] == "_")
			) {
			} else {
				return false;
			}
		}

		return true;
	}

	final public function dbms_get_valid_object_name($name)
	{

		$ret = "";

		for ($i = 0; $i < strlen($name); $i++) {
			$ord = ord($name[$i]);

			if (($ord >= 65 and $ord <= 90) or ($ord >= 97 and $ord <= 122) or ($name[$i] == "_")) {
			} else {
				$ret .= $name[$i];
			}
		}

		return $ret;
	}

	final public function dbms_left($string, $lenght)
	{
		die("ERROR: oracle.class.php::dbms_left not implemented yet");
	}

	final public static function dbms_get_current_timestamp_query()
	{
		return "CURRENT_DATE";
	}

	final public static function dbms_date_add_query($date, $days)
	{
		$interval = $days > 0 ? " + $days" : " - $days";
		return "$date $interval";
	}
}
