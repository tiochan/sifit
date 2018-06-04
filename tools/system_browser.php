<?php
	
	$AUTH_REQUIRED=true;
	$AUTH_LVL=0;			// ADMINS ONLY
	
	include_once "../include/init.inc.php";
	include_once INC_DIR . "/forms/forms.inc.php";
	include_once INC_DIR . "/forms/form_elements/system_browser.inc.php";


	html_header("System browser");

	$sb= new system_browser("/",true,true,true);

	$frm= new form("system_browser");
	$frm->add_element($sb);
	$frm->form_control();

	html_footer();
?>