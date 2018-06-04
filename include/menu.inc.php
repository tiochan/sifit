<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage menu
 *
 * Menu creation class
 */

/**
 * Styles and Javascript code for menus normal and html (desplegable)
 */
	global $GLOBAL_HEADERS;
	$GLOBAL_HEADERS["menu_text_css"]="<link rel='stylesheet' type='text/css' href='" . HOME . "/include/styles/menus.css'>";

	if(file_exists(MY_INC_DIR. "/styles/menus.css")) {
		$GLOBAL_HEADERS["my_menu_text_css"]="<link rel='stylesheet' type='text/css' href='" . HOME . "/my_include/styles/menus.css'>";
	}

	$GLOBAL_HEADERS["menu_html_css"]="<link rel='stylesheet' type='text/css' href='" . HOME . "/include/styles/menu_h.css'>";
	if(file_exists(MY_INC_DIR. "/styles/menu_h.css")) {
		$GLOBAL_HEADERS["menu_html_css"]="<link rel='stylesheet' type='text/css' href='" . HOME . "/my_include/styles/menu_h.css'>";
	}

	$GLOBAL_HEADERS["menu_html_js"]="<script language='javascript' src='" . HOME . "/include/styles/menu_h.js'></script>";



	class menu_item {
		public $label;
		public $image_url;
		public $url;

		public function menu_item($label, $image_url, $url) {
			$this->label= $label;
			$this->image_url= $image_url;
			$this->url= $url;
		}

		public function show() {
?>			<div class='menu' onclick="javascript:window.location.href='<?php echo $this->url; ?>'">
<?php		if($this->image_url != "") {
?>				<img class='menu_icon' align='middle' border=0 src='<?php echo $this->image_url; ?>'>
<?php		}
			echo "&nbsp;" . $this->label . "\n";
?>			</div>
<?php
		}
	}

	class menu {
		public $label;
		public $menu_items;

		public function menu($label){

			$this->label= $label;
			$this->menu_items= array();
		}

		public function add_menu_item($menu_item) {
			$this->menu_items[] = $menu_item;
		}

		public function show($cols=1) {

			if(($cols == 0) or count($this->menu_items) == 0) return;

			$rows= ceil(count($this->menu_items) / $cols);
?>			<table class='menu'>
				<tr>
					<th class='menu' colspan="<?php echo $cols; ?>"><?php echo $this->label; ?></th>
				</tr>
<?php
			for($i=0, $k=0; $i < $rows; $i++) {
?>				<tr>
<?php				for($j=0; $j < $cols and $k < count($this->menu_items); $j++, $k++) {
					echo "<td class='menu'>\n";
					$this->menu_items[$k]->show();
					echo "</td>\n";
				}
?>				</tr>
<?php			}
?>			</table><br>
<?php		}
	}

	class html_menu_item {

		public $label;
		public $image_url;
		public $url;

		public function html_menu_item($label, $image_url, $url) {
			$this->label= $label;
			$this->image_url= $image_url;
			$this->url= $url;
		}

		public function show() {

			$img= ($this->image_url != "") ? "<img class='menu_icon' src='" . $this->image_url . "'>" : "";
?>			<li><a href='<?php echo $this->url; ?>'><?php echo $img . $this->label; ?></a></li>
<?php
		}
	}

	class html_menu {

		public $label, $image_url, $url, $new_window;
		public $menu_items;

		public function html_menu($label, $image_url="", $url="", $new_window=false) {

			$this->label= $label;
			$this->image_url= $image_url;
			$this->url= $url;
			$this->new_window= $new_window;

			$this->menu_items= array();
		}

		public function add_menu_item($menu_item) {
			$this->menu_items[] = $menu_item;
		}

		public function show() {

			$img= ($this->image_url != "") ? "<img class='menu_icon' src='" . $this->image_url . "'>" : "";
			$url= ($this->url != "") ? $this->url : "#";

			$add= $this->new_window ? " target='_blank'" : "";

?>				<li><a href='<?php echo $url; ?>'<?php echo $add; ?>><?php echo $img . $this->label; ?></a>
<?php
			if(count($this->menu_items) > 0) {
?>					<ul id='navmenu-h'>
<?php
				foreach($this->menu_items as $menu_item) {
					$menu_item->show();
				}
?>					</ul>
<?php
			}
?>				</li>
<?php
		}
	}

	class html_menu_bar {

		protected $menus;

		public function html_menu_bar() {
			$this->menus= array();
		}

		public function add_menu(&$menu) {
			$this->menus[]= $menu;
		}

		public function show() {

			if(!count($this->menus)) return;

?>			<ul id='navmenu-h'>
<?php
			foreach($this->menus as $menu) {
				$menu->show();
			}
?>			</ul>
<?php
		}
	}
?>