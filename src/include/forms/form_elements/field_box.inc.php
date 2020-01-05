<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage forms
 *
 */

	include_once INC_DIR . "/forms/form_elements.inc.php";
	include_once INC_DIR . "/forms/form_elements/common_field_elements.inc.php";

	/**
	 * Creates a selection object (list box)
	 *
	 */
	class field_box extends common_field_elements {

		public $text;
		public $fields;
		public $add_actions;
		public $cols;

		public $show_cancel_button=true;

		/**
		 * Constructor:
		 *
		 * @param string $name
		 * @param string $text
		 * @param array $fields
		 * @param string $add_actions
		 * @return field_box
		 */
		public function field_box($name, $text, &$fields, $add_actions=false, $cols=1) {

			parent::form_element($name);
			$this->text= $text;
			$this->fields=$fields;
			$this->add_actions= $add_actions;
			$this->cols= $cols;
		}

		protected function action_accept() {
			return 0;
		}

		protected function action_cancel() {
			return 0;
		}

		public function event($event_type) {

			global $MESSAGES;

			// Is comming from a submit?
			$fb_action= get_http_post_param("fb_action_" . $this->doc_name,0);
			if(!$fb_action) return 0;

			$fb_action= "action_" . $fb_action;

			if(method_exists($this,$fb_action)) {

				return $this->$fb_action();

			} else {

				return parent::event($event_type);
			}
		}

		public function show_hidden() {

			if($this->hidden_shown) return;
			parent::show_hidden();
?>
			<input type='hidden' name='fb_action_<?php echo $this->doc_name; ?>' value=''>
<?php

			if(count($this->fields)) {
				foreach($this->fields as $field) {
					$field->show_hidden($field->name);
				}
			}
		}

		public function show() {

			global $MESSAGES;

			parent::show();

			if(!$this->visible) return;
?>
			<table class='search_box'>
<?php 			if($this->text != "") {?>
				<th class='search_box'>
<?php				echo $this->text;?>
				</th>
<?php			}?>
				<tr class='search_box'>
					<td class='search_box_fields'>
						<table border="0">
<?php

			if(count($this->fields)) {	$i=0;
				foreach($this->fields as $field) {
					if($i == 0) echo "<tr>";	$i++;

					if($field->type!="hidden") {
						echo "<td>";
						echo $field->alias . ":";
						echo "</td><td>";
					}

					$field->show($field->name, true);

					if($field->type!="hidden") {
						echo "</td>";
					}

					if($i == $this->cols) {
						$i=0;
						echo "</tr>";
					} else {
						echo "<td>&nbsp;</td>";
					}
				}
			}
?>
						</table>
					</td>
				</tr>
<?php
			// parent::show();

			if($this->add_actions) {
				$this->add_actions();
			}
?>
			</table>
<?php

			for($i=0;$i<count($this->fields);$i++) {
				$this->fields[$i]->set_form_default_value($this->form_name);
			}

			if(!$this->first_time) $this->recover_values_from_call();
			return 0;
		}

		protected function add_actions() {

			global $MESSAGES;
?>
				<tr bgcolor="#DDDDDD">
					<td colspan='2'>
						<center>
							<input type='submit' class='action' value='<?php echo $MESSAGES["BUTTON_ACCEPT"]; ?>' onclick='document.forms.<?php echo $this->form_name; ?>.element.value="<?php echo $this->doc_name; ?>";document.forms.<?php echo $this->form_name; ?>.fb_action_<?php echo $this->doc_name; ?>.value="accept";document.forms.<?php echo $this->form_name; ?>.submit()'>
<?php 			if($this->show_cancel_button) { ?>
							<input type='button' class='action' value='<?php echo $MESSAGES["BUTTON_CANCEL"]; ?>' onclick='document.forms.<?php echo $this->form_name; ?>.element.value="<?php echo $this->doc_name; ?>";document.forms.<?php echo $this->form_name; ?>.fb_action_<?php echo $this->doc_name; ?>.value="cancel";document.forms.<?php echo $this->form_name; ?>.submit()'>
<?php 			} ?>
			 			</center>
		 			</td>
		 		</tr>
<?php

		}
	}
?>