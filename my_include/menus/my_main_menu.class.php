<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package admin
 *
 * Main menu.
 */

	include_once INC_DIR . "/menus/main_menu.class.php";

	class my_main_menu extends main_menu {


		public function my_main_menu() {

			global $MESSAGES, $USER_LEVEL;

			$this->menus= array();
			$more_items= Array();

			parent::main_menu();
/*
			$this->menus["99_about_menu"]= new html_menu("Doc", ICONS . "/info.png");

			$doc_item[]= new html_menu_item("Document de configuraci&oacute; ETPL", ICONS . "/info.png",  HOME . "/doc/info/config_ETPL.pdf");

			foreach ( $doc_item as $item ) {
				$this->menus["99_about_menu"]->add_menu_item($item);
			}
			ksort($this->menus);*/
		}
	}
?>
