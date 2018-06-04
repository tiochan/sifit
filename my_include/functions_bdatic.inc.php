<?php
/**
 * @author Sebastian Gomez (sebastian.gomez@upcnet.es)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage ATIC
 *
 * Tags definition class
 *
 */


	function db_atic_connect(&$db) {
		
		require_once SYSHOME . "/include/dbms/" . DBTypeATIC . ".class.php";
		
		$dbtype= DBTypeATIC . "_class";
		$db= new $dbtype() or die("ERROR: couldn't create DBMS Object");
		$db->dbms_connect(DBServerATIC, DBUserATIC, DBPassATIC, false) or die("ERROR: couldn't connect to database server");
		$db->dbms_select_db(DBNameATIC) or die("ERROR: couldn't connect to database ". DBNameATIC);
	}

?>
