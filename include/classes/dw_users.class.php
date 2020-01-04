<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage users
 *
 * Datawindow class for users management.
 */


/*
	Table definition

	+--------------------+--------------+------+-----+-------------------+----------------+
	| Field              | Type         | Null | Key | Default           | Extra          |
	+--------------------+--------------+------+-----+-------------------+----------------+
	| id_user            | mediumint(9) | NO   | PRI | NULL              | auto_increment |
	| username           | char(60)     | NO   | UNI | NULL              |                |
	| password           | char(60)     | NO   |     | NULL              |                |
	| external           | tinyint(4)   | NO   |     | 0                 |                |
	| name               | char(60)     | NO   |     | NULL              |                |
	| surname            | char(60)     | YES  |     | NULL              |                |
	| id_group           | mediumint(9) | NO   |     | 0                 |                |
	| email              | char(100)    | NO   |     | NULL              |                |
	| level              | tinyint(4)   | NO   |     | 100               |                |
	| send_notifications | tinyint(4)   | YES  |     | NULL              |                |
	| hiredate           | timestamp    | YES  |     | CURRENT_TIMESTAMP |                |
	| lang               | char(10)     | YES  |     | en                |                |
	+--------------------+--------------+------+-----+-------------------+----------------+

*/

	include_once INC_DIR . "/forms/forms.inc.php";
	include_once INC_DIR . "/forms/form_elements/datawindow.inc.php";
	include_once INC_DIR . "/forms/form_elements/search_box.inc.php";

	class dw_users extends datawindow {

		public function dw_users(&$optional_db=null) {

			global $USER_GROUP, $USER_LEVEL, $global_db, $MESSAGES;


			$fields= Array();

			$group_restriction= $USER_LEVEL!=0 ? "id_group=$USER_GROUP" : "";
			$group_reference= new foreign_key($global_db,"groups","id_group","name", $group_restriction);

			$filter_restriction= $USER_LEVEL == 0 ? "" : "(id_group=$USER_GROUP or id_group is null)";
			$filter_reference= new foreign_key($global_db,"filters","id_filter","name", $filter_restriction,"");

			$list= new listbox();

			if($USER_LEVEL == 0) $list->lb["0"]=$MESSAGES["SKILL_0"];
			if($USER_LEVEL <= 3) $list->lb["3"]=$MESSAGES["SKILL_3"];
			$list->lb["5"]=$MESSAGES["SKILL_5"];

			switch($USER_LEVEL) {
				case 0:
						$restriction="";
						break;
				case 3:
						$restriction= "id_group=$USER_GROUP";
						break;
				case 5:
						$restriction= "id_user=$USER_ID";
						break;
			}
			$null_ref=null;

			$fields[0]= new field("id_user","","auto",false,true,false,false);
			$fields[1]= new field("username",$MESSAGES["USER_FIELD_USERNAME"],"fstring",true,true,true,true);
			$fields[2]= new field("password",$MESSAGES["USER_FIELD_PASSWORD"],"password",false,false,false,true);
			$fields[3]= new field("external",$MESSAGES["USER_FIELD_EXTERNAL"],"fbool",true,false,true,($USER_LEVEL <= 3),0);
			$fields[4]= new field("name",$MESSAGES["USER_FIELD_NAME"],"fstring",true,false,true,true);
			$fields[5]= new field("surname",$MESSAGES["USER_FIELD_SURNAME"],"fstring",false,false,true,true);
			$fields[6]= new field("id_group",$MESSAGES["USER_FIELD_GROUP"],"foreign_key",true,false,($USER_LEVEL < 3),($USER_LEVEL < 3),$USER_GROUP,$group_reference);
			$fields[7]= new field("level",$MESSAGES["USER_FIELD_LEVEL"],"listbox",true,false,true,($USER_LEVEL == 0),5,$list);
			$fields[8]= new field("email",$MESSAGES["USER_FIELD_EMAIL"],"fstring",true,false,true,true);
			$fields[9]= new field("hiredate",$MESSAGES["USER_FIELD_HIREDATE"],"fstring",false,false,true,false);
			$fields[10]= new field("lang",$MESSAGES["USER_FIELD_LANG"],"list_lang",true,false,true,true,"en");
			$fields[11]= new field("send_notifications",$MESSAGES["USER_FIELD_SEND_NOTIFICATIONS"],"fbool",true,false,true,true,1);

			$sb= new search_box(array($fields[1], $fields[3], $fields[4], $fields[5], $fields[6], $fields[7]),"search_alert",$MESSAGES["SEARCH"]);


			$can_insert= ($USER_LEVEL <= 3);
			$can_update= ($USER_LEVEL <= 5);
			$can_delete= ($USER_LEVEL <= 3);
			$query_adds=" order by username";

			parent::datawindow("users",$fields,0, $restriction, $query_adds, $can_insert, $can_update, $can_delete,$optional_db);
			$this->add_search_box($sb);
		}

		function pre_show_row($values, &$can_update, &$can_delete) {

			global $USER_LEVEL, $USER_ID;

			if($USER_LEVEL == 0) return 1;

			if($USER_ID == $values["id_user"]) {
				$can_update= true;
				$can_delete= false;
				return 1;
			}

			$can_update= $can_delete= ($values["level"] > $USER_LEVEL);
			return 1;
		}


		/* Restrictions:
		 *
		 *  - SIGVI Administrators can be created only by other SIGVI administrator
		 *  - group administrators can be created and deleted only by SIGVI Administrators
		 *  - group administrators can be updated only by themselves and sigvi administrators
		 *  - server administrators can be created, deleted only by sigvi administrators and group administrators
		 *  - server administrators can be updated by sigvi administrators, group administrators and themselves
		 */

		function pre_insert(&$values) {
			global $USER_NAME;
			global $USER_GROUP;
			global $USER_LEVEL;
			global $MESSAGES;

			// SIGVI Admin can insert everithing
			switch($USER_LEVEL) {
				case 0:    // All privileges
					return 1;
				case 3:
					// New user level must be lower than current user
					$values["level"]= 5;
					$values["id_group"]= $USER_GROUP;

					break;
				case 5:		// Server admins can't create users
					html_showError($MESSAGES["MGM_GRANT_CANT_CREATE"]);
					return 0;
			}

			return 1;
		}

		function pre_update($row_id, $old_values, &$new_values) {
			global $USER_NAME;
			global $USER_GROUP;
			global $USER_LEVEL;
			global $USER_ID;
			global $MESSAGES;
			global $global_db;


			if($USER_LEVEL == 0)
				return 1;

			// Server admin can only modify themselves
			if(($USER_LEVEL == 5) and ($USER_ID != $row_id)) {
				html_showError($MESSAGES["MGM_CANT_GRANT_SAME_LEVEL"]);
				return 0;
			}

			// Only sigvi admin can modify level
			$new_values["level"]= $old_values["level"];
			// Only sigvi admin can change group
			$new_values["id_group"]= $old_values["id_group"];

			// One user can't udpate other users with same level
			if(($USER_ID != $row_id) and ($new_values["level"] <= $USER_LEVEL)) {
				html_showError($MESSAGES["MGM_CANT_GRANT_SAME_LEVEL"]);
				return 0;
			}

			// One user can't get same or more privileges for other users (except sigvi admin)
			if(($USER_ID != $row_id) and ($new_values["level"] <= $USER_LEVEL)) {
				html_showError($MESSAGES["MGM_GRANT_CANT_MODIFY"]);
				return 0;
			}

			// If gets here, then all is supposed to be OK.
			return 1;
		}

		function pre_delete($row_id, $old_values) {
			global $USER_NAME;
			global $USER_ID;
			global $USER_GROUP;
			global $USER_LEVEL;
			global $MESSAGES;

			global $global_db;

			// Same user... can't delete himself
			if($row_id == $USER_ID) {
				html_showError($MESSAGES["USER_MGM_NOT_OWN_DELETE"]);
				return 0;
			}

			// SIGVI Admin can delete everithing but himself
			if($USER_LEVEL == 0)
				return 1;

			// No one, excepts sigvi administrators, can delete other with same or higher level
			// Check for current level for user to be deleted:
			if($USER_LEVEL >= $old_values["level"]) {
				html_showError($MESSAGES["MGM_GRANT_CANT_DELETE"]);
				return 0;
			}

			return 1;
		}
	}
