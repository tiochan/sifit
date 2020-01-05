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

	+------------+--------------+------+-----+---------------------+----------------+
	| Field      | Type         | Null | Key | Default             | Extra          |
	+------------+--------------+------+-----+---------------------+----------------+
	| id_log     | int(11)      |      | PRI | NULL                | auto_increment |
	| log_date   | datetime     |      |     | 0000-00-00 00:00:00 |                |
	| id_user    | int(11)      |      |     | 0                   |                |
	| user_level | int(11)      |      |     | 0                   |                |
	| host       | varchar(100) | YES  |     | NULL                |                |
	| module     | varchar(25)  | YES  |     | NULL                |                |
	| action     | varchar(255) | YES  |     | NULL                |                |
	+------------+--------------+------+-----+---------------------+----------------+
*/

	$AUTH_REQUIRED=true;
	$AUTH_LVL=0;			// SIGVI ADMIN ONLY

	include_once "../include/init.inc.php";
	include_once INC_DIR . "/forms/forms.inc.php";
	include_once INC_DIR . "/forms/form_elements/datawindow.inc.php";
	include_once INC_DIR . "/forms/form_elements/search_box.inc.php";

	global $MESSAGES;

	$form_name="form_logs";

	html_header($MESSAGES["LOGS_MGM_TITLE"]);

	$restriction="";

	$app="";
	$orderby=" order by log.log_date desc";

	$list= new listbox();
	$list->lb["-1"]="-";
	$list->lb["0"]=$MESSAGES["SKILL_0"];
	$list->lb["3"]=$MESSAGES["SKILL_3"];
	$list->lb["5"]=$MESSAGES["SKILL_5"];

	$fields[]= new field("id_log","#","integer",false,true,true,false);
	$fields[]= new field("log_date",$MESSAGES["LOGS_FIELD_DATE"],"fstring", false, false, true, false);
	$fields[]= new field("id_user",$MESSAGES["LOGS_FIELD_USER_ID"],"fstring", false, false, true, false);
	$fields[]= new field("username",$MESSAGES["LOGS_FIELD_USERNAME"],"fstring", false, false, true, false);
	$fields[]= new field("user_level",$MESSAGES["LOGS_FIELD_USER_LEVEL"],"listbox", false, false, true, false, null, $list);
	$fields[]= new field("host",$MESSAGES["LOGS_FIELD_HOST"],"fstring", false, false, true, false);
	$fields[]= new field("module",$MESSAGES["LOGS_FIELD_MOD"],"fstring", false, false, true, false);
	$fields[]= new field("action",$MESSAGES["LOGS_FIELD_REG"],"fstring", false, false, true, false);

	$sb= new search_box(array($fields[1], $fields[2], $fields[3], $fields[4], $fields[5],$fields[6],$fields[7]),"sb_log","Search",2);

	$dw= new datawindow("log",$fields,"", $restriction, $orderby, false, false, false);
	$dw->add_search_box($sb);

	$frm= new form($form_name);
	$frm->add_element($dw);
	$frm->form_control();

	html_footer();
?>
