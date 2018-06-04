<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage admin
 *
 */


	$AUTH_REQUIRED=true;
	$AUTH_LVL=3;

	include_once "../include/init.inc.php";

	include_once INC_DIR . "/forms/forms.inc.php";
	include_once INC_DIR . "/forms/form_elements/tab.inc.php";
	include_once INC_DIR . "/classes/dw_groups.class.php";

	if(file_exists(MY_INC_DIR . "/classes/dw_users.class.php")) {
		include_once MY_INC_DIR . "/classes/dw_users.class.php";
	} else {
		include_once INC_DIR . "/classes/dw_users.class.php";
	}

	$tab= new tab_box("tab_1");

	global $USER_LEVEL, $MESSAGES;

	if($USER_LEVEL == 0) {
		$tb_groups= new tab("tab_groups",$MESSAGES["GROUPS"]);
		$grp= new dw_group();
		$tb_groups->add_element($grp);
		$tab->add_tab($tb_groups);
	}

	$tb_users= new tab("tab_users",$MESSAGES["USERS"]);
	$usr= new dw_users();
	$tb_users->add_element($usr);
	$tab->add_tab($tb_users);


	$form= new form("form_groups_and_users");
	$form->add_element($tab);

	html_header($MESSAGES["GROUPS_AND_USERS_MGM_TITLE"]);
	$form->form_control();
	html_footer();
?>