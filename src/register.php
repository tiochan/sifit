<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package admin
 *
 * Registration page.
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

	$AUTH_REQUIRED=false;

	include_once "./include/init.inc.php";

	if(!defined("DEMO_VERSION") || !DEMO_VERSION) {
		header("Location: ". HOME . "/index.php");
		exit;
	}


	include_once INC_DIR . "/forms/forms.inc.php";
	include_once INC_DIR . "/forms/field_types/listbox.inc.php";
	include_once INC_DIR . "/forms/form_elements/button.inc.php";
	include_once INC_DIR . "/forms/form_elements/checkbox.inc.php";
	include_once INC_DIR . "/forms/form_elements/datawindow.inc.php";
	include_once INC_DIR . "/forms/containers/sub_form.inc.php";
	include_once INC_DIR . "/forms/form_elements/select.inc.php";
	include_once INC_DIR . "/forms/form_elements/search_box.inc.php";

	$form_name= "form_users";

	function user_exists($username) {
		global $global_db;

		$query="select * from users where username='" . $username . "'";
		$res=$global_db->dbms_query($query);

		if($res) {
			$global_db->dbms_free_result($res);
			return 0;
		}
		return 1;
	}

	function create_group($group_name) {
		global $global_db;

		$query="insert into groups (name, description) values ('" . $group_name . "','" . $group_name . " group.')";
		$global_db->dbms_query($query);

		if($global_db->dbms_affected_rows() < 1) return false;

		if(!($res=$global_db->dbms_query("select id_group from groups where name='$group_name'"))) {
			html_showError("Any error, please try later.");
			return false;
		}

		$row= $global_db->dbms_fetch_row($res);
		$global_db->dbms_free_result($res);
		return $row[0];
	}

	class register_user extends datawindow {

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
			global $global_db;

			// SIGVI Admin can insert everithing
			switch($USER_LEVEL) {
				case 0:    // All privileges
					break;
				case 3:
					// New user level must be lower than current user
					if($values["level"] <= $USER_LEVEL) {
						html_showError($MESSAGES["MGM_CANT_GRANT_SAME_LEVEL"]);
						return 0;
					}
					break;
				case 5:		// Server admins can't create users
					html_showError($MESSAGES["MGM_GRANT_CANT_CREATE"]);
					return 0;
			}

			// Check username availability.
			if(user_exists($values["username"])) {
				html_showError(sprintf($MESSAGES["FIELD_EXISTS"],$values["username"]));
				return 0;
			}

			$group_name= $values["username"] . "_grp_" . rand(10000,32000);
			if(($group_id= create_group($group_name))===false) return 0;

			$values["id_group"]= $group_id;
			$values["external"]=0;
			$values["level"]=3;					// Group mgr
			$values["send_notifications"]=1;

			return 1;
		}

		function post_insert($values) {
			global $MESSAGES;
			global $global_db;

			$msg= sprintf($MESSAGES["REGISTER_SUCCESS"], $values["name"]);
			echo $msg;

			include_once SYSHOME . "/include/mail.inc.php";

			$page_title=APP_NAME;
			$subject="Welcome to SIGVI";

			send_mail($values["email"],$page_title,$subject,$msg,"html");

			// Notify admins:
			$subject="SIGVI: new subscription";
			$content="New user registered: ". $values["username"];
			send_admins_mail($page_title,$subject,$content,"html");
			if(($adm_email= get_app_param("adm_email")) !== false) {
				send_mail($adm_email,$page_title,$subject,$content,"html");
			}

			$global_db->dbms_commit();
			html_showFooter();
			exit;
		}
	}

	html_header($MESSAGES["USER_REGISTER_TITLE"]);

	$fields= Array();

	$list= new listbox();
	$list->lb["3"]=$MESSAGES["SKILL_3"];

	$fields[0]= new field("id_user","","auto",false,true,false,false);
	$fields[1]= new field("username",$MESSAGES["USER_FIELD_USERNAME"],"fstring",true,true,true,true);
	$fields[2]= new field("password",$MESSAGES["USER_FIELD_PASSWORD"],"password",false,false,false,true);
	$fields[3]= new field("name",$MESSAGES["USER_FIELD_NAME"],"fstring",true,false,true,true);
	$fields[4]= new field("surname",$MESSAGES["USER_FIELD_SURNAME"],"fstring",false,false,true,true);
	$fields[5]= new field("email",$MESSAGES["USER_FIELD_EMAIL"],"fstring",true,false,true,true);
	$fields[6]= new field("lang",$MESSAGES["USER_FIELD_LANG"],"list_lang",true,false,true,true,"en");
	$fields[7]= new field("external","","fbool",false,false,false,false);
	$fields[8]= new field("id_group","","fstring",false,false,false,false);
	$fields[9]= new field("level","","integer",false,false,false,false);
	$fields[10]= new field("send_notifications","","integer",false,false,false,false);

	$can_insert= true;
	$can_update= false;
	$can_delete= false;

	$dw= new register_user("users",$fields,"id_user", "", "", $can_insert, $can_update, $can_delete);
	$frm= new form($form_name);
	$frm->add_element($dw);

	$frm->show_form();

	$action= get_http_post_param("dw_action_" . $dw->doc_name, "");
	if($action != "insert_row") {
		$dw->show_insert_form("insert_row");
	} else {
		$dw->event("");
	}

	html_footer();
?>
