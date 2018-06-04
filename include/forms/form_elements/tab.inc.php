<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage forms
 *
 */

	include_once SYSHOME . "/include/forms/form_elements.inc.php";


	class tab extends form_element {
		public $tab_id;
		public $text;

		public function tab($tab_id, $text) {
			parent::form_element($tab_id);

			$this->tab_id= $tab_id;
			$this->text= $text;

			$this->visible=false;
		}
	}

	/**
	 * Creates a tab_box
	 *
	 */
	class tab_box extends form_element {
		public $tabs;

		protected $selected_tab_id;

		/**
		 * tab box constructor
		 *
		 * @param string $name, internal button name
		 * @return button
		 */
		public function tab_box($name) {

			parent::form_element($name);

			$this->tabs= array();
			$this->selected_tab_id= get_http_param($this->doc_ref . "_tab_selected", -1);
		}

		/**
		 * Adds a new tab to the tab box.
		 *
		 * @param tab $tab
		 */
		public function add_tab(& $tab) {

			if($this->selected_tab_id == -1) $this->selected_tab_id= $tab->tab_id;
			$tab->visible= false;		// Only the selected tab will be displayed.
			$this->tabs[]= $tab;
			$this->add_element($tab);
		}

		/**
		 * Try to find a tab_id on the tabs array.
		 * If is found return the array position, else returns -1 or the
		 * default value (if set).
		 *
		 * @param string $tab_id
		 * @param mixed $default_return
		 * @return integer
		 */
		protected function search_tab($tab_id, $default_return=-1) {

			for($i=0; $i< count($this->tabs); $i++) {
				if($this->tabs[$i]->tab_id == $tab_id) return $i;
			}

			return $default_return;
		}

		/**
		 * Determines which tab will be displayed as active. If tab is found,
		 * set it as default tab and returns true, else returns false.
		 *
		 * @param string $tab_id
		 * @return integer
		 */
		public function set_selected_tab($tab_id) {

			if(($pos= $this->search_tab($tab_id)) == -1) {
				if(DEBUG) html_showError("Tab_box::set_default_tab, tab id does not exists ('$tab_id')<br>");
				return false;
			}

			$this->selected_tab_id= $tab_id;
			return true;
		}

		/**
		 * Show hidden information to perpetuate the shown tag
		 *
		 */
		public function show_hidden() {

			if($this->hidden_shown) return;
			parent::show_hidden();

?>
				<input type='hidden' name='<?php echo $this->doc_ref; ?>_tab_selected' value='<?php echo $this->selected_tab_id; ?>'>
<?php
		}

		/**
		 * Show tag boxes. Only objects on active tag will be displayed.
		 *
		 */
		public function show() {

			parent::show();

			if(!$this->visible) return;

			$tab_index= $this->search_tab($this->selected_tab_id, -1);

			if($tab_index == -1) {
				if(DEBUG) html_showError("No tab selected or tab not found ($this->selected_tab_id)");
				else html_showError("tab_box::show, Error code TB[1439]<br>");
				exit;
			}
?>
			<table class="tab" cellpadding="0" cellspacing="0">
				<tr>
				<td>
					<table class="sub_tab" cellpadding="0" cellspacing="0">
					<tr>
						<td>
<?php					foreach($this->tabs as $tab) {
						$action= "document.forms." . $this->form_name . ".element.value=\"" . $this->doc_name . "\";
										  document.forms." . $this->form_name . "." . $this->doc_ref . "_tab_selected.value=\"" . $tab->tab_id . "\";
										  document.forms." . $this->form_name . ".submit()";
						if($tab->tab_id == $this->selected_tab_id) { ?>
							<div class="tab_selected">&nbsp;<?php echo $tab->text; ?>&nbsp;</div>
<?php						} else { ?>
							<div class="tab" onclick='<?php echo $action; ?>'>&nbsp;<?php echo $tab->text; ?>&nbsp;</div>
<?php						} ?>
<?php					} ?>
							<div class="end_tab"></div>
						</td>
					</tr>
					</table>
				</td>
				</tr>
				<tr>
					<td class="tab_content">
						&nbsp;
						<table border="0" cellpadding="3" cellpadding="3">
							<tr>
							<td>
<?php								$this->tabs[$tab_index]->visible= true;
								$this->tabs[$tab_index]->show();
?>
							</td>
							</tr>
						</table>
						&nbsp;
					</td>
				</tr>
			</table>
<?php		}

		function event($event_type) {
			return $this->clicked();
		}

		// Abstract
		function clicked() {
			return 0;
		}
	}
?>