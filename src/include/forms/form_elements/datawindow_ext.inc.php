<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage forms
 *
 */

include_once SYSHOME . "/include/forms/field_ext.inc.php";
include_once SYSHOME . "/include/forms/form_elements.inc.php";
include_once SYSHOME . "/include/forms/form_elements/datawindow_query.inc.php";
include_once SYSHOME . "/include/forms/form_elements/common_database_elements_ext.inc.php";


/**
 * Defines a new interface to dabatase tables.
 *
 */
class datawindow_ext extends common_database_elements_ext
{

	// Aspect related properties
	public $tabular = true;			// The show mode (tabular or not tabular (sequential))
	public $nav_enabled = true;		// Is enabled the navigation bar (if needed)?
	public $show_no_rows = false;		// If no rows, show headers or a "NO ROWS" message?
	public $show_toolbar = true;		// By default set to true
	public $show_row_number = true;	// On no-tabular view, show or not Row number ("# <num>")
	public $width;					// Datawindow container width

	public $table_title;

	public $logical_delete = false; // If true, will manage logical deletes, requires a field
	// called "deleted".
	// If a row is deleted will set this field to 1.
	public $allow_save_and_continue = false;

	protected $select;				// The select that will return the rows

	protected $col_modifier;
	protected $form_id;

	protected $search_box;

	// Restrictions:
	protected $restriction_search_box;	// The restriction inherited from the search box
	public $export_allowed;



	/**
	 * Creates a new datawindow improved object
	 *
	 * @param datawindow_query $datawindow_query
	 * @return datawindow_ext
	 */
	public function datawindow_ext(&$datawindow_query, $width = 0, $table_title = "")
	{

		global $MESSAGES;

		$aux_name = "datawindow_" . get_new_id();
		parent::common_database_elements_ext($aux_name, $datawindow_query);

		$this->datawindow_query = $datawindow_query;
		$this->datawindow_query->parent_datawindow = &$this;

		$this->width = $width;

		$this->search_box = array();

		$this->table_title = $table_title;

		$this->logical_delete = false;

		$this->custom_query = "";

		foreach ($datawindow_query->fields as $field) {
			if ($field->type == "dummy") continue;
			$field->adopted($this);
		}

		$this->export_allowed = false;
		//$this->add_group_action($MESSAGES["DELETE"],"group_delete");
	}

	public function set_custom_query($query)
	{

		$this->custom_query = $query;
	}

	/**
	 * event
	 *
	 * hidden fields:
	 * - action: To store the default action
	 * - row_id: To store the id of the row clicked
	 * - start_in: For navigation: where to start?
	 *
	 *	Control datawindow flow...
	 *	The control flow pass from states.
	 *
	 *		STATUS 0: action is not set. --> show rows (retrieve)
	 *			Next status can be 1.0 (insert) 2.0 (update) or 3.0 (delete)
	 *
	 *		STATUS 1.0: action == "start_insert" -->
	 *			Come from STATUS 0--> show form for new register
	 *			Next status can be 1.1 (ok) or 0 (cancel)
	 *
	 *		STATUS 1.1: action == "insert_row" -->
	 *			Come from STATUS 1.0 --> check for parameters.
	 *				if parameters are OK --> call the f_insert_row function,
	 *				else --> show again the form and recover the previous values,
	 *			Next status can be 0 (parms OK) or 1.1 (parms not OK)
	 *
	 *		STATUS 2.0: action == "start_update" -->
	 *			Come from STATUS 0 --> show form for new register, and uses the previous form parameter
	 *				"row_id" to recover the values of the row to update.
	 *			Next status can be 2.1 (ok) or 0 (cancel)
	 *
	 *		STATUS 2.1: action == "update_row" -->
	 *			Come from STATUS 2.0 --> check for parameters.
	 *				if parameters are OK --> call the update_row function,
	 *				else --> show again the form and recover the previous values,
	 *			Next status can be 0 (parms OK) or 2.1 (parms not OK)
	 *
	 *		STATUS 3.0: action == "start_delete" -->
	 *			Come from STATUS 0 --> ask for user to confirm the deletion and delete the row.
	 *				"row_id" to recover the values of the row to delete.
	 *			Next status can be 2.1 (ok) or 0 (cancel)
	 *

		public function event($event_type) {
		// Defined on parent class (common_database_elements
		}
	 */

	protected function action_start_insert($row_id)
	{

		// Action disabled on CLI mode
		if (CLI_MODE) return 0;


		if (!$this->datawindow_query->insert_allowed) {
			if (DEBUG) html_showError("Datawindow::ERROR: insert is not allowed!");
			else html_showError("DW[14-1]: Construction error. Contact administrator");
			exit;
		}

		$this->show_insert_form("insert_row");
		return 1;
	}

	protected function action_start_update($row_id)
	{

		// Action disabled on CLI mode
		if (CLI_MODE) return 0;


		if (!$this->datawindow_query->update_allowed) {
			if (DEBUG) html_showError("Datawindow::ERROR: update is not allowed!");
			else html_showError("DW[11-1]: Construction error. Contact administrator");
			exit;
		}

		$this->show_update_form($row_id);
		return 1;
	}

	protected function action_start_delete($row_id)
	{

		// Action disabled on CLI mode
		if (CLI_MODE) return 0;


		if (!$this->datawindow_query->delete_allowed) {
			if (DEBUG) html_showError("Datawindow::ERROR: delete is not allowed!");
			else html_showError("DW[12-1]: Construction error. Contact administrator");
			exit;
		}

		if ($row_id == -1) {
			if (DEBUG) html_showError("Datawindow::ERROR: datawindow::delete_row, row_id not defined.");
			else html_showError("DW[3]: Construction error. Contact administrator");
			exit;
		}

		$values = array();
		if (!$this->datawindow_query->get_values_from_id($row_id, $values)) {
			if (DEBUG) html_showError("Datawindow::ERROR: Error getting the row values from table.");
			else html_showError("DW[4]: Construction error. Contact administrator");
			return 0;
		}

		$this->delete_row($row_id, $values);
		// $this->show_return_form();
		return 0;
	}

	protected function action_insert_row($row_id)
	{

		// Action disabled on CLI mode
		if (CLI_MODE) return 0;


		$values = array();
		$this->datawindow_query->get_values_from_post($values);

		if (!$this->check_parameters($values)) {					// Some parameters not set...
			$this->show_insert_form("insert_row");
			$this->recover_values_from_call($values);
			return 1;
		}

		if ($this->insert_row($values)) {
			// $this->show_return_form();
		} else {
			$this->show_insert_form("insert_row");
			$this->recover_values_from_call($values);
			return 1;
		}

		return 0;
	}

	protected function action_update_row($row_id)
	{

		global $MESSAGES;

		// Action disabled on CLI mode
		if (CLI_MODE) return 0;


		$old_values = array();

		if (!$this->datawindow_query->get_values_from_id($row_id, $old_values)) {
			if (DEBUG) html_showError("Datawindow::" . $MESSAGES["QUERY_ERROR"] . ": " . $this->db->dbms_error());
			else html_showError("DW[6]: Construction error. Contact administrator");
			return 0;
		}

		$new_values = array();
		$this->datawindow_query->get_values_from_post($new_values);

		// TODO: Check this feature (field_ext->hide_on_update).
		// Recover previous values for fields that must not be updated by user.
		// Those fields can be updated by application logic, so first recover
		// previous values.
		foreach ($this->datawindow_query->fields as $field) {
			if ($field->type == "dummy") continue;
			if ($field->hide_on_update) $new_values[$field->name] = $old_values[$field->name];
		}

		if (!$this->check_parameters($new_values)) {
			$this->show_update_form($row_id);
			$this->recover_values_from_call($new_values);
			return 1;
		}

		if (!$this->update_row($row_id, $old_values, $new_values)) {
			$this->show_update_form($row_id);
			$this->recover_values_from_call($new_values);
			return 1;
		}

		if ($this->allow_save_and_continue) {
			$this->show_update_form($row_id);
			$this->recover_values_from_call($new_values);
			return 1;
		}

		return 0;
	}

	protected function action_group_delete($row_ids)
	{

		// Action disabled on CLI mode
		if (CLI_MODE) return 0;


		foreach ($row_ids as $row_id) {
			$this->action_start_delete($row_id);
		}
	}

	protected function action_retrieve()
	{

		// Action disabled on CLI mode
		if (CLI_MODE) return 0;


		return 0;
	}

	protected function action_start_export($row_id)
	{

		return $this->export_data();
	}

	/**
	 * Show the rows of the query in a table.
	 *
	 * @return integer, 0=any error happend.
	 */
	protected function retrieve()
	{

		global $MESSAGES;

		/**
		 * Disable some features on CLI mode
		 */
		if (CLI_MODE) {
			$this->nav_enabled = false;
			$this->export_allowed = false;
			$this->show_no_rows = false;
			$this->show_toolbar = false;
			$this->row_selection_enabled = false;
			$this->datawindow_query->insert_allowed = false;
			$this->datawindow_query->update_allowed = false;
			$this->datawindow_query->delete_allowed = false;
		}

		parent::retrieve();

		$start_in = get_http_post_param("start_in_" . $this->doc_name, 0);

		$stop = false;
		$nav_str = "";

		// Perhaps the inherited class want to put something here! (hidden fields, buttons, etc.)
		if (method_exists($this, "pre_retrieve")) {
			// The first table to show is the table which will store the
?>
			<table class='data_box_previous'>
				<tr class='data_box_previous'>
					<td class='data_box_previous'>
						<?php $stop = !$this->pre_retrieve(); ?>
					</td>
				</tr>
			</table>
		<?php		}

		if ($stop) {
			if (DEBUG) html_showInfo("Info: datawindow::retrieve cancelled by user.");
			return 0;
		}

		$query = $this->datawindow_query->get_query(); // Begin to build the query, first, rebuild querys

		// How many rows will return?
		if (!$res = $this->db->dbms_query($this->datawindow_query->get_count_query())) {
			if (DEBUG) html_showError("Datawindow::" . $MESSAGES["QUERY_ERROR"] . ": " . $this->db->dbms_error() . "<br>Query: $query<br>");
			else html_showError("DW[16]: Construction error. Contact administrator");
			return 0;
		}

		$row = $this->db->dbms_fetch_row($res);
		$numRows = $row[0];

		if ($this->nav_enabled) {
			// The second table will show the navigation bar:
			// Navigation bar can be disabled. By default is enabled.
			// Ok, navigation is allowed, but, is really necessary?

			$nav_str = $this->get_navigationString($start_in, $numRows);

			// Alter the query to don't show more than limit per page
			if ($numRows > $this->max_lines_per_page) {
				$query = $this->db->dbms_limit($query, $this->max_lines_per_page, $start_in);
			}
		}

		// Query is completed. Execute it!
		if (!$res = $this->db->dbms_query($query)) {
			if (DEBUG) html_showError("Datawindow::ERROR: " . $MESSAGES["QUERY_ERROR"] . ": " . $this->db->dbms_error());
			else html_showError("DW[8]: Construction error. Contact administrator");
			return 0;
		}

		$totalNumRows = $numRows;
		$numRows = $this->db->dbms_num_rows($res);

		if (($numRows == 0) and !$this->datawindow_query->insert_allowed and !$this->datawindow_query->update_allowed and !$this->datawindow_query->delete_allowed  and $this->show_no_rows) {
			html_showInfo($MESSAGES["NO_ROWS"]);
			$this->db->dbms_free_result($res);
			return 1;
		}

		if ($numRows != 0) $this->db->dbms_data_seek($res, 0);

		$values = array();

		$form_ref = "document.forms." . $this->form_name;
		$row_id_ref = $form_ref . ".row_id_" . $this->doc_name;
		$action_ref = $form_ref . ".dw_action_" . $this->doc_name;

		$field_list = $this->datawindow_query->get_visible_fields(false, false);

		$width = ($this->width) ? " width='$this->width'" : "";

		if ($this->tabular) {
		?>
			<br>
			<table class="data_box_external" <?php echo $width; ?>>
				<?php
				if ($this->table_title != "") {
				?>
					<tr class="data_box_title">
						<td align="left" class="data_box_title" colspan="3"><?php echo $this->table_title; ?></td>
					</tr>
				<?php
				}

				if ($this->show_toolbar) $this->show_toolbar_header($totalNumRows, $nav_str);
				?>
				<tr>
					<td colspan="3">
						<table class="data_box_rows" cellspacing="0">
							<tr>
								<?php
								if (method_exists($this, "start_show_row")) echo "\n<th class='data_box_rows'></th>";

								// FIELD HEADERS //////////////////////////////////
								foreach ($field_list as $field) {
									if (($field->name == "") or (!$field->visible)) continue;
									echo "<th class='data_box_rows'>" . $field->get_field_header($this) . "</th>";
								}

								// Extended methods
								if ($this->datawindow_query->update_allowed) echo "\n<th class='data_box_rows'>&nbsp;</th>";
								if ($this->datawindow_query->delete_allowed) echo "\n<th class='data_box_rows'>&nbsp;</th>";
								if (method_exists($this, "post_show_row")) echo "\n<th class='data_box_rows'></th>";
								if ($this->row_selection_enabled) echo "\n<th class='data_box_rows'><center>" . $this->show_select_all() . "</center></th>";
								?>
							</tr>
							<?php
							// FIELD VALUES ///////////////////////////////////
							$EvenRow = false;
							while ($row = $this->db->dbms_fetch_array($res)) {

								unset($values);
								$values = array();

								$this->datawindow_query->get_values_from_row($row, $values);

								// PRE_SHOW_ROW ///////////////////////////////
								$can_update = true;
								$can_delete = true;
								if (!$this->pre_show_row($values, $can_update, $can_delete)) {
									continue;
								}

								// GET_ROW_COLOR //////////////////////////////
								$row_color = $this->get_row_color($EvenRow, $values);
								if ($row_color != "") {
									$row_color = "style='background-color: " . $row_color . ";'";
								}

								if ($EvenRow) { ?>
									<tr class='data_box_rows_tabular_even'>
									<?php							} else { ?>
									<tr class='data_box_rows_tabular_odd'>
									<?php							}

								// SHOW_ROW ///////////////////////////////////

								// The user want to add something at the start of the row?
								if (method_exists($this, "start_show_row")) {
									?>
										<td class='data_box_cell'>
											<center><?php $this->start_show_row($values); ?></center>
										</td>
									<?php
								}

								$id = $this->show_row($values);

								if ($this->datawindow_query->update_allowed) {
									?> <td class='data_box_cell'>
											<center>
												<?php if ($can_update and ($id != "")) $this->show_update_button($id);
												?> </center>
										</td>
									<?php
								}

								if ($this->datawindow_query->delete_allowed) {
									?> <td class='data_box_cell'>
											<center>
												<?php if ($can_delete and ($id != ""))	$this->show_delete_button($id);
												?></center>
										</td>
									<?php							}

								// The user want to add something at the end of the row?
								if (method_exists($this, "post_show_row")) {
									?>
										<td class='data_box_cell'>
											<center><?php $this->post_show_row($values); ?></center>
										</td>
									<?php
								}

								$this->show_select_box($id);
									?>
									</tr>
								<?php
								$EvenRow = !$EvenRow;
							}

								?>
						</table>
					</td>
				</tr>
				<?php
				$this->show_toolbar_footer($nav_str);
				?>
			</table>
		<?php
		} else {

			//////////////////////////////////////////////////////////////////
			// Not tabular
			////////////////////////////////////////////////////////////////
		?>
			<br>
			<center>
				<table class="data_box_external" <?php echo $width; ?>>
					<?php
					if ($this->table_title != "") {
					?>
						<tr class="data_box_title">
							<td align="left" class="data_box_title" colspan="3"><?php echo $this->table_title; ?></td>
						</tr>
					<?php
					}

					if ($this->show_toolbar) $this->show_toolbar_header($totalNumRows, $nav_str);
					?>
					<tr>
						<td colspan="3">
							<table class="data_box_rows" cellspacing="0">
								<?php

								$num_fields = count($field_list);

								$cont = 1;

								while ($row = $this->db->dbms_fetch_array($res)) {

									$this->datawindow_query->get_values_from_row($row, $values);

									$can_update = true;
									$can_delete = true;

									if (!$this->pre_show_row($values, $can_update, $can_delete)) continue;

									$to_head = $this->show_row_number ? "<b># " . $cont++ . "</b>" : "";
									$id = "";
									$value = 0;
									$to_print = "";


									unset($values);
									$values = array();
									$this->datawindow_query->get_values_from_row($row, $values);
									$to_print .= $this->get_show_row_no_tabular($values, $id);

									$toToolbar = "";

									if ($can_update and $this->datawindow_query->update_allowed and ($id != "")) $toToolbar .= $this->get_update_button($id);
									if ($can_delete and $this->datawindow_query->delete_allowed and ($id != "")) $toToolbar .= $this->get_delete_button($id);

									if ($to_print != "") {
										echo "<tr class='data_box_rows_list_header'>\n<td class='data_box_rows_list_header'>$to_head</td>\n<td class='data_box_rows_list_header' align='right'>$toToolbar";

										if (method_exists($this, "post_show_row")) {
											$this->post_show_row($values);
										}
										echo "</td></tr>\n";

										echo $to_print;
									}
								}
								?>
							</table>
						</td>
					</tr>
					<?php
					$this->show_toolbar_footer($nav_str);
					?>
				</table>
			</center>
		<?php		}
		$this->db->dbms_free_result($res);

		if (method_exists($this, "post_retrieve")) {
			// The first table to show is the table which will store the
		?>
			<table class='data_box_post'>
				<tr class='data_box_post'>
					<td class='data_box_post'>
						<?php $stop = !$this->pre_postretrieve();
						?>
					</td>
				</tr>
			</table>
		<?php		}

		return 1;
	}

	protected function export_data()
	{
		global $MESSAGES;

		$rows = array();

		$query = $this->datawindow_query->get_query();
		$res = $this->db->dbms_query($query);

		$fields = $this->datawindow_query->get_visible_fields(false, false);
		$num_fields = count($fields);

		$i = 1;
		while ($row = $this->db->dbms_fetch_array($res)) {

			unset($values);
			unset($real_values);

			$values = array();
			$real_values = array();

			$this->datawindow_query->get_values_from_row($row, $values);

			// Now, sequencial access to each row, changing value for field itself.
			foreach ($fields as $field) {

				if (!$field->will_be_displayed(false, false)) continue;

				if (isset($values[$field->name])) {
					$curr_val = $values[$field->name];
					if (strpos(get_class($field), "master") !== false) {
						$value = $field->get_parent_value($curr_val);
					} else {
						$value = $field->get_value($curr_val);
					}
				} else {
					$value = "";
				}

				$real_values[$field->alias] = $value;
			}

			$rows[] = $real_values;
		}

		include_once INC_DIR . "forms/form_basic.inc.php";

		$rnd = rand(1000, 2000);
		$base_name = $this->doc_name . "_" . $rnd . ".csv";
		$file = "/tmp/$base_name";
		//exportDataCSV($this->db, $res, $file);
		exportArrayCSV($rows, $file);
		$url = HOME . "/tools/download.php?file=$base_name";
		?>
		<script>
			openWin("<?php echo $url; ?>")
		</script>
		<script>
			parent.close();
		</script>
	<?php

		$this->db->dbms_free_result($res);

		html_showSuccess($MESSAGES["MGM_EXPORTED"]);
		return 0;
	}

	/**
	 * PUBLIC
	 *
	 * Show a create form for this table definition
	 *
	 */
	protected function show_update_form($row_id)
	{

		global $MESSAGES;

		// TODO: For password fields:
		// Password recovered from database should be encrypted (using PASSWORD SQL fuction per example).
		// If its value is restored next update will execute the PASSWORD() function using the encrypted password,
		// and result is not which we want...
		// The process must know when to update or not password. Most easy way is that user use the
		// f_own_update or f_pre_update to do it by it self.

		// row identified must be defined
		//			$row_id= get_http_post_param("row_id_" . $this->doc_name, false);

		if ($row_id === false) {
			if (DEBUG) html_showError("Datawindow::ERROR: Identifier not defined");
			else html_showError("DW[9]: Construction error. Contact administrator");
			return 0;
		}

		$query = $this->datawindow_query->get_row_query($row_id);
		if (!$res = $this->db->dbms_query($query)) {
			if (DEBUG) html_showError("Datawindow::ERROR: " . $MESSAGES["QUERY_ERROR"] . ": " . $this->db->dbms_error());
			else html_showError("DW[10]: Construction error. Contact administrator");
			return 0;
		}

		$numRows = $this->db->dbms_num_rows($res);

		if (!$numRows) {
			html_showInfo($MESSAGES["NO_ROWS_FOR_ID"]);
			$this->db->dbms_free_result($res);
			return 0;
		}

		// If there is any result to be shown... show it!
		$this->show_insert_form("update_row");
		$this->datawindow_query->recover_values_from_query($res);

		// Shadow POST DATA protection
		$shadow_id = $this->shadow->get_shadow_id($row_id);
		$this->shadow->add_shadowed_id($row_id, $shadow_id, "update_row");
		html_set_field_value($this->form_name, "row_id_" . $this->doc_name, $shadow_id, false);

		$this->db->dbms_data_seek($res, 0);
		$row = $this->db->dbms_fetch_array($res);
		$this->add_custom_update_form_values($row);

		$this->db->dbms_free_result($res);
		return 1;
	}

	protected function add_custom_update_form_values($res)
	{
	}

	/**
	 * PRIVATE
	 *
	 *  - For every field, checks for its status.
	 *    if field is required and it has value, if value has the same type than field has, ...
	 *  - Finally calls the f_own_check if defined in the inherited class.
	 *
	 * @return integer, 1=Ok, 0=Error
	 */
	protected function check_parameters($values)
	{

		global $MESSAGES;

		$dw_action = get_http_post_param("dw_action_" . $this->doc_name);
		$for_insert = ($dw_action == "insert_row");

		if (!$this->datawindow_query->check_fields_values($values, $for_insert, $this->db)) return 0;

		if (method_exists($this, "my_check")) return $this->my_check($values);
		return 1;
	}

	/**
	 * PROTECTED
	 *
	 * @param array $values, associative array with the value of each row field.
	 */
	protected function insert_row($values)
	{

		global $MESSAGES;

		if (!$this->datawindow_query->insert_allowed or ($this->datawindow_query->table_id === null)) {
			if (DEBUG) {
				html_showError("Datawindow::ERROR: insert is not allowed!");
			} else {
				html_showError("DW[14]: Construction error. Contact administrator");
			}
			exit;
		}

		$this->db->dbms_begin_transaction();

		// For each field not updatable, clean its value. The value must be set on the
		// pre_insert method.
		foreach ($this->datawindow_query->fields as $field) {
			if (!$field->updatable) $values[$field->name] = null;
		}

		if (!$this->pre_insert($values)) {
			$this->db->dbms_rollback();
			return 0;
		}

		// Call insert method from datawindow query object
		if (!$this->datawindow_query->insert_row($values)) {
			html_showError($MESSAGES["MGM_ERROR_CREATING"] . ": " . $this->db->dbms_error());
			$this->db->dbms_rollback();
			return 0;
		}

		// Now, call the field_insert method of each field.
		$ok = true;
		foreach ($this->datawindow_query->fields as $field) {

			if ($field->type == "dummy") continue;

			if (isset($values[$field->name])) {
				$ok = $field->field_insert($values[$field->name]);
			}
			if ($ok === false) break;
		}

		if ($ok === false) {
			html_showError($MESSAGES["MGM_ERROR_CREATING"] . ": " . $this->db->dbms_error());
			$this->db->dbms_rollback();
			return 0;
		}

		if (!$this->post_insert($values)) {
			$this->db->dbms_rollback();
			return 0;
		}

		$this->db->dbms_commit();

		html_showSuccess($MESSAGES["MGM_CREATED"]);
		return 1;
	}

	/**
	 * PROTECTED
	 * - update the row identified by row_id post var.
	 * - The rest of fields needed must be passed also as post vars (defined in previus form)
	 */
	protected function update_row($row_id, $old_values, $new_values)
	{

		global $MESSAGES;

		if (!$this->datawindow_query->update_allowed) {
			if (DEBUG) {
				html_showError("Datawindow::ERROR: update is not allowed!");
			} else {
				html_showError("DW[11]: Construction error. Contact administrator");
			}
			exit;
		}

		$this->db->dbms_begin_transaction();

		foreach ($this->datawindow_query->fields as $field) {

			if ($field->updatable or ($field->name == $this->datawindow_query->fields[$this->datawindow_query->field_id]->name)) continue;
			if (key_exists($field->name, $new_values)) $new_values[$field->name] = null;
		}


		if (!$this->pre_update($row_id, $old_values, $new_values)) {
			$this->db->dbms_rollback();
			return 0;
		}

		if (!$this->datawindow_query->update_row($row_id, $old_values, $new_values)) {
			html_showError($MESSAGES["MGM_ERROR_MODIFYING"] . ": " . $this->db->dbms_error());
			$this->db->dbms_rollback();
			return 0;
		}

		$ok = true;

		foreach ($this->datawindow_query->fields as $field) {

			if ($field->type == "dummy") continue;

			$old_val = isset($old_values[$field->name]) ? isset($old_values[$field->name]) : null;
			$new_val = isset($new_values[$field->name]) ? isset($new_values[$field->name]) : null;

			$ok = $field->field_update($old_val, $new_val);
			if ($ok === false) break;
		}

		if ($ok === false) {
			html_showError($MESSAGES["MGM_ERROR_MODIFYING"] . ": " . $this->db->dbms_error());
			$this->db->dbms_rollback();
			return 0;
		}

		if (!$this->post_update($row_id, $old_values, $new_values)) {
			$this->db->dbms_rollback();
			return 0;
		}

		$this->db->dbms_commit();

		$this->update_commited($row_id, $old_values, $new_values);

		html_showSuccess($MESSAGES["MGM_MODIFIED"]);

		return 1;
	}

	/**
	 * PROTECTED
	 * - Deletes the row identified by row_id post var.
	 */
	public function delete_row($row_id, $values)
	{

		global $MESSAGES;

		if (!$this->datawindow_query->delete_allowed) {
			if (DEBUG) {
				html_showError("Datawindow::ERROR: delete is not allowed!");
			} else {
				html_showError("DW[12]: Construction error. Contact administrator");
			}
			exit;
		}


		$this->db->dbms_begin_transaction();

		if (!$this->pre_delete($row_id, $values)) {
			$this->db->dbms_rollback();
			return 0;
		}

		if (!$this->datawindow_query->delete_row($row_id, $values)) {
			html_showError($MESSAGES["MGM_ERROR_DELETING"] . ": " . $this->db->dbms_error());
			$this->db->dbms_rollback();
			return 0;
		}

		if (!$this->post_delete($row_id, $values)) {
			$this->db->dbms_rollback();
			return 0;
		}

		$ok = true;
		foreach ($this->datawindow_query->fields as $field) {
			if ($field->type == "dummy") continue;

			$ok = $field->field_delete($values[$field->name]);
			if ($ok === false) break;
		}

		if ($ok === false) {
			html_showError($MESSAGES["MGM_ERROR_DELETING"] . ": " . $this->db->dbms_error());
			$this->db->dbms_rollback();
			return 0;
		}

		html_showSuccess($MESSAGES["MGM_DELETED"]);
		$this->db->dbms_commit();
		return 1;
	}

	public function add_search_box(&$search_box)
	{

		$this->search_box[] = $search_box;
		$this->restriction_search_box = $this->db->dbms_query_append($this->restriction_search_box, $search_box->get_restriction());

		$this->add_element($search_box);
	}

	public function get_restriction()
	{

		$ret = "";

		// And adding the restrictions from user:
		$ret = $this->db->dbms_query_append($ret, $this->restriction_search_box);

		if ($this->logical_delete) {
			$ret = $this->db->dbms_query_append($ret, "deleted=0");
		}

		return $ret;
	}

	private function save_datawindow_action($row_id, $action)
	{

		$this->get_unique_doc_id();

		$row_id_ref = $this->get_unique_var("row_id");
		$action_ref = $this->get_unique_var("action");

		$_SESSION[$row_id_ref] = $row_id;
		$_SESSION[$action_ref] = $action;
	}

	private function restore_datawindow_action(&$row_id, &$action)
	{

		$row_id_ref = $this->get_unique_var("row_id");
		$action_ref = $this->get_unique_var("action");

		$row_id = $_SESSION[$row_id_ref];
		$action = $_SESSION[$action_ref];
	}

	private function get_unique_var($var_name)
	{

		return APP_NAME . $this->doc_ref . "." . $var_name;
	}

	protected function show_toolbar_header($numRows, $nav_str)
	{
		global $MESSAGES;

	?>
		<tr class="data_box_toolbar">
			<td align="left" class="data_box_toolbar_tools">
				<?php
				if ($this->show_global_actions_first) {

					$this->show_global_actions();
					echo $this->create_group_actions_select();
					$this->show_reload_button();
					echo "&nbsp;";
					if ($this->datawindow_query->insert_allowed) $this->show_insert_button();
					echo "&nbsp;";
					if ($this->export_allowed) {
						$this->show_export_button();
					}
				} else {

					$this->show_reload_button();
					echo "&nbsp;";
					if ($this->datawindow_query->insert_allowed) $this->show_insert_button();
					echo "&nbsp;";
					if ($this->export_allowed) {
						$this->show_export_button();
					}
					$this->show_global_actions();
					echo $this->create_group_actions_select();
				}
				?>
			</td>
			<td class="data_box_toolbar_num_rows" colspan="2">
				<?php
				echo sprintf($MESSAGES["NUM_ROWS"], $numRows);
				?>
			</td>
		</tr>
		<tr class="data_box_toolbar">
			<td class="data_box_toolbar_nav" colspan="3">
				&nbsp;&nbsp;<?php echo $nav_str; ?>
			</td>
		</tr>
	<?php
	}

	protected function show_toolbar_footer($nav_str)
	{
		if ($nav_str == "") return;
	?>
		<tr class="data_box_toolbar">
			<td align="right" class="data_box_toolbar_nav_footer" colspan="3">
				&nbsp;&nbsp;<?php echo $nav_str; ?>
			</td>
		</tr>
<?php
	}
}
