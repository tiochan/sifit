<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage dbms
 *
 * Class for POSTGRES DBMS functions.
 */

	include_once INC_DIR . "dbms/dbms.class.php";

	class pgsql_class extends dbms_class {

		function __construct () {
			$this->prevent_errors=false;
		}

		function __destruct() {
			if($this->dbms_is_connected()) @$this->dbms_close();
		}

		final public function dbms_connect($server, $username, $password, $persistent=false, $new_link=false) {

			// For postgress, connection will be done when selecting database
			$this->sgbd_link=1;

			$this->connected=true;
			$this->server= $server;
			$this->username= $username;
			$this->password= $password;
			$this->persistent= $persistent;

			return $this->sgbd_link;
		}


		final public function dbms_is_connected() {

			return (isset($this->sgbd_link) and $this->sgbd_link);
		}


		final public function dbms_reconnect() {

			if($this->conn_string="") return false;
			if($this->persistent) {
				$this->sgbd_link= pg_pconnect($this->conn_string);
			} else {
				$this->sgbd_link= pg_connect($this->conn_string);
			}


			if($this->sgbd_link) $this->connected=true;
			else $this->connected=false;

			return $this->sgbd_link;
		}


		final public function dbms_select_db($database) {

			if(!$this->dbms_is_connected()) return 0;

			$this->conn_string = "host='" . $this->server . "' port='5432' dbname='$database' user='" . $this->username . "' password='" . $this->password . "'";

			if($this->persistent) {
				$this->sgbd_link= pg_pconnect($this->conn_string);
			} else {
				$this->sgbd_link= pg_connect($this->conn_string);
			}

			$db_ret= $this->sgbd_link;
			$this->dbms_query("set datestyle=SQL,DMY");

			$this->db_name= $database;
			return $db_ret;
		}

		final public function dbms_select_last_db() {

			if(!$this->dbms_is_connected()) return 0;

			if(trim($this->db_name)=="") return 0;

			// Postgress connect to database at pg_connect, not with a select_db method.
			return 1;
		}


		final public function dbms_close() {

			if(!$this->dbms_is_connected()) return 0;

			@pg_close($this->sgbd_link);
			$this->connected=false;

			return 1;
		}


		final public function &dbms_query($sql_query,$seq=null) {

			if(!$this->dbms_is_connected()) return 0;

			$this->log_query($sql_query);

			$this->res= false;

			//$sql_query=$this->dbms_escape_string($sql_query);
			$time_start = getmicrotime();

			if (!empty($seq)) $sql_query=str_replace("#SEQ#","nextval('".$seq."')",$sql_query);
			$this->res = pg_query($this->sgbd_link, $sql_query);

			$time_end = getmicrotime();
			$time = round($time_end - $time_start, 3);
			$this->show_queryInfo($sql_query, $time);

			$this->last_was_select = !(stripos($sql_query,"select") === false);

			if(!$this->res) $this->show_queryError($sql_query, $this->dbms_error());

			return $this->res;
		}

		final public function dbms_check_result($res) {

			if(!$this->dbms_is_connected()) return 0;

			return($res and ($this->dbms_num_rows($res) > 0));
		}


		final public function dbms_error() {

			if(!$this->dbms_is_connected()) return 0;

			return pg_last_error($this->sgbd_link);
		}


		final public function dbms_data_seek($res, $row) {

			if(!$this->dbms_is_connected()) return 0;

			return pg_result_seek($res, $row);
		}


		final public function dbms_free_result($res) {

			if(!$this->dbms_is_connected()) return 0;

			return pg_free_result($res);
		}

		final public function dbms_row_count($table, $where="") {

			if(!$this->dbms_is_connected()) return 0;

			$query= "select count(*) from $table";
			if($where != "") $query .= " where $where";

			if(!$res= $this->dbms_query($query)) return false;
			if(!(list($rows)= $this->dbms_fetch_row($res))) return false;
			$this->dbms_free_result($res);

			return $rows;
		}

		final public function dbms_num_rows($res) {

			if(!$this->dbms_is_connected()) return 0;

			return pg_num_rows($res);
		}


		final public function dbms_num_fields($res) {

			if(!$this->dbms_is_connected()) return 0;

			return pg_num_fields($res);
		}

		final public function dbms_field_name($res, $field) {

			if(!$this->dbms_is_connected()) return 0;

			return pg_field_name($res, $field);
		}

		final public function dbms_field_type($res, $field) {

			if(!$this->dbms_is_connected()) return 0;

			return pg_field_type($res, $field);
		}


		final public function &dbms_fetch_row($res) {

			if(!$this->dbms_is_connected()) return 0;

			$ret= pg_fetch_row($res);
			return $ret;
		}

		final public function &dbms_fetch_array($res) {

			if(!$this->dbms_is_connected()) return 0;

			$ret= pg_fetch_array($res);

			return $ret;
		}

		final public function &dbms_fetch_object($res) {

			if(!$this->dbms_is_connected()) return 0;

			$ret= pg_fetch_object($res);
			return $ret;
		}

		final public function dbms_affected_rows() {

			if(!$this->dbms_is_connected()) return 0;

			return pg_affected_rows($this->res);
		}

		final public function &dbms_get_tables() {

			if(!$this->dbms_is_connected()) return 0;

			$tables= Array();
			$query= "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'";

			$res= pg_query($this->sgbd_link, $query);

			$i=0;
			while($row= pg_fetch_row($res)) {
				$tables[$i++]= $row[0];
			}

			return $tables;
		}

		final public function &dbms_get_cols($table) {

			if(!$this->dbms_is_connected()) return 0;

			$cols= Array();
			$query="SELECT column_name FROM information_schema.columns WHERE table_name ='$table'";
			$res=mssql_query($query);

			$i=0;
			while($row= pg_fetch_row($res)) {
				$cols[$i++]= $row[0];
			}

			return $cols;
		}


		final public function dbms_begin_transaction() {

			if(!$this->dbms_is_connected()) return 0;

			$ret= pg_query($this->sgbd_link, "begin");
			$this->res= $ret;

			return $ret;
		}

		final public function dbms_commit() {

			if(!isset($this->sgbd_link)) return 0;

			$ret= pg_query($this->sgbd_link, "commit");
			$this->res= $ret;

			return $ret;
		}

		final public function dbms_rollback() {

			if(!isset($this->sgbd_link)) return 0;

			//if(DEBUG) echo "rollback work.<br>";

			$ret= pg_query($this->sgbd_link, "rollback");
			$this->res= $ret;

			return $ret;
		}

		final public function &dbms_primary_keys($table) {

			die("method dbms_primary_keys not defined!");
		}

		final public function dbms_concat($separator, $fields) {

			if(!$this->dbms_is_connected()) return 0;

			$arg_list = func_get_args();

			$pre="";
			$ret="";
			for($i=1; $i < count($arg_list); $i++) {
				$ret.= $pre . $arg_list[$i];
				$pre=" || '$separator' || ";
			}

			return $ret;
		}

		final public function dbms_to_date($date, $format="") {

			// Psql: select to_timestamp('16/03/2006 23:50:51','DD/MM/YYYY HH:MI:SS');
			if($format=="") {
				switch(DATE_FORMAT) {
					case "d-m-y":
						$format= "DD/MM/YYYY HH:MI:SS";
						break;
					case "m-d-y":
						$format= "MM/DD/YYYY HH:MI:SS";
						break;
					case "y-m-d":
						$format= "YYYY/MM/DD HH:MI:SS";
						break;
					default:
						$format= "DD/MM/YYYY HH:MI:SS";
						break;
				}
			}
			$ret= "TO_TIMESTAMP('" . $date . "','" . $format . "')";

			return $ret;
		}

		final public function dbms_date_to_string($date, $format="", $use_time=true) {

			// Psql: to_char(data_creacio,'DD/MM/YYYY HH24:MI:SS')
			if($format=="") {
				switch(DATE_FORMAT) {
					case "d-m-y":
						$format= "DD/MM/YYYY HH:MI:SS";
						break;
					case "m-d-y":
						$format= "MM/DD/YYYY HH:MI:SS";
						break;
					case "y-m-d":
						$format= "YYYY/MM/DD HH:MI:SS";
						break;
					default:
						$format= "DD/MM/YYYY HH:MI:SS";
						break;
				}
			}
			$ret= "TO_CHAR(" . $date . ",'" . $format . "')";

			return $ret;
		}

		final public function dbms_limit($query, $numrows, $offset=0) {

			if(!$this->dbms_is_connected()) return 0;

			$ret= $query . " LIMIT " . $numrows;
			if($offset) $ret.= " OFFSET " . $offset;

			return $ret;
		}

		final public function dbms_sequence($sequence_name) {

			if(!$this->dbms_is_connected()) return 0;

			// $ret= "next('" . $sequence_name . "')";
			return false;
		}

		final public function dbms_convert_to_bind(&$sql_query, &$bind_vars) {

			die("method dbms_convert_to_bind not defined!");
		}

		final public function dbms_now() {

			if(!$this->dbms_is_connected()) return 0;

			$aux= $this->dbms_query("select now()");
			list($ret) = $this->dbms_fetch_row($aux);

			return $ret;
		}

		final public function dbms_escape_string($string) {

			if(!$this->dbms_is_connected()) return 0;

			$ret= addslashes($string);

			return $ret;
		}

		final public function dbms_add_upper_function($field_name) {

			return "UPPER($field_name)";
		}

		final public function show_queryError($sql_query, $sql_error) {
			if(!$this->prevent_errors) return;
			if(DEBUG) html_showInfo("<table border=1><th colspan=2><font size='+1'>Query erronia</font></th><tr><td><b>Query</b></td><td><font color='gray'>$sql_query</font></td></tr><tr><td><b>Error</b></td><td><font color='red'>$sql_error</font></td></tr></table>");
		}

		final function dbms_query_append($query, $sentence, $conector="and") {

			if(trim($query) == "") return $sentence;
			if(trim($sentence) == "") return $query;
			return "$query $conector $sentence";
		}

		final public function dbms_parse_search_query($field_name, $field_value) {

			$query_str="";
			$parts=explode(" OR ", $field_value);
			$or_sep="";

			foreach($parts as $val) {

				$subparts=explode(" AND ", $val);
				$and_sep="";
				$sub_query_str="";

				foreach($subparts as $subval) {
					$sub_query_str.= $and_sep . $field_name . " like '" . $subval . "'";
					$and_sep=" AND ";
				}

				$query_str.= $or_sep . "(" . $sub_query_str . ")";
				$or_sep=" OR ";
			}

			return $query_str;
		}

		final public function dbms_create_database($database_name) {

			die("method dbms_create_database not defined!");
		}

		final public function dbms_drop_database($database_name) {

			die("method dbms_drop_database not defined!");
		}

		final public function dbms_grant_database_access($database_name, $table_name, $user, $passwd, $priv) {

			die("method dbms_grant_database_access not defined!");
		}

		final public function dbms_is_valid_object_name($name) {

            global $translate;

			foreach($translate as $key => $value) {
				if(strpos($name, $key) !== false) return false;
			}

			for($i=0; $i < strlen($name); $i++) {
				$ord= ord($name[$i]);

				if( ($ord >= 65 and $ord <= 90) or
					($ord >= 97 and $ord <= 122) or
					($ord >= 48 and $ord <= 57) or
					($name[$i]=="_")) {
				} else {
					return false;
				}
			}

			return true;
		}

		final public function dbms_get_valid_object_name($name) {

			$ret="";

			for($i=0; $i < strlen($name); $i++) {
				$ord= ord($name[$i]);

				if(($ord >= 65 and $ord <= 90) or ($ord >= 97 and $ord <= 122) or ($name[$i]=="_")) {
				} else {
					$ret.=$name[$i];
				}
			}

			return $ret;
		}

		final public function dbms_left($string, $lenght) {
			return "SUBSTRING($string FOR $lenght)";
		}

		final public static function dbms_get_current_timestamp_query() {
			return "CURRENT_DATE";
		}

		final public static function dbms_date_add_query($date, $days) {
			$interval= $days > 0 ? " + INTEGER $days" : " - INTEGER $days";
			return "$date $interval";
		}
	}
