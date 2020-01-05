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
	require_once INC_DIR . "/forms/form_elements/common_data_elements_ext.inc.php";


	/**
	 * Generic method definition for form elements that uses fields.
	 *
	 */
	class common_database_elements_ext extends common_data_elements_ext {


		/**
		 * @var dbms_class
		 */
		public $db;
		/**
		 * @var datawindow_query
		 */
		protected $datawindow_query;

		/**
		 * Creates a new datawindow improved object
		 *
		 * @param string $doc_name
		 * @param datawindow_query $datawindow_query
		 * @return datawindow_ext
		 */
		public function common_database_elements_ext($doc_name, & $datawindow_query) {

			global $global_db;

			// Save parameters into local properties
			$this->db= $datawindow_query->db;
			$this->datawindow_query= $datawindow_query;

			// Call parent...
			parent::common_data_elements_ext($doc_name);
		}

		public function add_group_action($text,$action) {

			$this->row_selection_actions[$text]= $action;
		}

		public function add_global_action($text,$icon,$action,$custom_show_function="") {

			$this->global_actions[$text]= array($icon,$action,$custom_show_function);
		}

		/**
		 * method executed from group selection
		 *
		 * @param mixed $not_necessary
		 * @return mixed
		 */
		protected function action_group_selected ($not_necessary) {

			// No actions allowed on CLI mode
			if(CLI_MODE) return 0;

			$group_action= get_http_post_param("group_select_action_" . $this->doc_name, 0);

			if(!$group_action) return 0;

			// TODO: Restore row_id from session, instead _POST
			// See save_datawindow_action and restore_datawindow_action.

			$this->check_action($not_necessary, $group_action);
			// FIRST, show the general datawindow hidden fields:

			$group_action= "action_" . $group_action;


			if(method_exists($this,$group_action)) {
				$row_ids= $this->get_checked_row_ids();
				return $this->$group_action($row_ids);
			} else {
				if(DEBUG) html_showError("Datawindow::ERROR: datawindow::event, flow control status not defined. [Action=" . $group_action . "]");
				else html_showError("DW[71]: Construction error. Contact administrator");
				exit;
			}
		}

		protected function retrieve() {

			if(!CLI_MODE) $this->shadow->reset_shadow();

			parent::retrieve();
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

			$id= "";


			$field_values= array();
			foreach($this->datawindow_query->fields as $field) {
				if($field->name == "") continue;
				$field_values[$field->name]= isset($values[$field->name]) ? $field->get_value($values[$field->name]) : null;
			}

			$fields= $this->datawindow_query->get_visible_fields(false,false);

			foreach($fields as $field) {

				if(($field->name=="") or (!$field->visible)) continue;

				$value= $field_values[$field->name];

				$cell_color= $this->datawindow_query->get_cell_color($field, $value);
				if($cell_color == "") $cell_color= $this->get_cell_color($field, $value);
				$cell_color= $cell_color != "" ? "bgcolor=$cell_color" : "";
				?><td <?php echo $cell_color; ?> class='data_box_cell' <?php echo $field->col_modifier; ?>><?php echo $value; ?></td>
				<?php
			}

			if($this->datawindow_query->field_id !== null) {
				$field= $this->datawindow_query->fields[$this->datawindow_query->field_id];
				$id= $field->get_real_value($values[$field->name]);
			}

			return $id;
		}

		/**
		 * Echo the values of each field.
		 * If the identifier field is set, returns the value of it, else return
		 * ""
		 *
		 * @param unknown_type $values
		 * @return mixed
		 */
		protected function get_show_row_no_tabular($values, & $id) {

			$id= "";
			$to_print= "";


			$field_values= array();
			foreach($this->datawindow_query->fields as $field) {
				if($field->name == "") continue;
				$field_values[$field->name]= isset($values[$field->name]) ? $field->get_value($values[$field->name]) : null;
			}

			$fields= $this->datawindow_query->get_visible_fields(false,false);

			foreach($fields as $field) {

				if(($field->name=="") or (!$field->visible)) continue;

				$value= $field_values[$field->name];

				$cell_color= $this->datawindow_query->get_cell_color($field, $value);
				if($cell_color == "") $cell_color= $this->get_cell_color($field, $value);
				$cell_color= $cell_color != "" ? "bgcolor=$cell_color" : "";

				if($field->type == "html") {
					$to_print.="<tr class='data_box_rows_list' bgcolor='white'><td class='data_box_rows_list_alias' colspan='2'><b>" . $field->alias . "</b></td></tr><tr><td $cell_color colspan='2' class='data_box_cell_value' " . $field->col_modifier . ">$value</td></tr>";
				} else {
					$to_print.="<tr class='data_box_rows_list' bgcolor='white'><td class='data_box_rows_list_alias'><b>" . $field->alias . "</b></td><td $cell_color class='data_box_cell_value' " . $field->col_modifier . ">$value</td></tr>";
				}
			}

			if($this->datawindow_query->field_id !== null) {
				$field= $this->datawindow_query->fields[$this->datawindow_query->field_id];
				$id= $field->get_real_value($values[$field->name]);
			}

			return $to_print;
		}

		/**
		 * Show a create form for this table definition
		 *
		 * @param ["insert_row" | "update_row"] $action
		 */
		function show_insert_form($action, $message = "") {
			global $MESSAGES;
			global $PHP_SELF;

			$controlled_action=0;

			if($message == "") {
				if($action == "insert_row") {
					$message= $MESSAGES["NEW_INFO"];
					$controlled_action=1;
				}

				if($action == "update_row") {
					$message= $MESSAGES["MODIFY_INFO"];
					$controlled_action=1;
				}
			}


			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			$for_update= ($action == "update_row");

			$width= ($this->width) ? " width='$this->width'" : "";

?>			<br>
			<center>
			<table class="data_box_external" <?php echo $width; ?>>
<?php
			if($this->table_title != "") {
?>				<tr class="data_box_title">
					<td align="left" class="data_box_title" colspan="3"><?php echo $this->table_title; ?></td>
				</tr>
<?php
			}

			if(method_exists($this,"pre_show_form")) {
				echo "<tr><td>";
				$this->pre_show_form($for_update);
				echo "</td></tr>";
			}

?>
				<tr><td colspan="3">
					<table class="data_box_rows" cellspacing="0">
						<tr class='data_box_rows_list_header'>
							<th class='data_box_rows_list_header'><?php echo $message; ?></th>
							<th class='data_box_rows_list_header'><?php $this->show_insert_buttons($action); ?></th>
						</tr>
<?php
			$field_list= $this->datawindow_query->get_visible_fields(!$for_update, $for_update);
			$first_field= $this->show_insert_fields($field_list, $for_update);

			if(method_exists($this,"post_show_form")) {
				$this->post_show_form($for_update);
			}
?>
						<tr>
							<td class='data_box_rows_list_footer' colspan='2'>
<?php
			$this->show_insert_buttons($action);
?>
		       				</td>
       					</tr>
					</table>
				</td></tr>
			</table>
			</center>
<?php
			if($first_field !== null) $first_field->set_focus($this->form_name);
			foreach($this->datawindow_query->fields as $field) $field->set_form_default_value($this->form_name);
		}

		protected function & show_insert_fields($field_list, $for_update) {

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			$first_field= null;

			foreach($field_list as $field) {

				if(!$field->will_be_displayed(!$for_update, $for_update)) continue;
?>
				<tr class='data_box_rows_list' bgcolor='white'>
					<td class='data_box_cell_alias'>
						<b>
						<?php
						echo $field->alias;
						if($field->required) {
?>
							<font color='red'>*</font>
<?php
						}
?>
						</b></td>
					<td class='data_box_cell_value'>
<?php

				if(($first_field === null) and ($field->visible)) {
					$first_field= $field;
				}

				$field->show(!$for_update, $for_update);

?>
					</td>
				</tr>
<?php
			}

			return $first_field;
		}

		/**
		 * Before inserting, this function is called.
		 *
		 * @param array $values, associative array with the value of each row field. The values can be changed here.
		 * @return integer: 1 = all is OK, continue, 0 = any error, stop inserting (rollback)
		 */
		protected function pre_insert(&$values) {

			// No actions allowed on CLI mode
			if(CLI_MODE) return 0;

			return 1;
		}

		/**
		 * After inserting, this function is called.
		 *
		 * @param array $values, associative array with the value of each row field.
		 * @return integer: 1 = all is OK, continue, 0 = any error, undo inserting (rollback)
		 */
		protected function post_insert($values) {

			// No actions allowed on CLI mode
			if(CLI_MODE) return 0;

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
		protected function pre_update($row_id, $old_values, &$new_values) {

			// No actions allowed on CLI mode
			if(CLI_MODE) return 0;

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
		protected function post_update($row_id, $old_values, $new_values) {

			// No actions allowed on CLI mode
			if(CLI_MODE) return 0;

			return 1;
		}

		/**
		 * after updating, once commit is executed, this function is called.
		 *
		 * @param integer $row_id, The row identifier
		 * @param array $old_values, associative array with the value of each row field with the old values of the row at database (before update)
		 * @param array $new_values, associative array with the value of each row field with the new value which replaced the old_value.
		 */
		protected function update_commited($row_id, $old_values, $new_values) {

		}

		/**
		 * Before deleting, this function is called.
		 *
		 * @param integer $row_id, The row identifier
		 * @param array $values, associative array with the value of each row field.
		 * @return integer: 1 = all is OK, continue, 0 = any error, stop deleting (rollback)
		 */
		protected function pre_delete($row_id,$values) {

			// No actions allowed on CLI mode
			if(CLI_MODE) return 0;

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

			// No actions allowed on CLI mode
			if(CLI_MODE) return 0;

			return 1;
		}

		protected function delete_row($row_id, $values) {

			// No actions allowed on CLI mode
			if(CLI_MODE) return 0;

			return 1;
		}

		/**
		 * Return the selected rows from previous submit
		 *
		 * @return array of row_ids of selected rows
		 */
		protected function & get_checked_row_ids () {
			// Collect data
			$rows=array();

			$pre_name= $this->doc_name . "_check_";
			$len= strlen($pre_name);

			foreach($_POST as $key => $value) {
				if(strpos($key, $pre_name)!==false) {
					$rows[]= substr($key, $len);
				}
			}

			return $rows;
		}

		protected function show_select_all() {

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			$pre_name= $this->doc_name . "_check_";
			$action="if(this.checked) { check_all_checkboxes(\"$pre_name\"); } else { uncheck_all_checkboxes(\"$pre_name\");}";
			$ret= "<input type='checkbox' class='action' value='1' name='selec_all_" . $this->doc_name . "' onclick='$action'>";

			return $ret;
		}

		protected function show_return_form() {
			global $MESSAGES;

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			echo $this->create_row_action($MESSAGES["BUTTON_ACCEPT"],"retrieve",-1);
			$this->datawindow_query->show_hidden();
		}

		public function show_hidden() {

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			if($this->hidden_shown) return;
			?>

			<input type='hidden' name='dw_action_<?php echo $this->doc_name; ?>' value=''>
			<input type='hidden' name='row_id_<?php echo $this->doc_name; ?>' value='-1'>
			<input type='hidden' name='start_in_<?php echo $this->doc_name; ?>' value='0'>
			<?php

			// For each field store some hidden information (if it need to do it)
			$this->datawindow_query->show_hidden();

			parent::show_hidden();
		}

		/**
		 * Get the values from $_POST and set them to current form fields.
		 *
		 * - Fill form fields with its values defined from last call (previous form)
		 *
		 */
		protected function recover_values_from_call(& $values = null) {

			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			if($values === null) {
				$values=Array();
				$this->datawindow_query->get_values_from_post($values);
			}

			$this->datawindow_query->set_form_values($values);
		}

		protected function get_cell_color($field, $value) { return ""; }

	}
?>