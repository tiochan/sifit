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
	include_once SYSHOME . "/include/forms/form_elements/common_field_elements.inc.php";

	/**
	 * Creates a search_box
	 *
	 */
	class search_box_ext extends common_field_elements {

		public $text;
		public $fields;

		protected $private_name;

		public $help_text;
		public $cols;
		public $show_buttons=true;
		public $show_very_simple=false;

		protected $values;
		protected $extensible;

		protected $status;

		protected $restriction; // Only accesible via get_restriction method
		protected $active;		// Is really extended (extensible or not).
		protected $searching;	// Come from a submit? (Currently searching?)

		protected $hidden_field_name;
		protected $show_field_name;



		/**
		 * search_box constructor
		 *
		 * @param field array $fields
		 * @param string $name, internal field identifier
		 * @param string $text, the text to display
		 * @return search_box
		 */
		function search_box_ext($fields, $name, $text, $cols=1, $extensible=true) {
			global $MESSAGES;

			parent::form_element($name);

			$this->private_name= $this->doc_ref;

			$this->text= ($text != "") ? $text : $MESSAGES["BUTTON_SEARCH"];
			$this->fields= $fields;
			$this->values= Array();
			$this->help_text= $MESSAGES["SEARCH_CAN_USE_WILDCARDS"];
			$this->cols= $cols;
			$this->extensible= $extensible;

			/* For code debug
				echo "First time: " . ($this->first_time? "true":"false") . "<br>";
				echo "Active: " . ($this->active? "true":"false") . "<br>";
				echo "searching?: " . ($this->searching? "true":"false") . "<br>";
			*/

			$this->initial_tasks();
		}

		public function adopted() {
			$this->initial_tasks();
		}

		public function initial_tasks() {

			$this->show_field_name= "search_action_" . $this->private_name;
			$this->hidden_field_name= "hidden_search_action_" . $this->private_name;

			$this->status= get_http_post_param($this->hidden_field_name, "off");
			$this->active= $this->is_active();
			$this->searching= ($this->status == "search");

			$this->get_values();
		}

		public function set_extensible($extensible) {

			$this->extensible= $extensible;
			$this->active= $this->is_active();
		}

		public function set_active($active) {

			$this->status= "off";
			$this->initial_tasks();
		}

		public function is_active() {

			return (!$this->extensible or ($this->status != "off"));
		}

		public function get_field_value($field_name) {

			return key_exists($field_name, $this->values) ? $this->values[$field_name] : "";
		}

		public function set_field_value($field_name, $value) {

			$this->values[$field_name]= $value;
		}

		public function set_field_default_value($field_name, $value) {

			if(!isset($this->values[$field_name]))
				$this->values[$field_name]= $value;
		}

		protected function get_field_name_for_form($field) {

			$key= $field->html_name;
			$pre= $field->type!="dummy" ? "search_" : "";
			return ($this->private_name . "_" . $pre . $key);
		}

		/**
		 * PROTECTED
		 *
		 * - For every field get its value in the _POST array.
		 *
		 * Parameters:
		 *
		 * @param $values: Defined previously as array, passed by reference.
		 */
		public function get_values() {

			/**
			 * One field can be restricted via:
			 * 1. Previous search
			 * 2. Detail from master
			 *
			 * 1. Previous search
			 * In this case, it must search for:
			 * - 1.1. The samen name that was written on hidden OR
			 * - 1.2. The value from post
			 *
			 * 2. Detail from master
			 * In this case, the same field must have set the default value
			 * that itself has recovered from previous call.
			 *
			 * Get values from last call or via parameter
			 */
			foreach ($this->fields as $field) {

				$form_field_name= $this->get_field_name_for_form($field);
				$field_value= $this->get_value_from_http($form_field_name);

				if($field_value === false) {
					if ($field->is_detail) $field_value= $field->master_value;
					else continue;
				}

				$this->values[$field->name]= $field_value;
			}
		}

		/**
		 * Using current values array, calculates the restriction query
		 *
		 */
		public function get_restriction() {

			$this->get_values();

			$this->restriction= "";

			// If the searchbox is not active, then no restriction
			if(!$this->active) return "";

			// If is the first time that is loaded and no default values were given ...
			if(($this->first_time) and count($this->values)==0) { return ""; }
			$add="";

			// Get values from last call:
			foreach ($this->fields as $field) {

				if(!key_exists($field->name, $this->values)) continue;

				if($field->type=="dummy") continue;		// Dummy fields restriction must be set from inherited class.

				$aux= $this->values[$field->name];
				if(trim($aux) == "") continue;

				$value= strtoupper($aux);
				$query_str= $field->get_query_string($value);
				if($query_str == "") continue;

				$this->restriction.= $add . "(" . $query_str . ")";
				$add=" and ";
			}

			return $this->restriction;
		}

		/**
		 *
		 * @param string $field_name
		 */
		function get_value_from_http($field_name) {

			$value= get_http_param($field_name, false);
			if($value !== false) return $value;

			return get_http_post_param("hidden_" . $field_name, false);
		}

		/**
		 *
		 */
		function show_hidden() {

			if($this->hidden_shown) return;

			parent::show_hidden();

			echo "<input type='hidden' name='" . $this->hidden_field_name . "' value='" . $this->status . "'>\n";

			foreach($this->fields as $field) {
				$field_name= "hidden_" . $this->get_field_name_for_form($field);
				$value= isset($this->values[$field->name]) ? "value='" . $this->values[$field->name] . "'" : "";
				echo "<input type='hidden' name='$field_name' $value>\n";
			}
		}

		function show() {

			global $_POST, $MESSAGES;

			if(!$this->visible) return;

			$check_field_ref= "document.forms." . $this->form_name . "." . $this->show_field_name;
			$hidden_field_ref= "document.forms." . $this->form_name . "." . $this->hidden_field_name;

?>
			<table class='search_box'>
<?php

			if(!$this->show_very_simple) {
?>
							<tr>
								<th class='search_box'>
<?php
				echo $this->text;
				echo " ";

				/**
				 * If is extensible, show checkbox to extens/contract it
				 */
				if($this->extensible) {
					$add= $this->active ? "checked" : "";
					echo "<input class='action' type='checkbox' name='" . $this->show_field_name . "' $add
					       onclick='if($check_field_ref.checked) {
					     				$hidden_field_ref.value=\"on\";
					     			} else {
					     				$hidden_field_ref.value=\"off\";
					     			}
					               document.forms." . $this->form_name . ".submit()'>";
				}
?>
								</th>
							</tr>
<?php
			}

			if($this->active) {
?>
							<tr class='search_box'>
								<td class='search_box_fields'>
									<table class='search_box_int' border="0">
<?php
				$i=0;
				foreach($this->fields as $field) {
					if($i == 0) echo "<tr>";
					$i++;

					echo "<td class='search_box'>" . $field->alias . ":&nbsp;</td>";
					echo "<td class='search_box'>";
					$pre= $field->type!="dummy" ? "search_" : "";
					$pre= $this->private_name . "_" . $pre;
					$field->force_show($pre . $field->html_name);
					echo "</td>";

					if($i == $this->cols) {
						$i=0;
						echo "</tr>";
					} else {
						echo "<td class='search_box'>&nbsp;</td>";
					}
				}

				parent::show();
?>
									</table>
								</td>
							</tr>
<?php
				if($this->help_text != "") {
?>
							<tr class='search_box_help'>
								<td class='search_box_help'>
									<?php echo $this->help_text; ?>
								</td>
							</tr>
<?php
				}

				if($this->show_very_simple) {
?>
						</table>
					</td>
					<td>
						<table class='search_box_toolbar'>
							<tr class='search_box_toolbar'>
								<td class='search_box_toolbar'>
									<input type='submit' class='action'
									  value='<?php echo $MESSAGES["BUTTON_SEARCH"]; ?>'
									  title='<?php echo $MESSAGES["BUTTON_SEARCH"]; ?>'
										  onclick='document.forms.<?php echo $this->form_name; ?>.element.value="<?php echo $this->doc_name; ?>";
				                                   document.forms.<?php echo $this->form_name . "." . $this->hidden_field_name; ?>.value="search";
				                                   document.forms.<?php echo $this->form_name; ?>.submit()'>
									<input type='reset' class='action'
			  						  value='<?php echo $MESSAGES["BUTTON_RESET"]; ?>'
									  title='<?php echo $MESSAGES["BUTTON_RESET"]; ?>'>
								</td>
							</tr>
<?php
				} else {

					if($this->show_buttons) {
?>
							<tr class='search_box_toolbar'>
								<td class='search_box_toolbar'>
									<input type='submit' class='action'
									  value='<?php echo $MESSAGES["BUTTON_SEARCH"]; ?>'
									  title='<?php echo $MESSAGES["BUTTON_SEARCH"]; ?>'
									  onclick='document.forms.<?php echo $this->form_name; ?>.element.value="<?php echo $this->doc_name; ?>";
			                                   document.forms.<?php echo $this->form_name . "." . $this->hidden_field_name; ?>.value="search";
			                                   document.forms.<?php echo $this->form_name; ?>.submit()'>
									<input type='reset' class='action'
			  						  value='<?php echo $MESSAGES["BUTTON_RESET"]; ?>'
									  title='<?php echo $MESSAGES["BUTTON_RESET"]; ?>'>
								</td>
							</tr>
<?php
					}
				}
			}
?>
			</table>
<?php

			foreach ($this->fields as $field) {
				if(isset($this->values[$field->name])) {
					$field_name= $this->get_field_name_for_form($field);
					html_set_field_value($this->form_name, $field_name, $this->values[$field->name]);
				}
			}
		}

		function event($event_type) {

			// Values are got form constructor.
			// Now call specific search function, code must be at each inheritance (if needed).
			$this->search();
		}

		// Abstract
		function search() {
		}
	}
?>