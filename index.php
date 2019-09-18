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

include_once "include/init.inc.php";
include_once "include/menu.inc.php";
include_once INC_DIR . "forms/forms.inc.php";
include_once INC_DIR . "forms/form_elements/form_report.inc.php";




/****************************************************
 *  TODO MENU
 ****************************************************/

$menu= new menu("Tools");
$items= Array();
$items[]= new menu_item("Reports", ICONS . "/kig_doc.png", "tools/reports.php");

foreach ( $items as $item ) {
	$menu->add_menu_item($item);
}


/****************************************************
 *  SHOW MENUS
 ****************************************************/

global $MESSAGES;

html_header("");

//	$menu->show();
$main_page= new form_report("MAIN_PAGE");

$frm=new form("main_page");
$frm->add_element($main_page);
$frm->form_control();

html_footer();
