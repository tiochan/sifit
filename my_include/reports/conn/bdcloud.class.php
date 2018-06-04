<?php
/**
 * @author Sebastian Gomez (sebastian.gomez@upcnet.es)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage Cloud Billing
 *
 * Tags definition class
 *
 */


	function & bdcloud_connect() {
		
		require_once SYSHOME . "/include/dbms/" . DBTypeCloud . ".class.php";
		
		$dbtype= DBTypeCloud . "_class";
		$db= new $dbtype() or die("ERROR: couldn't create DBMS Object");
		$db->dbms_connect(DBServerCloud, DBUserCloud, DBPassCloud, false) or die("ERROR: couldn't connect to database server");
		$db->dbms_select_db(DBNameCloud) or die("ERROR: couldn't connect to database ". DBNameCloud);

		return $db;
	}

?>
