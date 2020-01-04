<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package admin
 *
 * Main page.
 */

$AUTH_REQUIRED=true;
$AUTH_LVL=100;

include_once "../include/init.inc.php";
include_once "../include/menu.inc.php";

html_header("");

phpinfo();

html_footer();
