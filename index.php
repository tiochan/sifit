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
	html_header($MESSAGES["APP_NAME"]);

	$menu->show();

	html_footer();
?>
