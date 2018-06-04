<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage forms
 *
 */


	include_once SYSHOME . "/conf/forms.conf.php";
	include_once SYSHOME . "/include/forms/form_elements.inc.php";
	include_once SYSHOME . "/include/forms/form_elements/button.inc.php";
	include_once SYSHOME . "/include/lookups/lookup.inc.php";

	// Global Identifier for new objects as to set their own UNIQUE object reference
	global $GLOBAL_ID;
	$GLOBAL_ID=0;

	function get_new_id() {
		global $GLOBAL_ID;

		$GLOBAL_ID++;
		return $GLOBAL_ID;
	}


	class accept_button extends button {

		function clicked() {
			// Do you need something here?
		}
	}

	class form {

		public $name;
		public $form_name;
		public $descents;

		public $form_action;

		public $lookup_link;


		protected $reload_button;
		protected $form_shown;
		protected $nav_sequence;
		protected $nav_control;

		protected $hidden_fields;


		/**
		 * form
		 *
		 * @param string name
		 * @return form
		 */
		function form($name) {

			global $_SERVER;
			global $MESSAGES;
			global $global_nav_sequence;
			global $global_nav_control;

			$this->name= $name;
			$this->form_name= $name;
			$this->form_id="frm_" . rand(1000000,getrandmax());
			$this->form_shown= false;

			$this->form_action= $_SERVER['PHP_SELF'];

			$this->descents= Array();

			/*
				TODO; use session to store/restore form_id to improve the security:
				- use if(isset($HTTP_POST_VARS["form_id"])) { ... }
				- use form_id to register at session $_SESSION($this->form_id);
				- recover at return and check it.
			*/

			$reload_image= (FE_SHOW_ICONS and defined("FE_RELOAD_ICON")) ? FE_RELOAD_ICON : $MESSAGES["BUTTON_RELOAD"];

			$this->reload_button=new accept_button("form_accept",$MESSAGES["BUTTON_RELOAD"], $reload_image);
			$this->reload_button->parent=$this;

			$this->nav_sequence= &$global_nav_sequence;
			$this->nav_control= &$global_nav_control;

			// Lookup module
			$this->lookup_link= new lookup_form_field("lookup" . $name);
			$this->add_element($this->lookup_link);
		}

		/**
		 *  PUBLIC
		 *
		 *	Control forms flow...
		 *  The form vars that indicates the status are:
		 *    - action
		 *    - element
		 *
		 */
		function form_control() {

			global $_POST;
			global $MESSAGES;
			global $global_nav;

//			html_showHeader();

			$this->propagate_form_name();

			$this->show_form();

			// Check if there is an event comming...
			if(!$this->is_reload() and (isset($_POST["element"]) and $_POST["element"]!= "")) {

				$element=$_POST["element"];
				$event= isset($_POST["event"]) ? $_POST["event"] : "";

				// Which element generated the event?
				if($element == $this->reload_button->doc_ref) {
					$ret= $this->reload_button;
				} else {
					// Propagate the event to this element
					if(!$this->exists_element($element, $ret)) {
						html_showError("ERROR: form::form_control, element not found ($element).");
						if(DEBUG) {
							echo "<h3>Debug information</h3><hr>";
							echo "<b>POST content</b>";
							print_object($_POST);
						}
						exit;
					}
				}

				// What happen? I dont know, so launch the usual show element
				if(!$ret->event($event)) $this->show();

			} else {
				// No events, show the element
				$this->show();
			}

			echo "</form>";
			$this->post_show_form();
		}

		protected function propagate_form_name() {

			foreach($this->descents as $elem) {
				$elem->propagate_form_name($this->form_name);
			}
		}

		public function get_form_name() {
			return $this->form_name;
		}

		/**
		 * Show the form (and each element)
		 *
		 * @return integer, 0=any error happend.
		 */
		function show() {

			global $MESSAGES;

			// Perhaps the inherited class want to put something here! (hidden fields, buttons, etc.)
			if(method_exists($this,"add_to_form")) {
				// Does the user needs to show its own form?
				if(method_exists($this, "own_form")) $this->own_form();

				$this->show_form();
				$this->add_to_form();
			}

			// Show the elements added to the form (buttons, etc.)
			foreach ($this->descents as $elem) $elem->show();
		}

		function show_form() {

			global $global_sequence;

			if($this->form_shown) return;

			/* Hidden fields:
			 * - form_id: The unique form identifier
			 * - form_name: The form name
			 * - element: To store the element name (if any) of the element that did an event
			 * - event: To store the event name (if any) of the element
			 */
?>

			<form name="<?php echo $this->form_name; ?>" method='post' action="<?php echo $this->form_action; ?>" enctype="multipart/form-data">
				<!-- FORM CONTROL FIELDS -->
				<input type='hidden' name='form_id' value='<?php echo $this->form_id; ?>'>
				<input type='hidden' name='form_name' value='<?php echo $this->form_name; ?>'>
				<input type='hidden' name='element' value=''>
				<input type='hidden' name='event' value=''>
				<input type='hidden' name='<?php echo APP_NAME ?>_sequence' value='<?php echo $this->nav_sequence->get_current_sequence(); ?>'>
				<!-- START HIDDEN FIELDS -->
<?php
			$this->show_hidden();

			foreach ($this->descents as $elem) {
				$elem->show_hidden($this->form_name);
			}
?>

				<!-- END HIDDEN FIELDS -->
<?php

			$this->form_shown= true;
		}

		function post_show_form() {
			foreach ($this->descents as $elem) {
				$elem->post_show_form($this->form_name);
			}
		}

		function show_hidden() {

		}

		function is_reload() {
			return $this->nav_sequence->is_reload();
		}

		function exists_element($element, &$ret) {
			$ret=null;

			foreach ($this->descents as $elem) {
				if($elem->exists_element($element, $ret)) break;
			}

			return $ret;
		}

		/**
		 * add_element adds a new element to the form
		 *
		 * @param form_element $element
		 */
		function add_element($element) {

			$name= trim($element->doc_ref);

			if($name != "") {
				$this->descents[$name]= $element;
				$this->descents[$name]->parent= $this;
				$this->descents[$name]->adopted();
				$this->descents[$name]->form_name= $this->form_name;
			}
		}

		protected function will_be_shown() {

			return true;

		}

		/**
		 * change_element change a property of an element
		 *
		 * @param form_element $element
		 */
		function change_element($element, $property, $value) {

			$ret=null;

			foreach ($this->descents as $elem) {
				if($elem->exists_element($element, $ret)) {
					break;
				}
			}

			if(!$ret) {
				return 0;
			}

			$ret->set_property($property, $value);
		}

		function replace_element($element_name, &$new_element) {

			foreach($this->descents as $key => $elem) {
				if($elem->doc_name == $element_name) {
					unset($this->descents[$key]);
					$this->add_element($new_element);
					return true;
				}
			}


			foreach ($this->descents as $elem) {

				if($elem->replace_element($element_name, $new_element)) {
					return true;
				}
			}
			return false;
		}

		function show_hidden_field($field_name, $value="") {

			if(!isset($this->hidden_fields[$field_name])) {
				echo "<input type='hidden' name='$field_name' value='$value'>";
				$this->hidden_fields[$field_name]= $value;
			}

		}
	}

?>