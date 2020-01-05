<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 *
 * @package lib
 * @subpackage reports
 *
 * Dashboard render tool
 *
 */

	$AUTH_REQUIRED=true;
	$AUTH_LVL=5;			// ADMINS ONLY

	define("SHOW_MENU",false);
	
	include_once "../include/init.inc.php";
	include INC_DIR . "/reports/dashboard.class.php";

	global $USER_LEVEL, $USER_GROUP, $MESSAGES;

	if(get_http_param("show_header",false)=="true") html_header("Dashboard preview");

	$dashboard_id= get_http_param("id_dashboard",false);
	if($dashboard_id === false) {
		$dashboard_id= get_http_param("detail_id_dashboard", false);
		if($dashboard_id === false) {
			html_showError("** Dashboard id expected **", true);
			html_footer();
			exit;
		}
	}

	$dashboard= new dashboard($dashboard_id);

	// Report access: only generic (id_group is null) and own group reports
	if($USER_LEVEL != 0 and $report->id_group != null and $report->id_group != $USER_GROUP) {
		html_showError("** Not allowed to see this dashboard **", true);
	} else {
		$content= $dashboard->parse_dashboard();
		echo $content;
	}

	html_footer();
?>
