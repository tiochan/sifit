<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage groups
 *
 * Datawindow class for groups management.
 */


/*
	Table definition

	+-------------+--------------+------+-----+---------+----------------+
	| Field       | Type         | Null | Key | Default | Extra          |
	+-------------+--------------+------+-----+---------+----------------+
	| id_group    | mediumint(9) |      | PRI | NULL    | auto_increment |
	| name        | varchar(30)  |      |     |         |                |
	| description | text         | YES  |     | NULL    |                |
	+-------------+--------------+------+-----+---------+----------------+
*/

	include_once INC_DIR . "/forms/forms.inc.php";
	include_once INC_DIR . "/forms/field_ext.inc.php";
	include_once INC_DIR . "/forms/form_elements/datawindow_ext.inc.php";

	class dw_group extends datawindow_ext {

		public function dw_group(&$optional_db=null) {

			global $MESSAGES;


			// Datawindow Query
			$qry= new datawindow_query();

			// Fields
			$fields= Array();

			$fields[0]= new field_ext("groups.id_group","","auto",false,true,0,false);
			$fields[0]->is_detail=false;
			$fields[1]= new field_ext("groups.name",$MESSAGES["GROUP_FIELD_NAME"],"fstring",true,true,1,true);
			$fields[1]->default_order_by="a";
			$fields[2]= new field_ext("groups.description",$MESSAGES["GROUP_FIELD_DESCRIPTION"],"fstring",false,false,2,true);

			$fields[1]->can_order= true;

			// Creation of table and add it to query
			$table_groups= new datawindow_table("groups", $fields, 0, true, true, true);
			$qry->add_table($table_groups);

			// Set the order by
			$qry->add_order_by_field($table_groups, 1);

			parent::datawindow_ext($qry);
		}

		function pre_delete($group_id,$values) {
			global $USER_GROUP;
			global $MESSAGES;
			global $global_db;

			if($group_id == $USER_GROUP) {
				html_showError($MESSAGES["GROUP_MGM_NOT_OWN_DELETE"]);
				return 0;
			}

			// Has this group users?
			$query="select * from users where id_group=$group_id";
			$res=$global_db->dbms_query($query);

			if($global_db->dbms_check_result($res)) {
				$global_db->dbms_free_result($res);
				html_showError($MESSAGES["GROUP_MGM_STILL_HAS_USERS"]);
				return 0;
			}

			return 1;
		}
	}
?>
