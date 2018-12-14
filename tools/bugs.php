<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage admin
 *
 */


/*
	Table definition

	+-----------------+-------------+------+-----+---------+----------------+
	| Field           | Type        | Null | Key | Default | Extra          |
	+-----------------+-------------+------+-----+---------+----------------+
	| id_bug          | int(11)     | NO   | PRI | NULL    | auto_increment |
	| status          | int(11)     | NO   |     | NULL    |                |
	| id_user         | int(11)     | NO   |     | NULL    |                |
	| username        | varchar(60) | YES  |     | NULL    |                |
	| bug_description | text        | YES  |     | NULL    |                |
	+-----------------+-------------+------+-----+---------+----------------+

*/

	$AUTH_REQUIRED=true;
	$AUTH_LVL=5;			// ADMINS ONLY


	include_once "../include/init.inc.php";
	include_once INC_DIR . "/forms/forms.inc.php";
	include_once INC_DIR . "/forms/form_elements/datawindow.inc.php";
	include_once INC_DIR . "/forms/form_elements/search_box.inc.php";

	global $MESSAGES;

	$form_name= "form_users";

	class bug extends datawindow {

		/* Restrictions:
		 *
		 *  - SIGVI Administrators can be created only by other SIGVI administrator
		 *  - group administrators can be created and deleted only by SIGVI Administrators
		 *  - group administrators can be updated only by themselves and sigvi administrators
		 *  - server administrators can be created, deleted only by sigvi administrators and group administrators
		 *  - server administrators can be updated by sigvi administrators, group administrators and themselves
		 */
		function pre_show_row(&$values, &$can_update, &$can_delete) {
			global $USER_ID;
			global $USER_LEVEL;

			if($USER_LEVEL == 0) return 1;

			$can_delete= false;
			if($USER_ID == $values["id_user"]) return 1;
			$can_update=false;
			$can_delete=false;

			return 1;
		}

		function pre_insert(&$values) {
			global $USER_ID;
			global $USER_NAME;
			global $MESSAGES;

			$values["id_user"]= $USER_ID;
			$values["status"]=0;
			$values["username"]=$USER_NAME;

			return 1;
		}

		function post_insert($values) {

			if(get_app_param("send_bug_notification")) {
				if(($adm_email= get_app_param("adm_email")) !== false) {

					include_once INC_DIR . "/mail.inc.php";
					$subject= "New bug reported from " . $values["username"];
					$content= $values["bug_description"];
					send_mail($adm_email,$subject,$subject,$content,"html");
				}
			}

			return 1;
		}

		function pre_update($row_id, $old_values, &$new_values) {
			global $USER_NAME;
			global $MESSAGES;
			global $global_db;

			$new_values["id_user"]= $old_values["id_user"];
			$new_values["username"]=$old_values["username"];

			if($old_values["status"]!=3 and $new_values["status"]==3) {
				$new_values["closing_date"]= date(DATE_FORMAT . " H:i:s");
			}

			return 1;
		}

		function pre_delete($row_id, $values) {
			// No delete allowed
			// html_showError("Delete not allowed");
			// return 0;
			return 1;
		}
	}

	html_header($MESSAGES["BUG_MGM_TITLE"]);

	$fields= Array();

	$list= new listbox();

	$list->lb["0"]=$MESSAGES["BUG_STATUS_OPEN"];
	$list->lb["3"]=$MESSAGES["BUG_STATUS_CLOSED"];
	$list->lb["5"]=$MESSAGES["BUG_STATUS_PENDING"];

	$list->default_search_value="0";

	$null_ref=null;

	global $USER_NAME;

	$fields[]= new field("id_bug","ID","auto",false,true,true,false);
	$fields[]= new field("status",$MESSAGES["BUG_FIELD_STATUS"],"listbox",true,false,true,true,0,$list);
	$fields[]= new field("id_user","","integer",false,false,false,false,$USER_NAME);
	$fields[]= new field("username",$MESSAGES["USER_FIELD_USERNAME"],"fstring",false,false,true,false,$USER_NAME);
	$fields[]= new field("bug_description",$MESSAGES["BUG_FIELD_DESCRIPTION"],"short_html",true,false,true,true);
	$fields[]= new field("creation_date",$MESSAGES["BUGS_FIELD_CREATION_DATE"],"date",true,false,true,false);
	$fields[]= new field("closing_date",$MESSAGES["BUGS_FIELD_CLOSING_DATE"],"date",true,false,true,false);

	$sb= new search_box(array($fields[1], $fields[3], $fields[4]),"search_alert",$MESSAGES["SEARCH"],1,false);
	$sb->set_field_default_value("status",0);

	$query_adds=" order by creation_date";
	$dw= new bug("bug",$fields,0, "", $query_adds, true, true, true);
	$dw->add_search_box($sb);


	$frm= new form($form_name);
	$frm->add_element($dw);
	$frm->form_control();

	html_footer();
?>
