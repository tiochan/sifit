<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 *
 * @package lib
 * @subpackage reports
 *
 * Report render tool
 *
 */

	$AUTH_REQUIRED=true;
	$AUTH_LVL=5;			// ADMINS ONLY

	define("SHOW_MENU",false);
	
	include_once "../include/init.inc.php";
	include INC_DIR . "/reports/reports.class.php";

	global $USER_LEVEL, $USER_GROUP, $MESSAGES;

	if(get_http_param("show_header",false)=="true") html_header($MESSAGES["REPORT_PREVIEW"]);

	$report_id= get_http_param("id_report",false);
	if($report_id === false) {
		$report_id= get_http_param("detail_id_report", false);
		if($report_id === false) {
			html_showError("** Report id expected **", true);
			html_footer();
			exit;
		}
	}

	$report= new report($report_id);

	// Report access: only generic (id_group is null) and own group reports
	if($USER_LEVEL != 0 and $report->id_group != null and $report->id_group != $USER_GROUP) {
		html_showError("** Not allowed to see this report **", true);
	} else {
		$content= $report->parse_report();
		echo $content;
	}

	html_footer();
?>
