<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 *
 * @package lib
 * @subpackage reports
 *
 * Dashboards and reports management
 *
 */


	$AUTH_REQUIRED=true;
	$AUTH_LVL=5;

	include_once "../include/init.inc.php";

	include_once INC_DIR . "/forms/forms.inc.php";
	include_once INC_DIR . "/forms/form_elements/tab.inc.php";
	include_once INC_DIR . "/classes/dw_dashboards.class.php";
	include_once INC_DIR . "/classes/dw_reports.class.php";
	include_once INC_DIR . "/classes/dw_report_tags.class.php";
	include_once INC_DIR . "/classes/dw_report_subscriptions.class.php";

	global $MESSAGES;

	$tab= new tab_box("tab_1");

	$tb_dashboards= new tab("tab_dashboards","Dashboards");
	$dw_dashboards= new dw_dashboards();
	$tb_dashboards->add_element($dw_dashboards);
	$tab->add_tab($tb_dashboards);

	$tb_dashboards= new tab("tab_report_subscriptions",$MESSAGES["SUBSCRIPTIONS_MGM_TITLE"]);
	$dw_subscriptions= new dw_report_subscriptions();
	$tb_dashboards->add_element($dw_subscriptions);
	$tab->add_tab($tb_dashboards);

	$tb_reports= new tab("tab_reports",$MESSAGES["REPORT_MGM_TITLE"]);
	$dw_reports= new dw_reports();
	$tb_reports->add_element($dw_reports);
	$tab->add_tab($tb_reports);

	$tb_tags= new tab("tab_tags",$MESSAGES["TAGS_MGM_TITLE"]);
	$dw_tags= new dw_report_tags();
	$tb_tags->add_element($dw_tags);
	$tab->add_tab($tb_tags);


	$form= new form("form_reports");
	$form->add_element($tab);

	html_header($MESSAGES["REPORT_MGM_TITLE"]);
	$form->form_control();

	html_footer();
?>
