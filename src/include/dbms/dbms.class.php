<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage dbms
 *
 * Abstract class for platform independent DBMS functions.
 */

include_once INC_DIR . "log.inc.php";
include_once INC_DIR . "html.inc.php";
include_once INC_DIR . "crono.inc.php";


abstract class dbms_class
{

	protected $db_type;
	protected $new_link;
	protected $sgbd_link;
	protected $connected = false;
	protected $server;
	protected $username;
	protected $password;
	protected $persistent;
	protected $db_name;
	protected $res;
	protected $conn_string = "";
	protected $prevent_errors;
	protected $sem_log = false;

	public $last_was_select = false;

	abstract public function dbms_connect($server, $username, $password, $persistent = false, $new_link = false);
	abstract public function dbms_is_connected();
	abstract public function dbms_reconnect();
	abstract public function dbms_select_db($database);
	abstract public function dbms_select_last_db();
	abstract public function dbms_close();
	abstract public function &dbms_query($sql_query, $seq = null);
	abstract public function dbms_check_result($res);
	abstract public function dbms_error();
	abstract public function dbms_data_seek($res, $row);
	abstract public function dbms_free_result($res);
	abstract public function dbms_row_count($table, $where = "");
	abstract public function dbms_num_rows($res);
	abstract public function dbms_num_fields($res);
	abstract public function dbms_field_name($res, $field);
	abstract public function dbms_field_type($res, $field);
	abstract public function &dbms_fetch_row($res);
	abstract public function &dbms_fetch_array($res);
	abstract public function &dbms_fetch_object($res);
	abstract public function dbms_affected_rows();
	abstract public function &dbms_get_tables();
	abstract public function &dbms_get_cols($table);
	abstract public function dbms_begin_transaction();
	abstract public function dbms_commit();
	abstract public function dbms_rollback();
	abstract public function &dbms_primary_keys($table);
	abstract public function dbms_concat($separator, $fields);
	abstract public function dbms_to_date($date, $format = "");
	abstract public function dbms_date_to_string($date, $format = "", $use_time = true);
	abstract public function dbms_limit($query, $numrows, $offset = 0);
	abstract public function dbms_sequence($sequence_name);
	abstract public function dbms_convert_to_bind(&$sql_query, &$bind_vars);
	abstract public function dbms_now();
	abstract public function dbms_escape_string($string);
	abstract public function dbms_add_upper_function($field_name);
	abstract public function dbms_query_append($query, $sentence, $conector = "and");
	abstract public function dbms_parse_search_query($field_name, $field_value);
	abstract public function dbms_create_database($database_name);
	abstract public function dbms_drop_database($database_name);
	abstract public function dbms_is_valid_object_name($string);
	abstract public function dbms_get_valid_object_name($string);
	abstract public function dbms_left($string, $lenght);
	abstract public static function dbms_get_current_timestamp_query();
	abstract public static function dbms_date_add_query($date, $interval);

	/**
	 * Grants the database access to the user. Revoke all previous rights.
	 * Returns true if all went ok, false else.
	 *
	 * @param string $database_name
	 * @param string $table_name
	 * @param string $user
	 * @param string $passwd Clear password
	 * @param string $priv [NONE|RO|RW|ALL]
	 * @return boolean
	 */
	abstract public function dbms_grant_database_access($database_name, $table_name, $user, $passwd, $priv);

	protected function show_queryInfo($sql_query, $time = false)
	{
		if (!headers_sent()) return;
		$add = ($time !== false) ? "<tr><td><b>Execution time:</b></td><td>$time</td></tr>" : "";
		if (DEBUG_QUERY) html_showInfo("<table border=1><th colspan=2><font size='+1'>Executing query:</font></th><tr><td><b>Query:</b></td><td><font color='gray'>$sql_query</font></td></tr>$add</table>");
	}

	protected function show_queryResult($sql_result)
	{
		if (DEBUG_QUERY) html_showInfo("<table border=1><tr><td><b>Result:</b></td><td><font color='gray'>$sql_result</font></td></tr></table>");
	}

	protected function show_queryError($sql_query, $sql_error)
	{
		if (!$this->prevent_errors) return;
		if (DEBUG) html_showInfo("<table border=1><th colspan=2><font size='+1'>Error in query:</font></th><tr><td><b>Query</b></td><td><font color='gray'>$sql_query</font></td></tr><tr><td><b>Error</b></td><td><font color='red'>$sql_error</font></td></tr></table>");
	}

	protected function log_query($sql_query)
	{

		if ($this->sem_log) return;		// No wait to end of execution. Is needed to avoid circular calls from log module

		$upper_query = strtoupper($sql_query);
		if (strpos($upper_query, "INSERT INTO LOG") === 0) return;

		$search_r = array("INSERT", "UPDATE", "DELETE", "CREATE", "ALTER", "DROP");


		$found = false;					// If the query has no interest, then dont log it.
		foreach ($search_r as $search) {
			$found = (strpos($upper_query, $search) === 0);
			if ($found) break;
		}
		if (!$found) return;

		$this->sem_log = true;
		log_write("DBMS", $sql_query, 2);
		$this->sem_log = false;
	}

	public function dbms_check_injection($query)
	{

		if (
			preg_match("/\</", strtoupper($query), $matches) ||
			preg_match("/\>/", strtoupper($query), $matches) ||
			preg_match("/\;/", strtoupper($query), $matches) ||
			preg_match("/UNION/", strtoupper($query), $matches) ||
			preg_match("/ALL/", strtoupper($query), $matches) ||
			preg_match("/OR/", strtoupper($query), $matches) ||
			preg_match("/AND/", strtoupper($query), $matches) ||
			preg_match("/ORDER/", strtoupper($query), $matches) ||
			preg_match("/SELECT/", strtoupper($query), $matches) ||
			preg_match("/WHERE/", strtoupper($query), $matches)
		) {
			return 1;
		}
		return 0;
	}
}
