<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package admin
 *
 * Show info page
 */

$CRONO_ENABLED = false;
$AUTH_REQUIRED = false;

include_once "./include/init.inc.php";

html_showHeader();
html_showMainFrameHeader("Info");
if (($page = get_http_param("info_page", false)) === false) {
	html_showError("Info page not defined.");
	html_showMainFrameFooter();
	exit;
}

$file_page = SYSHOME . "/doc/info/" . basename($page);

if (!file_exists($file_page)) {
	html_showError("Couldn't find info page.");
	html_showMainFrameFooter();
	exit;
}
echo "<table width='100%' border='0'><tr><td align='left'><font class='page_info'>";
include $file_page;
echo "</font></td></tr></table>";

html_showMainFrameFooter();
