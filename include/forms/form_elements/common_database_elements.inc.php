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
	require_once SYSHOME . "/include/forms/form_elements/common_field_elements.inc.php";

	global $MESSAGES;
?>
	<script language="javascript">
		function dw_action(form_name, dw_name, dw_action, row_id) {

			var dw_object;
			var dw_row_id;

			dw_object= "dw_action_" + dw_name;
			dw_row_id= "row_id_" + dw_name;

			if(dw_action == "start_delete") {
				if(!confirm("<?php echo $MESSAGES["CONFIRM_DELETE_REGISTER"]; ?>")) return;
			}

			document.forms[form_name].elements["element"].value= dw_name;
			document.forms[form_name].elements[dw_object].value= dw_action;
			document.forms[form_name].elements[dw_row_id].value= row_id;
			document.forms[form_name].submit();
		}
	</script>
<?php

	/**
	 * Generic method definition for form elements that uses fields.
	 *
	 */
	class common_database_elements extends common_field_elements {

		public $identifier;
		protected $db;
		public $max_lines_per_page;

		protected $shadow;

		public function common_database_elements($doc_name,
												 $fields,
												 $insert_allowed,
												 $update_allowed,
												 $delete_allowed,
												 &$optional_db=null) {

			global $global_db;

			$this->db= is_null($optional_db) ? $global_db : $optional_db;

			parent::common_field_elements($doc_name, $fields);

			// Add shadow funcionality
			$this->shadow= new shadow($this->doc_name);

			// Store into the object the actions allowed:
			$this->insert_allowed= $insert_allowed;
			$this->update_allowed= $update_allowed;
			$this->delete_allowed= $delete_allowed;

			$this->max_lines_per_page= MAX_LINES_PER_PAGE;
		}

		function show() {
			parent::show();

			if(!$this->visible) return;
			return $this->retrieve();
		}

		public function event($event_type) {

			global $MESSAGES;

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
				if(DEBUG) html_showError("Datawindow::ERROR: datawindow::event, event handler not defined. [Action=" . $dw_action . "]");
				else html_showError("DW[7]: Construction error. Contact administrator");
				exit;
			}
		}

		protected function check_action($row_id, $dw_action) {
			// TODO, check
		}

		protected function retrieve() {
			$this->shadow->reset_shadow();
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

		/**
		 * Show a create form for this table definition
		 *
		 * @param ["insert_row" | "update_row"] $action
		 */
		function show_insert_form($action) {
			global $MESSAGES;
			global $PHP_SELF;

			$for_update= ($action == "update_row");

?>
			<center>
			<table class="data_box_external">
<?php
			if(method_exists($this,"pre_show_form")) { ?>
				<tr class="data_box_external"><td>
<?php
				$this->pre_show_form($for_update); ?>
				</td></tr>
<?php
			}
		?>
				<tr class="data_box_external"><td class="data_box_external">
					<table class="data_box_rows">
		<?php
			$first_field="";
			for($i=0; $i<count($this->fields); $i++) {

				if(!$this->fields[$i]->will_be_displayed(true)) {
					continue;
				}

				if($this->fields[$i]->type=="html") {?>
					<tr class='data_box_rows_list'>
						<td class='data_box_edit_cell_alias' colspan='2'><?php
					echo $this->fields[$i]->alias;
					?><br><?php
				} else { ?>
					<tr>
						<td class='data_box_edit_cell_alias' valign='top'><?php
					echo $this->fields[$i]->alias;
					if($this->fields[$i]->required) {
						?><font color='red'> *</font><?php
					}
					?></td><td class='data_box_edit_cell'><?php
				}

				$f_name= $this->fields[$i]->name;
				if(($first_field=="") and ($this->fields[$i]->visible)) {
					$first_field= $f_name;
				}

				$this->fields[$i]->show($f_name,true);

				?></td></tr>
<?php
			}

			if(method_exists($this,"post_show_form")) {
				$this->post_show_form($for_update);
			}
		?>
					</table>
				</td></tr>

				<tr bgcolor="#DDDDDD"><td colspan='2'>
				<center>
		<?php
				echo $this->create_global_action($MESSAGES["BUTTON_ACCEPT"],$action);
				echo $this->create_row_action($MESSAGES["BUTTON_CANCEL"],"retrieve",-1);
		?>
	       		</center>
	       		</td></tr>

			</table>
			</center>
			<script language="JavaScript" type="text/javascript">
				if(document.forms.<?php echo $this->form_name; ?>.<?php echo $first_field; ?>) {
					document.forms.<?php echo $this->form_name; ?>.<?php echo $first_field; ?>.focus();
				}
			</script>

		<?php
			$values= Array();

			for($i=0;$i<count($this->fields);$i++) {
				$this->fields[$i]->set_form_default_value($this->form_name);
			}
		}

		/**
		 * Before inserting, this function is called.
		 *
		 * @param array $values, associative array with the value of each row field. The values can be changed here.
		 * @return integer: 1 = all is OK, continue, 0 = any error, stop inserting (rollback)
		 */
		function pre_insert(&$values) {
			return 1;
		}

		/**
		 * After inserting, this function is called.
		 *
		 * @param array $values, associative array with the value of each row field.
		 * @return integer: 1 = all is OK, continue, 0 = any error, undo inserting (rollback)
		 */
		function post_insert($values) {
			return 1;
		}

		/**
		 * Before updating, this function is called.
		 *
		 * @param integer $row_id, The row identifier
		 * @param array $old_values, associative array with the value of each row field with the current values of the row at database (before update)
		 * @param array $new_values, associative array with the value of each row field with the new value which will replace the old_value. Those values can be changed here.
		 * @return integer: 1 = all is OK, continue, 0 = any error, stop updating (rollback)
		 */
		function pre_update($row_id, $old_values, &$new_values) {
			return 1;
		}

		/**
		 * after updating, this function is called.
		 *
		 * @param integer $row_id, The row identifier
		 * @param array $old_values, associative array with the value of each row field with the old values of the row at database (before update)
		 * @param array $new_values, associative array with the value of each row field with the new value which replaced the old_value.
		 * @return integer: 1 = all is OK, continue, 0 = any error, undo updating (rollback)
		 */
		function post_update($row_id, $old_values, $new_values) {
			return 1;
		}

		/**
		 * Before deleting, this function is called.
		 *
		 * @param integer $row_id, The row identifier
		 * @param array $values, associative array with the value of each row field.
		 * @return integer: 1 = all is OK, continue, 0 = any error, stop deleting (rollback)
		 */
		protected function pre_delete($row_id,$values) {
			return 1;
		}

		/**
		 * After deleting, this function is called.
		 *
		 * @param integer $row_id, The row identifier
		 * @param array $values, associative array with the value of each row field.
		 * @return integer: 1 = all is OK, continue, 0 = any error, undo deleting (rollback)
		 */
		protected function post_delete($row_id,$values) {
			return 1;
		}

		function delete_row($row_id, $values) {
			return 1;
		}

		/**
		 * PROTECTED
		 *
		 * - Fill a values array using the result of a query
		 *
		 * @param $res: The result from a query.
		 * @param $values: Defined previously as array, passed by reference.
		 * @param $real: Determines if the value that must return is the real database.field value or the datawindow.field value
		 */
		function get_values_from_query($res, &$values, $real=0) {

			$row= $this->db->dbms_fetch_array($res);

			for($i=0; $i<count($this->fields); $i++) {

				if($this->fields[$i]->type=="dummy") continue;

				if($real) {
					$values[$this->fields[$i]->name]= $row[$this->fields[$i]->name];
				} else {
					$values[$this->fields[$i]->name]= $this->fields[$i]->get_value($row[$this->fields[$i]->name], false);
				}

				if($this->identifier == $i) {
					$values["row_id"]= $this->fields[$i]->get_value($row[$this->fields[$i]->name], false);
				}
			}
		}

		/**
		 * PROTECTED
		 *
		 * - Fill the values of the row identified by row_id
		 *
		 * @param $row_id: The row identifier
		 * @param $values: Defined previously as array, passed by reference.
		 * @param $real: If want to get the real values or referenced
		 */
		function get_values_from_id($row_id, &$values, $real=false) {


			$query= $this->get_row_query($row_id);

			if(!$res=$this->db->dbms_query($query)) {
				return 0;
			}

			$numRows= $this->db->dbms_num_rows($res);

			if(!$numRows) {
				$this->db->dbms_free_result($res);
				return 0;
			}

			$this->get_values_from_query($res, $values, $real);
			$this->db->dbms_free_result($res);
			return 1;
		}

		/**
		 * Get the values from $row and set them to form fields.
		 *
		 * - Fill a values array using the result of a query
		 *
		 * @param $res: The result from a query.
		 * @param $values: Defined previously as array, passed by reference.
		 */
		protected function get_values_from_row($row, &$values) {

			for($i=0; $i<count($this->fields); $i++) {
//				if(!isset($row[$this->fields[$i]->name])) continue;

				/* ATENCION: Esta funcion se llama desde la datawindow::retrieve.
				   Puede ser un problema si se llama aqui al metodo get_value ya que desde la datawindow se llama
				   a pre_show_row con los valores originales.
				   Si se pone get_value, los campos de tipo foreignkey traducen el valor original al del referenciado,
				   lo que puede conducir a errores.

				$values[$this->fields[$i]->name]= $this->fields[$i]->get_value($row[$this->fields[$i]->name]);
				if($this->identifier == $this->fields[$i]->name) {
					$values["row_id"]= $this->fields[$i]->get_value($row[$this->fields[$i]->name]);
				}
				*/

				if(key_exists($this->fields[$i]->name, $row)) {
					$cur_val= $row[$this->fields[$i]->name];
					$values[$this->fields[$i]->name]= $cur_val;
					if($this->identifier == $i) {
						$values["row_id"]= $cur_val;
					}
				}
			}
		}

		/**
		 * PROTECTED
		 *
		 * Fill the fields of the form using the result of a query
		 *
		 * @param db_result $res
		 */
		function recover_values_from_query($res) {

			$values=Array();
			$row= $this->db->dbms_fetch_array($res);

			for($i=0; $i<count($this->fields); $i++) {

				if($this->fields[$i]->type=="dummy") continue;

				$this->fields[$i]->set_form_value($this->form_name, $this->fields[$i]->get_real_value($row[$this->fields[$i]->name]));
				// $this->fields[$i]->set_form_value($this->form_name, $this->fields[$i]->get_value($row[$this->fields[$i]->name],false));

				if($this->identifier == $i) {
					// html_set_field_value($this->form_name,"row_id_" . $this->doc_name,$this->fields[$i]->get_real_value($row[$this->fields[$i]->name]));
					html_set_field_value($this->form_name,"row_id_" . $this->doc_name,$this->fields[$i]->get_value($row[$this->fields[$i]->name], false));
				}
			}
		}

		protected function show_reload_button() {
			echo $this->get_reload_button();
		}

		protected function show_insert_button() {
			echo $this->get_insert_button();
		}

		protected function show_export_button() {
			echo $this->get_export_button();
		}

		protected function show_update_button($l_row_id) {

			echo $this->get_update_button($l_row_id);
		}

		function show_delete_button($l_row_id) {

			echo $this->get_delete_button($l_row_id);
		}

		protected function get_reload_button() {
			global $MESSAGES;

			$img= (FE_SHOW_ICONS and defined("FE_RELOAD_ICON")) ? FE_RELOAD_ICON : "";
			return $this->create_row_action($MESSAGES["BUTTON_RELOAD"], "retrieve", -1, $img);
		}

		protected function get_insert_button() {
			global $MESSAGES;

			$img= (FE_SHOW_ICONS and defined("FE_INSERT_ICON")) ? FE_INSERT_ICON : "";
			return $this->create_row_action($MESSAGES["BUTTON_NEW"], "start_insert", -1, $img);
		}

		protected function get_export_button() {
			global $MESSAGES;

			$ret="";

			if(FE_SHOW_ICONS and defined("FE_EXPORT_ICON")) {
				$ret= "<img class='action' src='" . FE_EXPORT_ICON . "' " .
						"alt='" . $MESSAGES["BUTTON_EXPORT"] . "' " .
						"title='" . $MESSAGES["BUTTON_EXPORT"] . "' " .
						"align='absmiddle' " .
						"onclick='javascript:dw_action(\"" . $this->form_name . "\", \"" . $this->doc_name . "\", \"start_export\", -1);'>";
			} else {
				$ret= "<input class='action' type='button' " .
						"value='" . $MESSAGES["BUTTON_EXPORT"] . "' " .
						"title='" . $MESSAGES["BUTTON_EXPORT"] . "' " .
						"onclick='javascript:dw_action(\"" . $this->form_name . "\", \"" . $this->doc_name . "\", \"start_export\", -1);'>";
			}

			return $ret;
		}


		protected function get_update_button($l_row_id) {
			global $MESSAGES;

			$img= (FE_SHOW_ICONS and defined("FE_EDIT_ICON")) ? FE_EDIT_ICON : "";
			return $this->create_row_action($MESSAGES["BUTTON_MODIFY"], "start_update", $l_row_id, $img);
		}

		function get_delete_button($l_row_id) {
			global $MESSAGES;

			$img= (FE_SHOW_ICONS and defined("FE_DELETE_ICON")) ? FE_DELETE_ICON : "";
			return $this->create_row_action_with_confirmation($MESSAGES["BUTTON_DELETE"], "start_delete", $l_row_id, $img, $MESSAGES["CONFIRM_DELETE_REGISTER"]);
		}

		protected function get_navigationString($start_in, $numRows) {

			global $MESSAGES;

			$str="";

			$navNeeded=false;

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
						"onclick='$onclick'>";
			} else {
				$msg= "<input type='button' class='action'
						title='$alt'
						value='" . $MESSAGES["BUTTON_BACK"] . "'
						onclick='$onclick'>";
			}

			return $msg;
		}

		protected function create_navigation_back_button($start_in) {

			global $MESSAGES;

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
						"onclick='$onclick'>";
			} else {
				$msg= "<input type='button' class='action'
						title='$alt'
						value='" . $MESSAGES["BUTTON_BACK"] . "'
						onclick='$onclick'>";
			}

			return $msg;
		}

		protected function create_navigation_next_button($start_in) {

			global $MESSAGES;

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
						"onclick='$onclick'>";
			} else {
				$msg= "<input type='button' class='action'
						title='$alt'
						value='" . $MESSAGES["BUTTON_NEXT"] . "'
						onclick='$onclick'>";
			}

			return $msg;
		}

		protected function create_global_action($label, $event_name, $img="") {

			$form_ref= "document.forms." . $this->form_name;
			$row_id_ref= $form_ref . ".row_id_" . $this->doc_name;
			$action_ref= $form_ref . ".dw_action_" . $this->doc_name;

			$onclick="document.forms." . $this->form_name . ".element.value=\"" . $this->doc_name . "\";".
					  "$action_ref.value=\"$event_name\";".
					  "$form_ref.submit()";
			if($img=="") {
				$ret= "<input type='button' class='action' value='$label' onclick='$onclick'>\n";
			} else {
				$ret= "<img class='action' class='action' src='$img' alt='$label' title='$label' align='absmiddle' onclick='$onclick'>";
			}

			return $ret;
		}

		protected function create_navigation_end_button($num_rows) {

			global $MESSAGES;

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
						"onclick='$onclick'>";
			} else {
				$msg= "<input type='button' class='action'
						title='$alt'
						value='" . $MESSAGES["BUTTON_NEXT"] . "'
						onclick='$onclick'>";
			}

			return $msg;
		}

		protected function create_row_action($label, $event_name, $row_id, $img="") {

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
				$ret= "<img class='action' src='$img' alt='$label' title='$label' align='absmiddle' onclick='$onclick'>";
			}

			return $ret;
		}

		protected function create_row_action_with_confirmation($label, $event_name, $row_id, $img="", $confirmation_msg) {

			if($row_id != -1) {
				$shadowed_id= $this->shadow->get_shadow_id($row_id);
				$this->shadow->add_shadowed_id($row_id, $shadowed_id, $event_name);
			} else {
				$shadowed_id = -1;
			}

			$form_ref= "document.forms." . $this->form_name;
			$row_id_ref= $form_ref . ".row_id_" . $this->doc_name;
			$action_ref= $form_ref . ".dw_action_" . $this->doc_name;

			$onclick="if(confirm(\"$confirmation_msg\")) {
						document.forms." . $this->form_name . ".element.value=\"" . $this->doc_name . "\";
					  	$action_ref.value=\"$event_name\";
					  	$row_id_ref.value=\"$shadowed_id\";
					  	$form_ref.submit()
					  }";
			if($img=="") {
				$ret= "<input type='button' class='action' value='$label' onclick='$onclick'>\n";
			} else {
				$ret= "<img class='action' src='$img' alt='$label' title='$label' align='absmiddle' onclick='$onclick'>";
			}

			return $ret;
		}

		protected function create_row_redirection($label, $url, $img="") {

			$link="<a href='". $url . "' target='_blank'>";

			if($img=="") {
				$ret= $link . $label . "</a>";
			} else {
				$ret= "<center>$link<img border='0' class='action' src='$img' alt='$label' title='$label' align='absmiddle'></a></center>";
			}

			return $ret;
		}

		function show_return_form() {
			global $PHP_SELF;
			global $MESSAGES;

			$button_text= $this->allow_save_and_continue ? $MESSAGES["BUTTON_SAVE"] : $MESSAGES["BUTTON_ACCEPT"];
?>
			<br>
			<br>
			<center><input type='button' class='action' value='<?php echo $button_text; ?>' onclick='document.forms.<?php echo $this->form_name; ?>.element.value="<?php echo $this->doc_name; ?>";document.forms.<?php echo $this->form_name; ?>.dw_action_<?php echo $this->doc_name; ?>.value="retrieve";document.forms.<?php echo $this->form_name; ?>.row_id_<?php echo $this->doc_name; ?>.value=-1;document.forms.<?php echo $this->form_name; ?>.submit()'>
<?php
			for($i=0;$i<count($this->fields);$i++) {
				$this->fields[$i]->show_hidden($this->fields[$i]->name);
			}
		}

		function show_hidden() {

			if($this->hidden_shown) return;
?>
				<input type='hidden' name='dw_action_<?php echo $this->doc_name; ?>' value=''>
				<input type='hidden' name='row_id_<?php echo $this->doc_name; ?>' value='-1'>
				<input type='hidden' name='start_in_<?php echo $this->doc_name; ?>' value='0'>
<?php
			// For each field store some hidden information (if it need to do it)
			for($i=0;$i<count($this->fields);$i++) {
				$this->fields[$i]->show_hidden($this->fields[$i]->name);
			}

			parent::show_hidden();
		}
	}

?>
