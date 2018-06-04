<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package marks
 * @subpackage keys
 *
 * unique key
 *
 *
 */

/*
 * mark_keys table;
	+----------+--------------+------+-----+---------+----------------+
	| Field    | Type         | Null | Key | Default | Extra          |
	+----------+--------------+------+-----+---------+----------------+
	| id       | mediumint(9) | NO   | PRI | NULL    | auto_increment |
	| user_id  | mediumint(9) | NO   | UNI | NULL    |                |
	| user_key | varchar(255) | YES  |     | NULL    |                |
	+----------+--------------+------+-----+---------+----------------+
 */

	function generate_new_key() {

		global $USER_ID, $global_db;


		$new_keys= rand(10000, 20000) . "_" . rand(20000, 30000) . "_" . rand(30000, 40000);

		$query= "select * from mark_keys where user_id='$USER_ID'";
		$res=$global_db->dbms_query($query);

		if($res and $global_db->dbms_num_rows($res) > 0) {
			$global_db->dbms_free_result($res);
			$query= "update mark_keys set user_key = '$new_keys' where user_id = '$USER_ID'";
		} else {
			$query= "insert into mark_keys (user_id,user_key) values ('$USER_ID','$new_keys')";
		}

		$res=$global_db->dbms_query($query);

		if($res) {
			return $new_keys;
		} else {
			echo $global_db->dbms_error();
			return false;
		}
	}

	function get_user_from_key($key) {

		global $global_db;

		$query= "select user_id from mark_keys where user_key='$key'";
		$res=$global_db->dbms_query($query);

		if($res) {
			list($user_id)= $global_db->dbms_fetch_row($res);
			$global_db->dbms_free_result($res);
			return $user_id;
		} else {
			return false;
		}
	}

	function get_key_by_user($user_id) {

		global $global_db;

		$query= "select user_key from mark_keys where user_id='$user_id'";
		$res=$global_db->dbms_query($query);

		if($res) {
			list($key)= $global_db->dbms_fetch_row($res);
			$global_db->dbms_free_result($res);
			return $key;
		} else {
			return false;
		}
	}
?>
