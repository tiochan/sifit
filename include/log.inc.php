<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage log
 *
 * Require:
 * - init.inc.php loaded
 *
 * Write logs to the database.
 *
 * Log levels:
 *
 * - 0 - only database changes
 * - 3 - level 0 + auth events
 * - 5 - Log all events (includes navigation)
 */


	function log_write($module, $msg, $log_lvl=5) {

		global $USER_GROUP;
		global $USER_ID;
		global $global_db;

		if(!defined("AUDIT") or !AUDIT or !defined("AUDIT_LVL") or ($log_lvl > AUDIT_LVL)) return;

		$usr_id= isset($_SESSION[APP_NAME . '_id_user']) ? $_SESSION[APP_NAME . '_id_user']: -1;
		$usr_lv= isset($_SESSION[APP_NAME . '_level']) ? $_SESSION[APP_NAME . '_level']: -1;
		$usr_nm= isset($_SESSION[APP_NAME . '_username']) ? $_SESSION[APP_NAME . '_username']: -1;
		$rem_ad= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR']: "127.0.0.1";

		$msg= $global_db->dbms_escape_string($msg);

		$query= "insert into log (id_user, username, user_level, host, module, action) values ";
		$query.="('$usr_id', '$usr_nm', $usr_lv, '$rem_ad', '$module', '$msg')";

		$global_db->dbms_query($query);
	}
