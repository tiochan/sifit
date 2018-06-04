<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage forms
 *
 */

	include_once SYSHOME . "/include/forms/field.inc.php";
	include_once SYSHOME . "/include/forms/form_elements.inc.php";
	include_once SYSHOME . "/include/forms/form_elements/common_database_elements.inc.php";


	class datawindow extends common_database_elements {
		// PUBLIC

		public $custom_query;

		public $insert_allowed;
		public $update_allowed;
		public $delete_allowed;
		public $export_allowed;

		public $tabular;					// The show mode (tabular or not tabular (sequential))

		public $special_select;			// Perhaps the usual query is not sufficient
		public $nav_enabled;				// Is enabled the navigation bar (if needed)?

		public $logical_delete;
		/**
		 * local pointer to dbms
		 *
		 * @var dbms
		 */
		protected $table_name;
		// protected $identifier;		// The name of the field that identifies one row, declared on parent
		protected $select;				// The select that will return the rows
		protected $select_count;		// The select that will return the row count
		protected $custom_select_count;	// The select that will return the row count (own)
		protected $restriction;			// Restrictions on the query (defined by user)
		protected $query_adds;			// More things to add to the query

		protected $col_modifier;
		protected $form_id;

		protected $search_box;

		// Restrictions:
		protected $restriction_param;	// The restriction passed by parameter, on create moment
		protected $restriction_fields;// The restriction inherited from fields
		protected $restriction_search_box;	// The restriction inherited from the search box


		/**
		 * datawindow class
		 *
		 * @param string $table_name
		 * @param field_array $fields
		 * @param integer $identifier_field , must indicate the position of the array that contains the identifier field.
		 * @param string $restriction , To add to the WHERE clause
		 * @param string $query_adds , To add to the end of the query (order by clauses, etc.)
		 * @param bool $insert_allowed , Is allowed the insert? (will display or not the insert button, and more security issues).
		 * @param bool $update_allowed , same as insert
		 * @param bool $delete_allowed , same as insert
		 * @param dbms &$optional_db , Optionally you can use another dbms connection rather than global_db.
		 * @return form
		 */
		public function datawindow($table_name,
									$fields,
									$identifier_field,
									$restriction,
									$query_adds,
									$insert_allowed,
									$update_allowed,
									$delete_allowed,
									&$optional_db=null) {
			$aux_name= "datawindow_" . $table_name . "_" . get_new_id();
			parent::common_database_elements($aux_name, $fields, $insert_allowed, $update_allowed, $delete_allowed, $optional_db);

			$this->table_name=$table_name;
			$this->export_allowed= false;

			$this->search_box= array();

			$this->tabular=true;
			$this->nav_enabled=true;

			$this->logical_delete=false;

			$this->custom_query="";
			$this->query_adds= trim($query_adds);
			$this->restriction_param= trim($restriction);

			// And the restrictions from each field:
			$this->restriction_fields="";
			for($i=0; $i<count($this->fields); $i++) {
				$this->restriction_fields= $this->db->dbms_query_append($this->restriction_fields, $this->fields[$i]->get_restriction());
			}

			$this->select= $this->build_select();
			$this->restriction= $this->get_restriction();
			$this->select_count= $this->build_select_count();
			$this->custom_select_count="";

			/*
				TODO; use session to store/restore form_id to improve the security:
				- use get_http_post_param("row_id")  ...
				- use form_id to register at session $_SESSION($this->form_id);
				- recover at return and check it.
			*/

			// Check for identifier... if defined:
			if(!isset($this->fields[$identifier_field]) and ($this->update_allowed or $this->delete_allowed)) {
				if(DEBUG) {
					html_showError("Datawindow::ERROR: datawindow::datawindow Must indicate a identifier field.");
				} else {
					html_showError("DW[1]: Construction error. Contact administrator");
				}
				exit;
			}

			$this->identifier=$identifier_field;
		}

		public function set_custom_query($query) {

			$this->custom_query= $query;
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

		protected function action_start_insert($row_id) {

			if(!$this->insert_allowed) {
				if(DEBUG) {
					html_showError("Datawindow::ERROR: insert is not allowed!");
				} else {
					html_showError("DW[14-1]: Construction error. Contact administrator");
				}
				exit;
			}

			$this->show_insert_form("insert_row");
			return 1;
		}

		protected function action_start_export($row_id) {

			return $this->export_data();
		}

		protected function action_start_update($row_id) {

			if(!$this->update_allowed) {
				if(DEBUG) {
					html_showError("Datawindow::ERROR: update is not allowed!");
				} else {
					html_showError("DW[11-1]: Construction error. Contact administrator");
				}
				exit;
			}

			$this->show_update_form($row_id);
			return 1;
		}

		protected function action_start_delete($row_id) {

			if(!$this->delete_allowed) {
				if(DEBUG) {
					html_showError("Datawindow::ERROR: delete is not allowed!");
				} else {
					html_showError("DW[12-1]: Construction error. Contact administrator");
				}
				exit;
			}

			if($row_id == -1) {
				if(DEBUG) {
					html_showError("Datawindow::ERROR: datawindow::delete_row, row_id not defined.");
				} else {
					html_showError("DW[3]: Construction error. Contact administrator");
				}
				exit;
			}

			$values=Array();
			if(!$this->get_values_from_id($row_id, $values, true)) {
				if(DEBUG) {
					html_showError("Datawindow::ERROR: Error getting the row values from table.");
				} else {
					html_showError("DW[4]: Construction error. Contact administrator");
				}
				return 0;
			}

			return $this->delete_row($row_id, $values);
			// $this->show_return_form();
			// return 1;
		}

		protected function action_insert_row($row_id) {

			if(!$this->check_parameters()) {					// Some parameters not set...
				$this->show_insert_form("insert_row");
				$this->recover_values_from_call();
				return 1;
			}

			$values= $this->get_values();

			if($this->insert_row($values)) {
				// $this->show_return_form();
			} else {
				$this->show_insert_form("insert_row");
				$this->recover_values_from_call();
				return 1;
			}

			return 0;
		}

		protected function action_update_row($row_id) {

			if(!$this->check_parameters()) {
				$this->show_update_form($row_id);
				$this->recover_values_from_call();
				return 1;
			}

			$old_values=array();

			$query= $this->get_row_query($row_id);
			if(!$res=$this->db->dbms_query($query)) {
				if(DEBUG) {
					html_showError("Datawindow::" . $MESSAGES["QUERY_ERROR"] . ": " . $this->db->dbms_error());
				} else {
					html_showError("DW[6]: Construction error. Contact administrator");
				}
				return 0;
			}

			$this->get_values_from_query($res,$old_values,true);

			$numRows= $this->db->dbms_num_rows($res);

			if(!$numRows) {
				html_showInfo($MESSAGES["NO_ROWS_FOR_ID"]);
				$this->db->dbms_free_result($res);
				return 0;
			}

			$new_values= $this->get_values();

			if($this->update_row($row_id, $old_values, $new_values)) {
				// $this->show_return_form();
			} else {
				$this->show_update_form($row_id);
				$this->recover_values_from_call();
				return 1;
			}

			return 0;
		}

		protected function action_retrieve() {

			return 0;
		}

		protected function export_data() {

			$rows= array();

			$query= $this->build_query();
			$res=$this->db->dbms_query($query);

			$num_fields= count($this->fields);

			// First, prepare headers
/*			$rows[0]= array();
			for($i=0; $i < $num_fields; $i++) {
				if(($this->fields[$i]->name=="") or (!$this->fields[$i]->visible)) continue;
				$rows[0][]= $this->fields[$i]->alias;
			}
*/
			$i=1;
			while($row= $this->db->dbms_fetch_array($res)) {

				unset($values);
				unset($real_values);

				$values= array();
				$real_values= array();

				$this->get_values_from_row($row, $values);

				// Now, sequencial access to each row, changing value for field itself.
				foreach($this->fields as $field) {

					if(!$field->will_be_displayed(false)) continue;

					if(isset($values[$field->name])) {
						$curr_val= $values[$field->name];

						if(strpos(get_class($field), "master")!== false) {
							$value= $field->get_parent_value($curr_val);
						} else {
							$value= $field->get_value($curr_val);
						}

					} else {
						$value="";
					}

					$real_values[$field->alias]= $value;
				}

				$rows[]= $real_values;
			}


			include_once INC_DIR . "forms/form_basic.inc.php";

			$base_name= $this->doc_name . ".csv";
			$file= "/tmp/$base_name";
			//exportDataCSV($this->db, $res, $file);
			exportArrayCSV($rows, $file);
			$url= HOME . "/tools/download.php?file=$base_name";
			?>
				<script>openWin("<?php echo $url; ?>")</script>
				<script>parent.close();</script>
			<?php

			$this->db->dbms_free_result($res);

			return 0;
		}

		/**
		 * Show the rows of the query in a table.
		 *
		 * @return integer, 0=any error happend.
		 */
		protected function retrieve() {

			global $MESSAGES;

			parent::retrieve();


			$start_in= get_http_post_param("start_in_" . $this->doc_name, 0);
			if($start_in < 0) $start_in=0;

			$stop=false;

			// Perhaps the inherited class want to put something here! (hidden fields, buttons, etc.)
			if(method_exists($this,"pre_retrieve")) {
				// The first table to show is the table which will store the
?>
				<table class='data_box_previous'>
					<tr class='data_box_previous'><td class='data_box_previous'>
							<?php $stop= !$this->pre_retrieve(); ?>
					</td></tr>
				</table>
<?php
			}

			if($stop) {
				html_showInfo("Info: datawindow::retrieve cancelled by user.");
				return 0;
			}

			// Begin to build the query, first, rebuild querys
			$query= $this->build_query();

			// The second table will show the navigation bar:
			// Navigation bar can be disabled. By default is enabled.
			if($this->nav_enabled) {

				// How many rows will return?
				$res=$this->db->dbms_query($this->select_count);
				$row=$this->db->dbms_fetch_row($res);
				$numRows=$row[0];

				// Ok, navigation is allowed, but, is really necessary?

				$nav_str= $this->get_navigationString($start_in, $numRows);

				// Alter the query to don't show more than limit per page
				if($numRows > $this->max_lines_per_page) {
					$query= $this->db->dbms_limit($query, $this->max_lines_per_page, $start_in);
				}
			} else {
				$nav_str="";
			}

			// Query is completed. Execute it!
			$res=$this->db->dbms_query($query);

			if(!$res) {
				if(DEBUG) {
					html_showError("Datawindow::ERROR: " . $MESSAGES["QUERY_ERROR"] . ": " . $this->db->dbms_error());
				} else {
					html_showError("DW[8]: Construction error. Contact administrator");
				}

				return 0;
			}

			$totalNumRows= $numRows;
			$numRows= $this->db->dbms_num_rows($res);

			if(($numRows==0) and !$this->insert_allowed and !$this->update_allowed and !$this->delete_allowed) {
				html_showInfo($MESSAGES["NO_ROWS"]);
				$this->db->dbms_free_result($res);
				return 1;
			}

			if($numRows!=0) {
				$this->db->dbms_data_seek($res, 0);
			}

			//$num_fields= $this->db->dbms_num_fields($res);
			$num_fields= count($this->fields);
			$values= Array();

			$form_ref= "document.forms." . $this->form_name;
			$row_id_ref= $form_ref . ".row_id_" . $this->doc_name;
			$action_ref= $form_ref . ".dw_action_" . $this->doc_name;

			if($this->tabular) {
?>
				<br>
				<table class="data_box_external">
<?php
					$this->show_toolbar_header($totalNumRows, $nav_str);
?>
					<tr><td colspan="3">
						<table class="data_box_rows" cellspacing="0">
							<tr>
<?php
								// Field headers
								for($i=0; $i < $num_fields; $i++) {
									if(($this->fields[$i]->name=="") or (!$this->fields[$i]->visible)) continue;
									echo "<th class='data_box_rows'>" . $this->fields[$i]->alias . "</th>";
								}

								// Extended methods
								if($this->update_allowed) echo "\n<th class='data_box_rows'>&nbsp;</th>";
								if($this->delete_allowed) echo "\n<th class='data_box_rows'>&nbsp;</th>";
								if(method_exists($this, "post_show_row")) echo "\n<th class='data_box_rows'></th>";
?>
							</tr>
<?php

							if(method_exists($this, "own_show_rows")) {
								$this->own_show_rows($res);
							} else {

								$filaPar= false;
								while($row= $this->db->dbms_fetch_array($res)) {

									unset($values);
									$values= array();
									$this->get_values_from_row($row, $values);

									// What does the user to say for this row?
									$can_update=true;
									$can_delete=true;
									if(!$this->pre_show_row($values, $can_update, $can_delete)) {
										continue;
									}

									$row_color= $this->get_row_color($filaPar, $values);
									if($row_color!="") { $row_color="style='background-color: " . $row_color . ";'"; }

									if($filaPar) { ?>
									<tr class='data_box_rows_tabular_even'>
	<?php								} else { ?>
									<tr class='data_box_rows_tabular_odd'>
	<?php								}

									$id=""; $value=0;
									// Field values
									for($i=0; $i < $num_fields; $i++) {

										if($this->fields[$i]->name=="") continue;

										if(isset($values[$this->fields[$i]->name])) {
											$curr_val= $values[$this->fields[$i]->name];
											$value= $this->fields[$i]->get_value($curr_val);
										} else {
											$value="";
										}

										if($this->fields[$i]->visible) {
											$cell_color= $this->get_cell_color($this->fields[$i], $value);
											$cell_color= $cell_color != "" ? "bgcolor=$cell_color" : "";
	?>
											<td <?php echo $cell_color; ?> class='data_box_cell' <?php echo $this->fields[$i]->col_modifier; ?>><?php echo $value; ?></td>
	<?php
										}
										if($this->identifier == $i) {
											$id=$value;
										}
									}

									if($this->update_allowed) {
										echo "<td class='data_box_cell'>";
										if($can_update and ($id != "")) echo "<center>" . $this->show_update_button($id) . "</center>";
										echo "</td>";
									}

									if($this->delete_allowed) {
										echo "<td class='data_box_cell'>";
										if($can_delete and ($id != "")) echo "<center>" . $this->show_delete_button($id) . "</center>";
										echo "</td>";
									}


									// The user want to add something at the end of the row?
									if(method_exists($this, "post_show_row")) {
	?>
										<td class='data_box_cell'><center><?php $this->post_show_row($values); ?></center></td>
	<?php
									}

	?>
									</tr>
	<?php

									$filaPar= !$filaPar;
								}
							}

?>
						</table>
					</td></tr>
<?php
				$this->show_toolbar_footer($nav_str);
?>
				</table>
<?php
			} else {		// Not tabular
?>
				<br>
				<center>
				<table class="data_box_external">
<?php
				$this->show_toolbar_header($totalNumRows, $nav_str);
?>

					<tr><td colspan="3">
						<table class="data_box_rows" cellspacing="0">
<?php

				if(method_exists($this, "own_show_rows")) {
					$this->own_show_rows($res);
				} else {

					$cont=1;
					while($row= $this->db->dbms_fetch_array($res)) {

						$this->get_values_from_row($row, $values);

						$can_update=true;
						$can_delete=true;

						if(!$this->pre_show_row($values, $can_update, $can_delete)) continue;

						$toHead="#" . $cont++;
						$id=""; $value=0;
						$toPrint="";

						for($i=0, $field_counter=0; $i < $num_fields; $i++) {
							if($this->fields[$i]->name=="") continue;

							$value= $this->fields[$i]->get_value($row[$field_counter++]);

							if($this->fields[$i]->visible) {
								$alias=$this->fields[$i]->alias;
								$toPrint.="<tr class='data_box_rows_list'><td class='data_box_cell_alias'>$alias</td><td class='data_box_cell_value'>$value</td></tr>";
							}
						}

						$toToolbar="";

						$id= key_exists($this->identifier, $row) ? $row[$this->identifier] : "";

						if($can_update and $this->update_allowed and ($id != "")) {
							$toToolbar.=$this->get_update_button($id);
						}
						if($can_delete and $this->delete_allowed and ($id != "")) {
							$toToolbar.= $this->get_delete_button($id);
						}
						if($toPrint!="") {
							echo "<tr class='data_box_rows_list_header'>\n<td class='data_box_rows_list_header'>$toHead</td>\n<td class='data_box_notab_header' align='right'>$toToolbar</td></tr>\n";
							echo $toPrint;
						}

						if(method_exists($this, "post_show_row")) {
							echo "<tr class='data_box_notab_header'><td class='data_box_notab_header' colspan='2'>";
							$this->post_show_row($values);
							echo "</td></tr>";
						}
					}
				}
?>
						</table>
					</td></tr>
<?php
			$this->show_toolbar_footer($nav_str);
?>
				</table>
				</center>
<?php
			}
			$this->db->dbms_free_result($res);

			if(method_exists($this,"post_retrieve")) {
				// The first table to show is the table which will store the
?>
				<table class='data_box_post'>
					<tr class='data_box_post'><td class='data_box_post'>
<?php						$stop= !$this->pre_postretrieve();
?>
					</td></tr>
				</table>
<?php			}

			return 1;
		}

		/**
		 * PUBLIC
		 *
		 * Show a create form for this table definition
		 *
		 */
		protected function show_update_form($row_id) {

			global $MESSAGES;

			// TODO: For password fields:
			// Password recovered from database should be encrypted (using PASSWORD SQL fuction per example).
			// If its value is restored next update will execute the PASSWORD() function using the encrypted password,
			// and result is not which we want...
			// The process must know when to update or not password. Most easy way is that user use the
			// f_own_update or f_pre_update to do it by it self.

			// row identified must be defined
//			$row_id= get_http_post_param("row_id_" . $this->doc_name, false);

			if($row_id === false) {
				if(DEBUG) html_showError("Datawindow::ERROR: Identifier not defined");
				else html_showError("DW[9]: Construction error. Contact administrator");
				return 0;
			}

			$query= $this->get_row_query($row_id);
			if(!$res=$this->db->dbms_query($query)) {
				if(DEBUG) {
					html_showError("Datawindow::ERROR: " . $MESSAGES["QUERY_ERROR"] . ": " . $this->db->dbms_error());
				} else {
					html_showError("DW[10]: Construction error. Contact administrator");
				}
				return 0;
			}

			$numRows= $this->db->dbms_num_rows($res);

			if(!$numRows) {
				html_showInfo($MESSAGES["NO_ROWS_FOR_ID"]);
				$this->db->dbms_free_result($res);
				return 0;
			}

			// If there is any result to be shown... show it!
			$this->show_insert_form("update_row");
			$this->recover_values_from_query($res);

			// Shadow POST DATA protection
			$shadow_id= $this->shadow->get_shadow_id($row_id);
			$this->shadow->add_shadowed_id($row_id, $shadow_id, "update_row");
			html_set_field_value($this->form_name,"row_id_" . $this->doc_name, $shadow_id, false);

			$this->db->dbms_free_result($res);
			return 1;
		}

		/**
		 * PROTECTED
		 *
		 * Return the query that will return the row using a row_id
		 *
		 * @param unknown_type $row_id
		 * @return unknown
		 */
		protected function get_row_query($row_id) {
			$query=$this->select;

			if(strpos($query, "where")) {
				$query.= " and ";
			} else {
				$query.= " where ";
			}
			$query.= $this->fields[$this->identifier]->name . "=" . $this->fields[$this->identifier]->get_field_ref() . $row_id . $this->fields[$this->identifier]->get_field_ref();
			return $query;
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
		protected function check_parameters() {

			global $MESSAGES;

			for($i=0; $i<count($this->fields); $i++) {

				if($this->fields[$i]->type=="dummy") continue;

				$tmp_field= $this->fields[$i];
				$tmp_name= $this->fields[$i]->name;
				$tmp_alias=$this->fields[$i]->alias;

				$value= $tmp_field->get_value_from_post();

				// TODO: Check for unique fields when action='update_row'
				$dw_action= get_http_post_param("dw_action_" . $this->doc_name);
				if(($dw_action !== false) and ($dw_action == "insert_row")) {
					if($tmp_field->is_unique and ($value !== false)) {
						//$query= $this->select . " where " . $tmp_field->name . "=" . $tmp_field->get_field_ref() . $value . $tmp_field->get_field_ref();
						$query= "select count(*) from " . $this->table_name . " where " . $tmp_field->name . "=" . $tmp_field->get_field_ref() . $value . $tmp_field->get_field_ref();
						$tmp_res=$this->db->dbms_query($query);
						if($tmp_res) {
							list($num_rows)= $this->db->dbms_fetch_row($tmp_res);
							//$num_rows=$this->db->dbms_num_rows($tmp_res);
							$this->db->dbms_free_result($tmp_res);
							if($num_rows > 0) {
								html_showError(sprintf($MESSAGES["FIELD_EXISTS"],$tmp_alias));
								return 0;
							}
						}
					}
				}

				if($tmp_field->updatable) {

					if($tmp_field->required) {

						if($value === false) {
							html_showError(sprintf($MESSAGES["FIELD_REQUIRED"],$tmp_alias));
							return 0;
						}

						if($value=="") {
							html_showError(sprintf($MESSAGES["FIELD_REQUIRED"],$tmp_alias));
							return 0;
						}
					}

					if(!$tmp_field->check($value)) {
						html_showError(sprintf($MESSAGES["FIELD_TYPE_INCORRECT"],$tmp_alias));
						return 0;
					}
				}
			}

			if(method_exists($this,"my_check")) {
				$values= $this->get_values();
				return $this->my_check($values);
			}

			return 1;
		}

		/**
		 * PROTECTED
		 *
		 * @param array $values , associative array with the value of each row field.
		 * @return int
		 */
		protected function insert_row($values) {

			global $MESSAGES;

			if(!$this->insert_allowed) {
				if(DEBUG) {
					html_showError("Datawindow::ERROR: insert is not allowed!");
				} else {
					html_showError("DW[14]: Construction error. Contact administrator");
				}
				exit;
			}

			$this->db->dbms_begin_transaction();

			// For each field not updatable, clean its value. The value must be set on the
			// pre_insert method.
			for($i=0; $i<count($this->fields); $i++) {
				if($this->fields[$i]->updatable) continue;
				$values[$this->fields[$i]->name]=null;
			}

			if(!$this->pre_insert($values)) {
				$this->db->dbms_rollback();
				return 0;
			}

			$query="insert into " . $this->table_name . "(";
			$vals="";
			$pre="";
			for($i=0; $i<count($this->fields); $i++) {

				if($this->fields[$i]->type=="dummy") continue;

				$tmp_field= $this->fields[$i];
				$tmp_name= $this->fields[$i]->name;

				if(!$tmp_field->updatable and is_null($values[$tmp_name])) continue;

				if(isset($values[$tmp_name])) {
					$tmp_field_value= $tmp_field->get_sql_value($values[$tmp_name]);
					$query.= $pre . $tmp_name;
					$vals.=  $pre . $tmp_field_value;
					$pre=",";
				}
			}
			$query.= ") values ($vals)";

			$res=$this->db->dbms_query($query);

			if(!$res) {
				html_showError($MESSAGES["MGM_ERROR_CREATING"] . ": " . $this->db->dbms_error());
				$this->db->dbms_rollback();
				return 0;
			}

			$ok= true;
			for($i=0; $i<count($this->fields); $i++) {
				//if(!isset($values[$this->fields[$i]])) continue;

				if(isset($values[$this->fields[$i]->name]))
				$ok= $this->fields[$i]->field_insert($values[$this->fields[$i]->name]);
				if($ok === false) break;
			}

			if($ok === false) {
				html_showError($MESSAGES["MGM_ERROR_CREATING"] . ": " . $this->db->dbms_error());
				$this->db->dbms_rollback();
				return 0;
			}

			$row= implode(",", $values);
			log_write("FORM","Row inserted on table " . $this->table_name . " Values($row)",1);

			if(!$this->post_insert($values)) {
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
		protected function update_row($row_id, $old_values, $new_values) {

			global $MESSAGES;

			if(!$this->update_allowed) {
				if(DEBUG) {
					html_showError("Datawindow::ERROR: update is not allowed!");
				} else {
					html_showError("DW[11]: Construction error. Contact administrator");
				}
				exit;
			}

			$this->db->dbms_begin_transaction();

			for($i=0; $i<count($this->fields); $i++) {
				if($this->fields[$i]->updatable) continue;
				if(key_exists($this->fields[$i]->name, $new_values) and ($i != $this->identifier)) {
					$new_values[$this->fields[$i]->name]=null;
				}
			}

			if(!$this->pre_update($row_id, $old_values, $new_values)) {
				$this->db->dbms_rollback();
				return 0;
			}

			$query="update " . $this->table_name . " set ";
			$pre="";
			for($i=0; $i<count($this->fields); $i++) {

				if($this->fields[$i]->type=="dummy") continue;
				if($i == $this->identifier) continue;

				$tmp_field= $this->fields[$i];
				$tmp_name= $this->fields[$i]->name;

				if(!key_exists($tmp_name, $new_values)) continue;

				if(!$tmp_field->updatable and is_null($new_values[$tmp_name])) continue;

				$tmp_type= $this->fields[$i]->type;


				if(key_exists($tmp_name, $new_values)) {

					$tmp_field_value= $tmp_field->get_sql_value($new_values[$tmp_name]);

					$pos= strpos($tmp_name, ".");
					if($pos !== false) $tmp_name= substr($tmp_name, $pos + 1);

					if(strpos($tmp_type, "password") !== false) {
						if(trim($new_values[$tmp_name])!="") {
							$query.= $pre . $tmp_name . "=" . $tmp_field_value;
							$pre=",";
						}
					} else {
						$query.= $pre . $tmp_name . "=" . $tmp_field_value;
						$pre=",";
					}
				}
			}

			$query.= " where ". $this->fields[$this->identifier]->name . "=" . $this->fields[$this->identifier]->get_field_ref() . $row_id . $this->fields[$this->identifier]->get_field_ref();

			$res=$this->db->dbms_query($query);

			if(!$res) {
				html_showError($MESSAGES["MGM_ERROR_MODIFYING"] . ": " . $this->db->dbms_error());
				$this->db->dbms_rollback();
				return 0;
			}

			$ok= true;

			for($i=0; $i<count($this->fields); $i++) {
				//if(!isset($values[$this->fields[$i]->name])) continue;
				$old_val= isset($old_values[$this->fields[$i]->name]) ? isset($old_values[$this->fields[$i]->name]) : null;
				$new_val= isset($new_values[$this->fields[$i]->name]) ? isset($new_values[$this->fields[$i]->name]) : null;

				$ok= $this->fields[$i]->field_update($old_val, $new_val);
				if($ok === false) break;
			}

			if($ok === false) {
				html_showError($MESSAGES["MGM_ERROR_MODIFYING"] . ": " . $this->db->dbms_error());
				$this->db->dbms_rollback();
				return 0;
			}

			$old_row= implode(",", $old_values);
			$new_row= implode(",", $new_values);
			log_write("FORM","Row modified on table " . $this->table_name . ", OLD Values($old_row) to NEW Values($new_row)",1);

			if(!$this->post_update(get_http_post_param("row_id_" . $this->doc_name,""), $old_values, $new_values)) {
				$this->db->dbms_rollback();
				return 0;
			}

			$this->db->dbms_commit();
			html_showSuccess($MESSAGES["MGM_MODIFIED"]);
			return 1;
		}

		/**
		 * PROTECTED
		 * - Deletes the row identified by row_id post var.
		 */
		public function delete_row($row_id, $values) {

			global $MESSAGES;

			if(!$this->delete_allowed) {
				if(DEBUG) {
					html_showError("Datawindow::ERROR: delete is not allowed!");
				} else {
					html_showError("DW[12]: Construction error. Contact administrator");
				}
				exit;
			}


			$this->db->dbms_begin_transaction();

			if(!$this->pre_delete($row_id,$values)) {
				$this->db->dbms_rollback();
				return 0;
			}

			if($this->logical_delete) {
				$query="update " . $this->table_name . " set deleted='1' where " . $this->fields[$this->identifier]->name . "=" . $this->fields[$this->identifier]->get_field_ref() . $row_id . $this->fields[$this->identifier]->get_field_ref();
			} else {
				$query="delete from " . $this->table_name . " where " . $this->fields[$this->identifier]->name . "=" . $this->fields[$this->identifier]->get_field_ref() . $row_id . $this->fields[$this->identifier]->get_field_ref();
			}
			$res=$this->db->dbms_query($query);

			if(!$res) {
				html_showError($MESSAGES["MGM_ERROR_DELETING"] . ": " . $this->db->dbms_error());
				$this->db->dbms_rollback();
				return 0;
			}

			$row= implode(",", $values);
			log_write("FORM","Row deleted on table " . $this->table_name . ", Values($row)",1);

			if(!$this->post_delete($row_id,$values)) {
				$this->db->dbms_rollback();
				return 0;
			}

			$ok= true;

			for($i=0; $i<count($this->fields); $i++) {

				if($this->fields[$i]->type=="dummy") continue;

				$ok= $this->fields[$i]->field_delete($values[$this->fields[$i]->name]);
				if($ok === false) break;
			}

			if($ok === false) {
				html_showError($MESSAGES["MGM_ERROR_DELETING"] . ": " . $this->db->dbms_error());
				$this->db->dbms_rollback();
				return 0;
			}

			html_showSuccess($MESSAGES["MGM_DELETED"]);
			$this->db->dbms_commit();
			return 0;
		}

		public function add_search_box(&$search_box) {

			$this->search_box[]=$search_box;
			$this->restriction_search_box= $this->db->dbms_query_append($this->restriction_search_box, $search_box->get_restriction());

			$this->add_element($search_box);
		}

		private function get_restriction() {

			$ret= "";

			// And adding the restrictions from user:
			$ret= $this->db->dbms_query_append($ret, $this->restriction_param);
			$ret= $this->db->dbms_query_append($ret, $this->restriction_fields);
			$ret= $this->db->dbms_query_append($ret, $this->restriction_search_box);

			if($this->logical_delete) {
				$ret= $this->db->dbms_query_append($ret, "deleted=0");
			}

			return $ret;
		}

		private function build_select() {
			// Construct the query using the fields:

			if($this->custom_query != "") return $this->custom_query;

			$ret="select ";
			$prepend=" ";
			for($i=0; $i<count($this->fields); $i++) {
				if($this->fields[$i]->name == "") continue;
				if($this->fields[$i]->type=="dummy") continue;

				$ret .= $prepend . $this->fields[$i]->get_select_string();
				$prepend = ", ";
			}

			$ret.= " from " . $this->table_name;

			return $ret;
		}

		private function build_select_count() {

			if($this->custom_select_count != "") return $this->custom_select_count;

			$ret= "select count(*) from " . $this->table_name;
			if(trim($this->restriction) != "") {
				$ret.= " where " . $this->restriction;
			}

			return $ret;
		}

		private function build_query() {

			$query= $this->build_select();
			$this->restriction= $this->get_restriction();
			$this->select_count= $this->build_select_count();

			if(trim($this->restriction) != "") {
				if(stripos($query, "where") === false) {
					$query.= " where " . $this->restriction;
				} else {
					$query.= " and " . $this->restriction;
				}
			}

			if(trim($this->query_adds) != "") {
				$query.= " " . $this->query_adds;
			}

			return $query;
		}

		private function check_datawindow_action($row_id, $dw_action) {

			// TODO, check

		}

		private function save_datawindow_action($row_id, $action) {

			$this->get_unique_doc_id();

			$row_id_ref= $this->get_unique_var("row_id");
			$action_ref= $this->get_unique_var("action");

			$_SESSION[$row_id_ref]= $row_id;
			$_SESSION[$action_ref]= $action;

		}

		private function restore_datawindow_action(&$row_id, &$action) {

			$row_id_ref= $this->get_unique_var("row_id");
			$action_ref= $this->get_unique_var("action");

			$row_id= $_SESSION[$row_id_ref];
			$action= $_SESSION[$action_ref];

		}

		private function get_unique_var($var_name) {

			return APP_NAME . $this->doc_ref . "." . $var_name;

		}

		/**
		 * Protected function. Will return the background color of a row.
		 * You can write your own on your inherited class.
		 *
		 * @param boolean $filaPar
		 * @param array $values
		 * @return string
		 */
		protected function get_row_color($filaPar, $values) {
			return "";
		}

		protected function get_cell_color($field, $value) {
			return "";
		}

		protected function show_toolbar_header($numRows, $nav_str) {
			global $MESSAGES;
?>
					<tr class="data_box_toolbar">
						<td align="left" class="data_box_toolbar_tools">
							<?php	$this->show_reload_button();
							if($this->insert_allowed) {	echo "&nbsp;"; $this->show_insert_button(); }
							if($this->export_allowed) {	echo "&nbsp;"; $this->show_export_button(); }
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

		protected function show_toolbar_footer($nav_str) {
			if($nav_str=="") return;
?>
					<tr class="data_box_toolbar">
						<td align="right" class="data_box_toolbar_nav_footer" colspan="3">
							&nbsp;&nbsp;<?php echo $nav_str; ?>
						</td>
					</tr>
<?php
		}
	}
?>
