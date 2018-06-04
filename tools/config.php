<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage config
 *
 */

	$AUTH_REQUIRED=true;
	$AUTH_LVL=0;			// APP ADMINS ONLY
/*
	if( isset($_POST["tab_1_tab_selected"]) and $_POST["tab_1_tab_selected"] == "tab_parameters") {
	} else {
		define("NO_DDBB",true);
	}
*/
	global $MESSAGES;

	include_once "../include/init.inc.php";

	include_once INC_DIR . "/forms/forms.inc.php";
	include_once INC_DIR . "/forms/form_elements/tab.inc.php";
	include_once INC_DIR . "/classes/frm_config.class.php";
	include_once INC_DIR . "/classes/dw_parameters.class.php";

	$tab= new tab_box("tab_1");

	$tb_config= new tab("tab_config",$MESSAGES["CONFIG_MGM_TITLE"]);
	$dw_config= new frm_config();
	$tb_config->add_element($dw_config);
	$tab->add_tab($tb_config);

	$tb_parameters= new tab("tab_parameters",$MESSAGES["PARAMETERS_MGM_TITLE"]);
	if( isset($_POST["tab_1_tab_selected"]) and $_POST["tab_1_tab_selected"] == "tab_parameters") {
		$dw_parameters= new dw_parameter();
		$tb_parameters->add_element($dw_parameters);
	}
	$tab->add_tab($tb_parameters);


	$form= new form("form_configuration");
	$form->add_element($tab);

	html_header($MESSAGES["CONFIG_MGM_TITLE"]);

	$form->form_control();

	html_footer();
?>