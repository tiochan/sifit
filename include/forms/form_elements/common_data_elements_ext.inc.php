<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage forms
 *
 */
	include_once INC_DIR . "/shadow.inc.php";
	require_once INC_DIR . "/forms/forms.inc.php";


	/**
	 * Generic method definition for form elements that uses fields.
	 *
	 */
	class common_data_elements_ext extends form_element {

		public $row_selection_enabled= false;  // If true, will display a check_box on each row
		public $identifier;
		public $max_lines_per_page;
		public $save_as_name="";
		public $allow_save_and_continue= false;
		public $show_global_actions_first=false;

		protected $shadow;
		protected $global_actions;

		/**
		 * Creates a new datawindow improved object
		 *
		 * @param string $doc_name
		 * @param datawindow_query $datawindow_query
		 * @return datawindow_ext
		 */
		public function common_data_elements_ext($doc_name) {

			global $global_db;

			// Call parent...
			parent::form_element($doc_name);


			// Add shadow funcionality
			if(!CLI_MODE) $this->shadow= new shadow($this->doc_name);

			// Set default settings
			$this->max_lines_per_page= MAX_LINES_PER_PAGE;
			$this->row_selection_actions= array();
			$this->global_actions= array();

		}

		public function show() {
			parent::show();

			if(!$this->visible) return;
			return $this->retrieve();
		}


		/**
		 * event
		 *
		 * hidden fields:
		 * - dw_action: To store the default action
		 * - row_id: To store the id of the row clicked
		 */
		public function event($event_type) {

			global $MESSAGES;

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			// Is comming from a submit?
			$dw_action= get_http_post_param("dw_action_" . $this->doc_name, 0);
			if(!$dw_action) {
				$this->shadow->reset_shadow();
				return 0;
			}

			// See save_datawindow_action and restore_datawindow_action.
			$shadow_id= get_http_post_param("row_id_" . $this->doc_name, -1);
			if($shadow_id!=-1) {
				$row_id= $this->shadow->get_shadowed_id($shadow_id, $dw_action);
				if($row_id === false) {
					if(DEBUG) html_showError($MESSAGES["NO_ROWS_FOR_ID"] . ": " . $shadow_id);
					return 0;
				}
			} else {
				$row_id= -1;
			}

			$this->check_action($row_id, $dw_action);
			// FIRST, show the general datawindow hidden fields:

			$dw_action= "action_" . $dw_action;

			if(method_exists($this,$dw_action)) {
				$ret=$this->$dw_action($row_id);
				return $ret;
			} else {
				if(DEBUG) html_showError("Datawindow::ERROR: datawindow::event, flow control status not defined. [Action=" . $dw_action . "]");
				else html_showError("DW[7]: Construction error. Contact administrator");
				exit;
			}
		}

		protected function check_action($row_id, $dw_action) {
			// TODO, check
		}

		protected function retrieve() {
			// To implement on inherited classes
		}

		/**
		 * Launched before showing a row. If this function returns false, the row will not be shown.
		 *
		 * @param array $values
		 * @param boolean $can_update [ true = show the update button | false = don't show ]
		 * @param booelan $can_delete [ true = show the delete button | false = don't show ]
		 * @return boolean [ true = show the row | false = don't show the row ]
		 */
		protected function pre_show_row(&$values, &$can_update, &$can_delete) {
			$can_update=1;
			$can_delete=1;
			return 1;
		}

		protected function create_row_action($label, $event_name, $row_id, $img="") {

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			if($row_id != -1) {
				$shadowed_id= $this->shadow->get_shadow_id($row_id);
				$this->shadow->add_shadowed_id($row_id, $shadowed_id, $event_name);
			} else {
				$shadowed_id = -1;
			}

			$form_ref= "document.forms." . $this->form_name;
			$row_id_ref= $form_ref . ".row_id_" . $this->doc_name;
			$action_ref= $form_ref . ".dw_action_" . $this->doc_name;

			$onclick="document.forms." . $this->form_name . ".element.value=\"" . $this->doc_name . "\";".
					  "$action_ref.value=\"$event_name\";".
					  "$row_id_ref.value=\"$shadowed_id\";".
					  "$form_ref.submit()";
			if($img=="") {
				$ret= "<input type='button' class='action' value='$label' onclick='$onclick'>\n";
			} else {
				$ret= "<img class='action' src='$img' alt='$label' title='$label' align='absmiddle' onclick='$onclick'>\n";
			}

			return $ret;
		}

		protected function create_row_action_with_confirmation($label, $event_name, $row_id, $img="", $confirmation_msg) {

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			if($row_id != -1) {
				$shadowed_id= $this->shadow->get_shadow_id($row_id);
				$this->shadow->add_shadowed_id($row_id, $shadowed_id, $event_name);
			} else {
				$shadowed_id = -1;
			}

			$form_ref= "document.forms." . $this->form_name;
			$row_id_ref= $form_ref . ".row_id_" . $this->doc_name;
			$action_ref= $form_ref . ".dw_action_" . $this->doc_name;

			$confirmation_msg= str_replace("'", "&#39;", $confirmation_msg);
			$confirmation_msg= str_replace("\r\n", "\\n\\\r\n", $confirmation_msg);

			$onclick="if(confirm(\"$confirmation_msg\")) {
						document.forms." . $this->form_name . ".element.value=\"" . $this->doc_name . "\";
					  	$action_ref.value=\"$event_name\";
					  	$row_id_ref.value=\"$shadowed_id\";
					  	$form_ref.submit()
					  }";
			if($img=="") {
				$ret= "<input type='button' class='action' value='$label' onclick='$onclick'>\n";
			} else {
				$ret= "<img class='action' src='$img' alt='$label' title='$label' align='absmiddle' onclick='$onclick'>\n";
			}

			return $ret;
		}

		protected function create_row_redirection($label, $url, $img="") {

			$link="<a href='". $url . "' target='_blank'>";

			if($img=="") {
				$ret= $link . $label . "</a>\n";
			} else {
				$ret= "<center>$link<img border='0' class='action' src='$img' alt='$label' title='$label' align='absmiddle'></a></center>\n";
			}

			return $ret;
		}

		protected function create_row_redirection_window($label, $url, $img="", $width=800, $height=780) {

			if($img != "") {
				$ret= "<img class='action' align='absmiddle' title='$label' alt='$label' src='" . $img . "' onclick='javascript:openMyWindow(\"" . $url . "\",$width,$height);'>\n";
			} else {
				$ret= "<input type='button' class='action' align='absmiddle' value='$label' onclick='javascript:openMyWindow(\"" . $url . "\",$width,$height);'>\n";
			}

			return $ret;
		}

		/**
		 * Echo the values of each field.
		 * If the identifier field is set, returns the value of it, else return
		 * ""
		 *
		 * @param unknown_type $values
		 * @return mixed
		 */
		protected function show_row($values) {
			// To implement on inherited classes
		}

		protected function show_reload_button() {

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			echo $this->get_reload_button();
		}

		protected function show_insert_button() {

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			echo $this->get_insert_button();
		}

		protected function show_export_button() {

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			echo $this->get_export_button();
		}

		protected function show_update_button($l_row_id) {

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			echo $this->get_update_button($l_row_id);
		}

		protected function show_delete_button($l_row_id) {

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			echo $this->get_delete_button($l_row_id);
		}

		protected function get_reload_button() {
			global $MESSAGES;

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			$img= (FE_SHOW_ICONS and defined("FE_RELOAD_ICON")) ? FE_RELOAD_ICON : "";
			return $this->create_row_action($MESSAGES["BUTTON_RELOAD"], "retrieve", -1, $img);
		}

		protected function get_insert_button() {
			global $MESSAGES;

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			$img= (FE_SHOW_ICONS and defined("FE_INSERT_ICON")) ? FE_INSERT_ICON : "";
			return $this->create_row_action($MESSAGES["BUTTON_NEW"], "start_insert", -1, $img);
		}

		protected function get_export_button() {
			global $MESSAGES;

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			$img= (FE_SHOW_ICONS and defined("FE_EXPORT_ICON")) ? FE_EXPORT_ICON : "";
			return $this->create_row_action($MESSAGES["BUTTON_EXPORT"], "start_export", -1, $img);
		}

		protected function get_update_button($l_row_id) {
			global $MESSAGES;

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			$img= (FE_SHOW_ICONS and defined("FE_EDIT_ICON")) ? FE_EDIT_ICON : "";
			return $this->create_row_action($MESSAGES["BUTTON_MODIFY"], "start_update", $l_row_id, $img);
		}

		public function get_delete_button($l_row_id) {
			global $MESSAGES;

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			$img= (FE_SHOW_ICONS and defined("FE_DELETE_ICON")) ? FE_DELETE_ICON : "";
			return $this->create_row_action_with_confirmation($MESSAGES["BUTTON_DELETE"], "start_delete", $l_row_id, $img, $MESSAGES["CONFIRM_DELETE_REGISTER"]);
		}

		public function get_action_button($img,$title,$form_var, $value) {

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			$img= $img != "" ? $img : FE_DEFAULT_ACTION_ICON;

			$ret="<img class='action' src='" . $img . "'
				  alt='" . $title . "'
				  title='" . $title . "'
				  align='absmiddle'
				  onclick='document.forms." . $this->form_name .".element.value=\"". $this->doc_name . "\";
						   document.forms." . $this->form_name . "." . $form_var . ".value=\"$value\";
						   document.forms." . $this->form_name . ".submit()'>";

			return $ret;
		}

		protected function get_navigationString($start_in, $numRows) {

			global $MESSAGES;

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			$str="";

			$navNeeded=false;		// If all rows can be shown in a single page, no navigation is needed

			if($start_in > 0) {
				$navNeeded=true;
				$msg1= $this->create_navigation_first_button();
				$nl= $start_in - $this->max_lines_per_page;
				$msg1.= $this->create_navigation_back_button($nl);
			}

			$numRowsShown= $numRows - $start_in;
			if($numRowsShown > $this->max_lines_per_page) {
				$navNeeded=true;
				$nl= $start_in + $this->max_lines_per_page;
				$msg2= $this->create_navigation_next_button($nl);
				$msg2.= $this->create_navigation_end_button($numRows);
			}
			$finalRowsShown= min($numRowsShown,$this->max_lines_per_page);

			if($navNeeded) {
				if(isset($msg1)) $str.= $msg1 . "\n";
				if(isset($msg2)) $str.= $msg2 . "\n";

				$rowsMSG= sprintf($MESSAGES["INFO_ROWS_SHOWN"], $start_in + 1, $start_in + $finalRowsShown, $numRows);
				$str.= "&nbsp;&nbsp;" . $rowsMSG;
			}

			return $str;
		}

		protected function create_navigation_first_button() {

			global $MESSAGES;

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			$onclick="document.forms." . $this->form_name . ".element.value=\"" . $this->doc_name . "\";" .
					"document.forms." . $this->form_name . ".dw_action_" . $this->doc_name . ".value=\"retrieve\";" .
					"document.forms." . $this->form_name . ".start_in_" . $this->doc_name . ".value=\"0\";" .
					"document.forms." . $this->form_name . ".submit()";

			$alt= sprintf($MESSAGES["FIRST_ROW"], $this->max_lines_per_page);


			if(FE_SHOW_ICONS and defined("FE_FIRST_ICON")) {
				$msg="<img class='action' src='" . FE_FIRST_ICON . "' ".
						"alt='$alt' " .
						"title='$alt' " .
						"align='absmiddle' " .
						"onclick='$onclick'>\n";
			} else {
				$msg= "<input type='button'	class='action'
						title='$alt'
						value='" . $MESSAGES["BUTTON_BACK"] . "'
						onclick='$onclick'>\n";
			}

			return $msg;
		}

		protected function create_navigation_back_button($start_in) {

			global $MESSAGES;

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			$onclick="document.forms." . $this->form_name . ".element.value=\"" . $this->doc_name . "\";" .
					"document.forms." . $this->form_name . ".dw_action_" . $this->doc_name . ".value=\"retrieve\";" .
					"document.forms." . $this->form_name . ".start_in_" . $this->doc_name . ".value=\"$start_in\";" .
					"document.forms." . $this->form_name . ".submit()";

			$alt= sprintf($MESSAGES["PREV_ROWS"], $this->max_lines_per_page);


			if(FE_SHOW_ICONS and defined("FE_BACK_ICON")) {
				$msg="<img class='action' src='" . FE_BACK_ICON . "' ".
						"alt='$alt' " .
						"title='$alt' " .
						"align='absmiddle' " .
						"onclick='$onclick'>\n";
			} else {
				$msg= "<input type='button' class='action'
						title='$alt'
						value='" . $MESSAGES["BUTTON_BACK"] . "'
						onclick='$onclick'>\n";
			}

			return $msg;
		}

		protected function create_navigation_next_button($start_in) {

			global $MESSAGES;

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			$onclick="document.forms." . $this->form_name . ".element.value=\"" . $this->doc_name . "\";" .
					"document.forms." . $this->form_name . ".dw_action_" . $this->doc_name . ".value=\"retrieve\";" .
					"document.forms." . $this->form_name . ".start_in_" . $this->doc_name . ".value=\"$start_in\";" .
					"document.forms." . $this->form_name . ".submit()";

			$alt= sprintf($MESSAGES["NEXT_ROWS"], $this->max_lines_per_page);

			if(FE_SHOW_ICONS and defined("FE_NEXT_ICON")) {
				$msg="<img class='action' src='" . FE_NEXT_ICON . "' ".
						"alt='$alt' " .
						"title='$alt' " .
						"align='absmiddle' " .
						"onclick='$onclick'>\n";
			} else {
				$msg= "<input type='button' class='action'
						title='$alt'
						value='" . $MESSAGES["BUTTON_NEXT"] . "'
						onclick='$onclick'>\n";
			}

			return $msg;
		}

		protected function create_navigation_end_button($num_rows) {

			global $MESSAGES;

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			$start_in= $num_rows - $this->max_lines_per_page;

			$onclick="document.forms." . $this->form_name . ".element.value=\"" . $this->doc_name . "\";" .
					"document.forms." . $this->form_name . ".dw_action_" . $this->doc_name . ".value=\"retrieve\";" .
					"document.forms." . $this->form_name . ".start_in_" . $this->doc_name . ".value=\"$start_in\";" .
					"document.forms." . $this->form_name . ".submit()";

			$alt= sprintf($MESSAGES["LAST_ROW"], $this->max_lines_per_page);

			if(FE_SHOW_ICONS and defined("FE_LAST_ICON")) {
				$msg="<img class='action' src='" . FE_LAST_ICON . "' ".
						"alt='$alt' " .
						"title='$alt' " .
						"align='absmiddle' " .
						"onclick='$onclick'>\n";
			} else {
				$msg= "<input type='button' class='action'
						title='$alt'
						value='" . $MESSAGES["BUTTON_NEXT"] . "'
						onclick='$onclick'>\n";
			}

			return $msg;
		}

		protected function show_global_actions() {

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			foreach($this->global_actions as $text=>$action_item) {
				$img= $action_item[0];
				$action= $action_item[1];
				$custom_show_function= $action_item[2];
				if($custom_show_function!="") {
					$this->$custom_show_function($text, $img, $action);
				} else {
					echo $this->create_global_action($text,$action,$img);
				}
			}
		}

		/**
		 *
		 * Show the buttons for an INSERT/UPDATE form.
		 * @param string $action, the action to send on event.
		 */
		protected function show_insert_buttons($action) {

			global $MESSAGES;

			if($this->allow_save_and_continue) {
				echo $this->create_global_action($MESSAGES["BUTTON_SAVE"],$action);
				echo $this->create_row_action($MESSAGES["BUTTON_CLOSE"],"retrieve",-1);
			} else {
				echo $this->create_global_action($MESSAGES["BUTTON_ACCEPT"],$action);
				echo $this->create_row_action($MESSAGES["BUTTON_CANCEL"],"retrieve",-1);
			}
		}

		protected function show_select_box($row_id) {

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			if($this->row_selection_enabled) {
				$name= $this->doc_name . '_check_' . $row_id;
?>
				<td class='data_box_cell'>
					<center>
					<input type='checkbox' class='action' name='<?php echo $name; ?>' <?php if(isset($_POST[$name])) echo "checked"; ?>>
					</center>
				</td>
<?php			}
		}

		protected function create_group_actions_select() {

			global $MESSAGES;

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			if(!$this->row_selection_enabled) return "";

			$pre_name= $this->doc_name . "_check_";
			$ret="<div style='display:inline'>\n";
			$ret.="&nbsp;<b>" . $MESSAGES["SELECT_TITLE"] . " [</b> " .
				"<a href='javascript:void(0)' onclick='check_all_checkboxes(\"$pre_name\")'>" . $MESSAGES["SELECT_ALL"] . "</a>\n".
				" <b>|</b> " .
				"<a href='javascript:void(0)' onclick='uncheck_all_checkboxes(\"$pre_name\")'>" . $MESSAGES["SELECT_NONE"] . "</a>\n".
				" <b>|</b> ".
				"<a href='javascript:void(0)' onclick='invert_checkboxes(\"$pre_name\")'>" . $MESSAGES["SELECT_INVERT"] . "</a>\n".
				" <b>]</b>";

			$form_ref= "document.forms." . $this->form_name;
			$row_id_ref= $form_ref . ".row_id_" . $this->doc_name;
			$action_ref= $form_ref . ".dw_action_" . $this->doc_name;

			$onchange="document.forms." . $this->form_name . ".element.value=\"" . $this->doc_name . "\";".
					  "$action_ref.value=\"group_selected\";".
					  "$form_ref.submit()";

			$ret.="&nbsp;<b>" . $MESSAGES["APPLY_SELECTED"] . ":</b>&nbsp;\n";
			$ret.="<div style='display:inline; position:absolute'><select class='action' name='group_select_action_" . $this->doc_name . "' onchange='$onchange'>\n";
			$ret.="<option></option>\n";

			foreach($this->row_selection_actions as $title => $action) {
				$ret.="<option value='$action'>$title</option>\n";
			}
			$ret.="</select></div>\n";
			$ret.="</div>\n";



			return $ret;
		}

		protected function create_global_action($label, $event_name, $img="") {

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			$form_ref= "document.forms." . $this->form_name;
			$row_id_ref= $form_ref . ".row_id_" . $this->doc_name;
			$action_ref= $form_ref . ".dw_action_" . $this->doc_name;

			$onclick="document.forms." . $this->form_name . ".element.value=\"" . $this->doc_name . "\";".
					  "$action_ref.value=\"$event_name\";".
					  "$form_ref.submit()";
			if($img=="") {
				$ret= "<input type='button' class='action' value='$label' onclick='$onclick'>\n";
			} else {
				$ret= "<img class='action' src='$img' alt='$label' title='$label' align='absmiddle' onclick='$onclick'>\n";
			}

			return $ret;
		}

		protected function show_return_form() {
			global $MESSAGES;

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			echo $this->create_row_action($MESSAGES["BUTTON_ACCEPT"],"retrieve",-1);
		}

		public function show_hidden() {

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			parent::show_hidden();
		}

		/**
		 * Protected function. Will return the background color of a row.
		 * You can write your own on your inherited class.
		 *
		 * @param boolean $EvenRow
		 * @param array $values
		 * @return string
		 */
		protected function get_row_color($EvenRow, $values) {

			return $EvenRow ? "#F2F2F2" : "#FFFFFF";
		}

		/**
		 * Protected function. Will return the background color of a cell.
		 * If you want to change default, write your own on your inherited class.
		 *
		 * @param field_ext $field
		 * @param mixed $value
		 * @return string
		 */
		protected function get_cell_color($key, $value) { return ""; }
	}
?>