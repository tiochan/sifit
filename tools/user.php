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

	+--------------------+--------------+------+-----+---------+----------------+
	| Field              | Type         | Null | Key | Default | Extra          |
	+--------------------+--------------+------+-----+---------+----------------+
	| id_user            | mediumint(9) |      | PRI | NULL    | auto_increment |
	| username           | char(60)     |      | UNI |         |                |
	| password           | char(60)     |      |     |         |                |
	| external           | tinyint(4)   |      |     | 0       |                |
	| name               | char(60)     |      |     |         |                |
	| surname            | char(60)     | YES  |     | NULL    |                |
	| id_group           | mediumint(9) |      |     | 0       |                |
	| email              | char(100)    |      |     |         |                |
	| level              | tinyint(4)   |      |     | 100     |                |
	| send_notifications | tinyint(4)   | YES  |     | NULL    |                |
	+--------------------+--------------+------+-----+---------+----------------+
*/

	$AUTH_REQUIRED=true;
	$AUTH_LVL=5;			// EACH USER

	include_once "../include/init.inc.php";
	include_once INC_DIR . "/forms/forms.inc.php";

	global $MESSAGES;

	if(file_exists(MY_INC_DIR . "/classes/dw_user.class.php")) {
		include_once MY_INC_DIR . "/classes/dw_user.class.php";
	} else {
		include_once INC_DIR . "/classes/dw_user.class.php";
	}

	$form_name= "form_users";

	html_header($MESSAGES["USER_MGM_TITLE"]);

	$dw= new dw_user();
	$dw->tabular=false;
	$frm= new form($form_name);
	$frm->add_element($dw);
	$frm->form_control();

	html_footer();
?>