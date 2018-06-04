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
	class search_box extends common_field_elements {

		public $text;
		public $fields;

		public $help_text;
		public $cols;

		public $values;
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
		function search_box($fields, $name, $text, $cols=1, $extensible=true) {
			global $MESSAGES;

			parent::form_element($name);

			$this->text= ($text != "") ? $text : $MESSAGES["BUTTON_SEARCH"];
			$this->fields= $fields;
			$this->values= Array();
			$this->help_text= $MESSAGES["SEARCH_CAN_USE_WILDCARDS"];
			$this->cols= $cols;
			$this->extensible= $extensible;

			$this->show_field_name= "search_action_" . $this->doc_ref;
			$this->hidden_field_name= "hidden_search_action_" . $this->doc_ref;

			$this->status= get_http_post_param($this->hidden_field_name, "off");
			$this->active= $this->is_active();
			$this->searching= ($this->status == "search");

			/* For code debug
				echo "First time: " . ($this->first_time? "true":"false") . "<br>";
				echo "Active: " . ($this->active? "true":"false") . "<br>";
				echo "searching?: " . ($this->searching? "true":"false") . "<br>";
			*/

			$this->get_values();
		}

		public function set_extensible($extensible) {

			$this->extensible= $extensible;
			$this->active= $this->is_active();
		}

		protected function is_active() {

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

		/**
		 * Get values from last call:
		 *
		 * @return array
		 */
		protected function get_values() {

			// Get values from last call:
			foreach ($this->fields as $field) {
				$key= $field->name;
				$pre= $field->type!="dummy" ? "search_" : "";
				$aux= $this->get_value_from_get($pre . $field->name);

				if($aux === false) continue;
				$this->values[$field->name]= $aux;
			}

			return $this->values;
		}

		/**
		 * Using current values array, calculates the restriction query
		 *
		 */
		public function get_restriction() {

			$this->restriction= "";

			// If the searchbox is not active, then no restriction
			if(!$this->active) return "";

			// If is the first time that is loaded and no default values were given ...
			if(($this->first_time) and count($this->values)==0) { return ""; }
			$add="";

			// Get values from last call:
			foreach ($this->fields as $field) {
				if(!key_exists($field->name, $this->values)) continue;
				if($field->type=="dummy") continue;

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

		function get_value_from_get($field_name) {

			// Parameter can be passed via POST or GET
			if(isset($_POST[$field_name])) return $_POST[$field_name];
			if(isset($_GET[$field_name])) return $_GET[$field_name];

			// Hidden can be only passed via POST
			if(isset($_POST["hidden_" . $field_name])) return $_POST["hidden_" . $field_name];

			return false;
		}

		function show_hidden() {

			if($this->hidden_shown) return;
			parent::show_hidden();

			?>

				<input type='hidden' name='<?php echo $this->hidden_field_name; ?>' value='<?php echo $this->status; ?>'>
			<?php

			foreach($this->fields as $field) {
				$name= $field->name;
			?>

				<input type='hidden' name='hidden_search_<?php echo $name; ?>' <?php if(isset($this->values[$field->name])) echo "value='" . $this->values[$field->name] . "'";?>>
			<?php
			}
		}

		function show() {

			global $_POST, $MESSAGES;

			if(!$this->visible) return;

			$check_field_ref= "document.forms." . $this->form_name . "." . $this->show_field_name;
			$hidden_field_ref= "document.forms." . $this->form_name . "." . $this->hidden_field_name;

?>
			<table class='search_box'>
				<th class='search_box'>
<?php
			echo $this->text;
			echo " ";

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
<?php

			if($this->active) {
?>
				<tr class='search_box'>
					<td class='search_box_fields'>
						<table border="0">
<?php
				$i=0;
				foreach($this->fields as $field) {
					if($i == 0) echo "<tr class='search_box'>";
					$i++;

					echo "<td class='search_box'>" . $field->alias . "</td>";
					echo "<td class='search_box'>";
					$pre= $field->type!="dummy" ? "search_" : "";
					$field->force_show($pre . $field->name);
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
?>
				<tr class='search_box_toolbar'>
					<td class='search_box_toolbar'>
						<input type='submit' class='action'
						  value='<?php echo $MESSAGES["BUTTON_SEARCH"]; ?>'
						  title='<?php echo $MESSAGES["BUTTON_SEARCH"]; ?>'
						  onclick='document.forms.<?php echo $this->form_name; ?>.element.value="<?php echo $this->doc_name; ?>";
								   document.forms.<?php echo "$this->form_name." . $this->hidden_field_name; ?>.value="search";
								   document.forms.<?php echo $this->form_name; ?>.submit()'>
						<input type='reset' class='action'
  						  value='<?php echo $MESSAGES["BUTTON_RESET"]; ?>'
						  title='<?php echo $MESSAGES["BUTTON_RESET"]; ?>'>
					</td>
				</tr>
<?php
		}
?>
			</table>
<?php
			foreach ($this->fields as $field) {
				if(isset($this->values[$field->name])) {
					$pre= $field->type!="dummy" ? "search_" : "";
					html_set_field_value($this->form_name, $pre . $field->name, $this->values[$field->name]);
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