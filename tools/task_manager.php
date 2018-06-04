<?php
/**
 * @author Jorge Novoa (jorge.novoa@upcnet.es)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage processes to launch
 *
 */


	$AUTH_REQUIRED=true;
	$AUTH_LVL=5;

	include_once "../include/init.inc.php";

	include_once INC_DIR . "/forms/forms.inc.php";
	include_once INC_DIR . "/forms/form_elements/tab.inc.php";
	include_once INC_DIR . "/classes/dw_task_manager.class.php";

	
	global $MESSAGES;

	$form_name= "form_task_manager";

	html_header($MESSAGES["TASKS_MGM_TITLE"]);

	$dw= new dw_task_manager();

	$frm= new form($form_name);
	$frm->add_element($dw);
	$frm->form_control();

	html_footer();
