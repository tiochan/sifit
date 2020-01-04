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


	class datawindow_table {

		public $table_name;
		public $fields;
		public $field_id;
		public $restrictions;

		public $insert_allowed;
		public $update_allowed;
		public $delete_allowed;

		public $allow_changes;

		public $logical_delete=false;

		/**
		 * Table definition for datawindows purposes
		 *
		 * @param string $table_name
		 * @param array of fields $fields
		 * @param integer $field_id, the array index of the identifier field, set null for no identifier
		 * @param boolean $insert_allowed
		 * @param boolean $update_allowed
		 * @param boolean $delete_allowed
		 * @return datawindow_table
		 */
		public function datawindow_table($table_name, &$fields, $field_id, $insert_allowed, $update_allowed, $delete_allowed) {

			$this->table_name= $table_name;

			if($field_id !== null and !isset($fields[$field_id])) {
				if(DEBUG) html_showError("datawindow_table::constructor the identifier index is not set on fields array");
				else html_showError("Error code DWT[2238]");
				exit;
			}

			$this->allow_changes= (!CLI_MODE and ($insert_allowed or $update_allowed or $delete_allowed));

			if($this->allow_changes and !isset($fields[$field_id])) {
				if(DEBUG) html_showError("datawindow_table::constructor table allow changes, but no field id is set.");
				else html_showError("Error code DWT[2239]");
				exit;
			}

			$this->fields= $fields;

			// Fields must be preceded by table name
			$search_for= $table_name . ".";
			foreach($fields as $field) {
				if($field->type=="dummy") continue;

				if(strpos($field->name, $search_for) === false) {
					html_showError("Error on the declaration of field $field->name: Each field must be defined using syntax 'table_name.field_name'<br>");
					exit;
				}
			}

			$this->restrictions= array();

			$this->field_id= $field_id;

			$this->insert_allowed= (!CLI_MODE and $insert_allowed);
			$this->update_allowed= (!CLI_MODE and $update_allowed);
			$this->delete_allowed= (!CLI_MODE and $delete_allowed);
		}

		/**
		 * Adds a new restriction for the specified field index.
		 *
		 * @param integer $field_index
		 * @param string $restriction
		 */
		public function add_restriction($field_index, $restriction) {

			if(!isset($this->fields[$field_index])) {
				if(DEBUG) html_showError("datawindow_table::add_restriction the identifier index is not set on fields array");
				else html_showError("Error code DWT[2239]");
				exit;
			}

			$this->restrictions[]= $this->fields[$field_index]->name . " $restriction";
		}

		/**
		 * Adds a new FREE FORM restriction. Be careful, because the field reference must
		 * have the table preference (in the form table.field_name)
		 *
		 * @param string $restriction
		 */
		public function add_custom_restriction($restriction) {

			$this->restrictions[]= $restriction;
		}

		/**
		 * Returns the SELECT statement for this table. The fields are preceded
		 * with the table name.
		 * The SELECT string is not included.
		 *
		 * @return string
		 */
		public function get_select() {

			$select="";
			$field_separator="";
			foreach($this->fields as $field) {
				if($field->type == "dummy") continue;
//				$select.= $field_separator . $field->name . " as \"" . $field->name . "\"";
				$select.= $field_separator . $field->get_select_string() . " as \"" . $field->name . "\"";
				$field_separator= ", ";
			}

			return $select;
		}

		/**
		 * Returns the WHERE statement for tget_wherehis table. The fields are preceded
		 * with the table name.
		 * The WHERE string is not included.
		 *
		 * @return string
		 */
		public function get_where() {

			// First, use custom restrictions set by user
			$where= $this->get_property("restrictions","and");

			// Now add restrictions set by fields
			$fields_where= $this->logical_delete ? $this->table_name . ".deleted=0" : "";

			$sep= $fields_where == "" ? "" : " and ";

			foreach($this->fields as $field) {
				if($field->type == "dummy") continue;
				if(($current_field_restriction= $field->get_restriction()) != "") {
					//$fields_where.= $sep . $this->table_name . "." . $current_field_restriction;
					$fields_where.= $sep . $current_field_restriction;
					$sep= " and ";
				}
			}

			if($fields_where != "") {
				$where= $where == "" ? $fields_where : $where . " and " . $fields_where;
			}

			return $where;
		}

		/**
		 * Call each field to check for its fields status after a submit.
		 * Will return 0, if there is any field with error, or 1 if all
		 * is ok.
		 *
		 * @param boolean $for_insert
		 * @param dbms_class $db
		 * @param array $values
		 * @return integer
		 */
		public function check_fields_values(&$values, $for_insert, & $db = null) {

			foreach($this->fields as $field) {

				if($field->type == "dummy") continue;

				$value= isset($values[$field->name]) ? $values[$field->name] : null;
				if(!$field->check_field_value($value, $for_insert, $db, $this->table_name)) return 0;
			}

			return 1;
		}

		/**
		 * Return an array with the values of all the fields
		 *
		 * @return array
		 */
		public function get_fields_values() {

			$field_values= array();

			foreach($this->fields as $field) {
				$field_values[]= $field->value;
			}

			return $field_values;
		}

		private function get_property($property, $separator) {

			$ret="";
			$sep= "";
			foreach($this->$property as $custom_property) {
				$ret.= $sep . $custom_property;
				$sep=" $separator ";
			}

			if($ret != "") $ret= "( $ret )";

			return $ret;
		}

		public function show_hidden() {
			// No actions allowed on CLI mode
			if(CLI_MODE) return;

			foreach($this->fields as $field) $field->show_hidden();
		}

		public function insert_row(&$db, $values) {

			// No actions allowed on CLI mode
			if(CLI_MODE) return 0;


			if(!$this->allow_changes) return 0;

			$insert_into_fields="";
			$insert_into_values="";
			$pre="";

			foreach($this->fields as $field) {

				//if(!$field->updatable or ($field->type=="auto") or ($field->type=="dummy")) continue;
				/**
				 * Correction: rather than field is not updatable, if is set with a value, then
				 * it will be used to construct the query.
				 * The intention is that fields can be set by code (usually on pre-insert method)
				 */
				if(($field->type=="auto") or ($field->type=="dummy")) continue;

				if(isset($values[$field->name])) {
					$field_name= $field->name;
					$pos= strpos($field_name, ".");
					if($pos !== false) $field_name= substr($field_name, $pos + 1);
					$insert_into_fields.= $pre . $field_name;
					$insert_into_values.=  $pre . $field->get_sql_value($values[$field->name]);
					$pre=",";
				}
			}

			if(($insert_into_fields == "") or ($insert_into_values == "")) return 0;

			$query="INSERT INTO $this->table_name ($insert_into_fields) VALUES ($insert_into_values)";

			if(!$res=$db->dbms_query($query)) return 0;

			$new_row= implode(",", $values);
			log_write("FORM","Row inserted on table " . $this->table_name . ", NEW Values($new_row)",1);

			return 1;
		}

		public function update_row(&$db, $row_id, $old_values, $new_values) {

			// No actions allowed on CLI mode
			if(CLI_MODE) return 0;


			if(!$this->allow_changes or ($this->field_id===null)) return 0;

			$field_id= $this->fields[$this->field_id];

			$pre="";
			$update="";

			foreach($this->fields as $field) {

				if($field->type=="dummy") continue;
				if(!$field->updatable or !key_exists($field->name, $new_values)) continue;
				if((strpos($field->type, "password")!== false) and (trim($new_values[$field->name])=="")) continue;

				$field_name= $field->name;
				$pos= strpos($field_name, ".");
				if($pos !== false) $field_name= substr($field_name, $pos + 1);

				$update.= $pre . $field_name . "=" . $field->get_sql_value($new_values[$field->name]);
				$pre=", ";
			}

			$query="UPDATE " . $this->table_name . " SET ";
			$query.= $update;
			$query.= " WHERE ". $field_id->name . "=" . $field_id->get_sql_value($row_id);

			if(!$res=$db->dbms_query($query)) return 0;

			$old_row= implode(",", $old_values);
			$new_row= implode(",", $new_values);
			log_write("FORM","Row modified on table " . $this->table_name . ", OLD Values($old_row) to NEW Values($new_row)",1);

			return 1;
		}

		public function delete_row(&$db, $row_id, $old_values) {

			// No actions allowed on CLI mode
			if(CLI_MODE) return 0;


			if(!$this->allow_changes or ($this->field_id===null)) return 0;

			$field_id= $this->fields[$this->field_id];

			if($this->logical_delete) {
				$query="UPDATE " . $this->table_name . " SET deleted='1' WHERE " . $field_id->name . "=" . $field_id->get_sql_value($row_id);
			} else {
				$query="DELETE FROM " . $this->table_name . " WHERE " . $field_id->name . "=" . $field_id->get_sql_value($row_id);
			}

			if(!$res=$db->dbms_query($query)) return 0;

			$row= implode(",", $old_values);
			log_write("FORM","Row deleted on table " . $this->table_name . ", Values($row)",1);

			return 1;
		}
	}


	/**
	 * Elements of a query:
	 *
	 * - tables
	 * - tables fields, associated to each table
	 * - joins [optional] - if there are more than 1 table
	 *
	 */
	class datawindow_query {

		public $tables;
		public $group_by;
		public $order_by;
		public $order_by_fields;

		public $joins;

		public $custom_restriction;

		public $insert_allowed;
		public $update_allowed;
		public $delete_allowed;

		public $allow_changes;

		public $fields;

		public $table_id;
		public $field_id;

		public $parent_datawindow;		// The datawindow which is attached to
										// This property is set by the datawindow object

		/**
		 * @var dbms_class
		 */
		public $db;

		/**
		 * Creates a new datawindow query object for datawindow objects
		 *
		 * @param dbms_class $optional_db
		 * @return datawindow_query
		 */
		public function datawindow_query (& $optional_db=null) {

			global $global_db;

			$this->tables= array();
			$this->fields= array();
			$this->joins= array();
			$this->group_by= array();
			$this->order_by_fields= array();
			$this->custom_restriction= array();

			$this->insert_allowed= false;
			$this->update_allowed= false;
			$this->delete_allowed= false;

			$table_id= null;
			$field_id= null;

			$this->allow_changes= false;

			$this->db= ($optional_db === null) ? $global_db : $optional_db;
		}

		/**
		 * Adds a new table definition
		 *
		 * @param datawindow_table $table
		 */
		public function add_table(& $table) {

			/**
			 * Only one table can allow changes. If more than one are defined
			 * as changeable, only the first will maintain the definition. The
			 * others are set as not changeable.
			 *
			 * The first table that allow changes will be used to identity the
			 * rows. (Its identifier field will be used).
			 */
			$is_id=false;

			if(!CLI_MODE and $this->allow_changes) {
				$table->allow_changes= false;
				$table->insert_allowed= false;
				$table->update_allowed= false;
				$table->delete_allowed= false;
			} else {
				/**
				 * If there isn't still any table which accept changes, the
				 * field_id will remain for the first table added.
				 *
				 * If this table accept changes or is the first added, will
				 * take it for the field_id.
				 */

				if($this->field_id===null or $table->allow_changes) {
					$this->allow_changes= $table->allow_changes;
					$this->insert_allowed= $table->insert_allowed;
					$this->update_allowed= $table->update_allowed;
					$this->delete_allowed= $table->delete_allowed;

					$this->table_id= count($this->tables);		// This table is the identifier
					$is_id= true;
				}
			}

			$this->tables[]= $table;

			for($i=0; $i < count($table->fields); $i++) {
				if($is_id and ($i== $table->field_id)) $this->field_id= count($this->fields);
				$this->fields[]= & $table->fields[$i];
			}
		}

		/**
		 * Creates an array, ordered by field show order.
		 *
		 * @return field array
		 */
		public function & get_visible_fields($for_insert, $for_update) {

			$fields=array();
			$field_order=array();

			foreach($this->fields as $field) {
				if($field->will_be_displayed($for_insert, $for_update)) {
					$order= sprintf("%5d_%s",$field->show_order, $field->name);
					$fields[$order]= $field;
				}
			}

			ksort($fields);

			foreach($fields as $field) {
				$field_order[]= $field;
			}

			return $field_order;
		}


		/**
		 * Defines a new join between two tables:
		 *
		 * @param datawindow_table $table_1
		 * @param integer $field_1 , index of field of table_1
		 * @param datawindow_table $table_2
		 * @param integer $field_2 , index of field of table_2
		 * @param string $operator , the operator used to join tables
		 * @return datawindow_join
		 */
		public function add_join(& $table_1, $field_1, & $table_2, $field_2, $operator="=") {

			$table_1_name= $table_1->table_name;
			$table_2_name= $table_2->table_name;

			$field_1_name= $table_1->fields[$field_1]->name;
			$field_2_name= $table_2->fields[$field_2]->name;

			switch($operator) {
				case "=":
					$this->joins[]= "$field_1_name = $field_2_name";
					break;
				default:
					if(DEBUG) html_showError("datawindow_join: Operator not supported.");
					else html_showError("Error code DWJ[1432].");
					exit;
			}
		}

		// public function add_join(& $table_join) { $this->joins[]= $table_join->join; }
		protected function get_join() {  return $this->get_property("joins","and"); }

		/**
		 * Adds a new group by clause
		 *
		 * @param string $group_by
		 */
		public function add_group_by($group_by) { $this->group_by[]= $group_by;	}
		protected function get_group_by() {  return $this->get_property("group_by","and"); }

		/**
		 * Adds a new order by by clause
		 *
		 * @param string $order_by
		 */
		public function add_order_by($order_by) { $this->order_by[]= $order_by;	}

		/**
		 * Defines a new order by for a table field
		 *
		 * @param datawindow_table $table_1
		 * @param integer $field_1 , index of field of table_1
		 * @param datawindow_table $table_2
		 * @param integer $field_2 , index of field of table_2
		 * @param string $operator , the operator used to join tables
		 * @return datawindow_join
		 */
		public function add_order_by_field(& $table, $field, $ascending="a") {

			$table->fields[$field]->can_order= true;
			if($ascending=="a") {
				$table->fields[$field]->order_by= "a";
			} elseif($ascending=="d") {
				$table->fields[$field]->order_by= "d";
			}

			$this->order_by_fields[]= array($table, $field);
		}

		protected function get_order_by() {

			$order_by= "";

			$first= $order_by!= "" ? "," : "";

			foreach($this->order_by_fields as $aux) {

				$table= $aux[0];
				$field_index= $aux[1];

				$field= $table->fields[$field_index];

				$curr_order_by= $field->get_order_by();
				if($curr_order_by != "") {
					$order_by.= $first . $curr_order_by;
					$first=",";
				}
			}

			if($order_by == "") {
				$order_by= $this->get_property("order_by"," ,");
			}

			return $order_by;
		}


		/**
		 * Adds a new FREE FORM restriction. Be careful, because the field reference must
		 * have the table preference (in the form table.field_name)
		 *
		 * @param string $restriction
		 */
		public function add_custom_restriction($restriction) { $this->custom_restriction[]= $restriction; }
		protected function get_custom_restriction() { return $this->get_property("custom_restriction","and"); }


		private function get_property($property, $separator) {

			$ret="";
			$sep= "";

			if(!property_exists($this, $property)) return "";

			if(count($this->$property) == 0) return "";
			foreach($this->$property as $custom_property) {
				$ret.= $sep . "$custom_property";
				$sep=" $separator ";
			}

			//if($ret != "") $ret= "( $ret )";

			return $ret;
		}

		protected function get_select() {

			$select= "";
			$select_separator="";
			foreach($this->tables as $table) {
				$current_select= $table->get_select();
				if($current_select != "") {
					$select .= $select_separator . $current_select;
					$select_separator= ", ";
				}
			}

			return $select;
		}

		protected function get_from() {

			$from= "";
			$table_separator="";
			foreach($this->tables as $table) {
				$from.= $table_separator . $table->table_name;
				$table_separator= ", ";
			}

			return $from;
		}

		protected function get_where() {

			$where= "";
			$where_separator="";

			foreach($this->tables as $table) {

				$current_where= $table->get_where();
				if($current_where != "") {
					$where .= $where_separator . $current_where;
					$where_separator= " and ";
				}
			}

			return $where;
		}

		/**
		 * Returns the query for current object definition
		 *
		 * @return string
		 */
		public function get_query() {

			$query="";

			$select=   $this->get_select();
			$from=     $this->get_from();
			$where=    $this->get_where();
			$group_by= $this->get_group_by();
			$order_by= $this->get_order_by();

			// If no selection has defined, something is wrong.. not usual.
			if(($select == "") or ($from == "")) die("ERROR 10501");

			// Add join
			$where= $this->db->dbms_query_append($where, $this->get_join());
			$where= $this->db->dbms_query_append($where, $this->get_custom_restriction());
			$where= $this->db->dbms_query_append($where, $this->parent_datawindow->get_restriction());

			if($where != "") $where= "WHERE $where";
			if($group_by != "") $group_by= "GROUP BY $group_by";
			if($order_by != "") $order_by= "ORDER BY $order_by";

			$query= "SELECT $select FROM $from $where $group_by $order_by";

			return $query;
		}

		public function insert_row($values) {

			// No actions allowed on CLI mode
			if(CLI_MODE) return 0;


			if($this->table_id === null) return 0;

			$table= $this->tables[$this->table_id];
			return $table->insert_row($this->db, $values);
		}

		public function update_row($row_id, $old_values, $new_values) {

			// No actions allowed on CLI mode
			if(CLI_MODE) return 0;


			if($this->table_id === null) return 0;

			$table= $this->tables[$this->table_id];
			return $table->update_row($this->db, $row_id, $old_values, $new_values);
		}

		public function delete_row($row_id, $old_values) {

			// No actions allowed on CLI mode
			if(CLI_MODE) return 0;


			if($this->table_id === null) return 0;

			$table= $this->tables[$this->table_id];
			return $table->delete_row($this->db, $row_id, $old_values);
		}

		/**
		 * Returns the count(*) query for current object definition
		 *
		 * @return string
		 */
		public function get_count_query() {

			global $global_db;

			$query="";

			$from=     $this->get_from();
			$where=    $this->get_where();
			$group_by= $this->get_group_by();

			// If no selection has defined, something is wrong.. not usual.
			if($from == "") die("ERROR 10502");

			// Add join
			$where= $this->db->dbms_query_append($where, $this->get_join());
			$where= $this->db->dbms_query_append($where, $this->get_custom_restriction());
			$where= $this->db->dbms_query_append($where, $this->parent_datawindow->get_restriction());

			if($where != "") $where= "WHERE $where";
			if($group_by != "") $group_by= "GROUP BY $group_by";

			$query= "SELECT count(*) FROM $from $where $group_by";

			return $query;
		}

		/**
		 * Returns the query to select only one row, using the identifier field
		 * of the first changeable table.
		 *
		 * @param string $row_id_value
		 */
		public function get_row_query($row_id_value) {

			$query="";

			$select=   $this->get_select();
			$from=     $this->get_from();
			$where=    $this->get_where();

			// If no selection has defined, something is wrong.. not usual.
			if(($select == "") or ($from == "")) die("ERROR 10501");

			// Add join
			$where= $this->db->dbms_query_append($where, $this->get_join());
			$where= $this->db->dbms_query_append($where, $this->get_custom_restriction());

			// Row restriction
			$table_id= & $this->tables[$this->table_id];
			$field_id= & $table_id->fields[$table_id->field_id];
			//$row_restriction= $table_id->table_name . "." . $field_id->name . "=" . $field_id->get_field_ref() . $row_id_value . $field_id->get_field_ref();
			$row_restriction= $field_id->name . "=" . $field_id->get_field_ref() . $row_id_value . $field_id->get_field_ref();

			$where= $this->db->dbms_query_append($where, $row_restriction);


			$query= "SELECT $select FROM $from WHERE $where";

			return $query;
		}

		/**
		 * Call each table to check for its fields status after a submit.
		 * Will return 0, if there is any field table with error, or 1 if all
		 * is ok.
		 *
		 * @param boolean $for_insert
		 * @param dbms_class $db
		 * @param array $values
		 * @return integer
		 */
		public function check_fields_values(& $values, $for_insert, & $db = null) {

			foreach($this->tables as $table) {
				if(!$table->check_fields_values($values, $for_insert, $db)) return 0;
			}
			return 1;
		}

		/**
		 * Return an array with the values of all the fields of each table
		 *
		 * @return array
		 */
		public function get_fields_values() {

			$field_values= array();

			foreach($this->tables as $table) {
				$field_values= array_merge($field_values, $table->get_fields_values());
			}

			return $field_values;
		}

		/**
		 * Send the call to all objects to echo hidden code
		 *
		 */
		public function show_hidden() {

			// No actions allowed on CLI mode
			if(CLI_MODE) return 0;


			foreach($this->tables as $table) $table->show_hidden();
		}

		/**
		 * Return the array of fields that will be shown
		 *
		 */
		public function get_fields_list() {

			$fields_list= array();

			foreach($this->tables as $table) {
				$fields_list= array_merge($fields_list, $table->get_fields_list());
			}

			return $fields_list;
		}

		/**
		 * Given one row, an associative array from a SQL query, will return the
		 * value of the identifier field of all query.. (if any)
		 *
		 * @param array $row
		 * @return string
		 */
		public function get_identifier_value_from_row($row) {

			if($this->table_id === null) return null;

			return $this->tables[$this->table_id]->get_identifier_value_from_row($row);
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
		public function get_values_from_post(&$values) {

			foreach($this->fields as $field) {
				if(($val= $field->get_value_from_post()) !== false) {
					$values[$field->name] = $val;
				}
			}
			return $values;
		}

		/**
		 * Get the values from $row and set them to form fields.
		 *
		 * - Fill the values array using the result of a query. The returned
		 *   array is an associative array, and if there is a field identifier,
		 *   will fill a "row_id" association with its value.
		 *
		 * @param $res: The result from a query, must be an associative array.
		 * @param $values: Defined previously as array, passed by reference.
		 */
		public function get_values_from_row($row, &$values) {

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

			foreach($this->fields as $field) {

				if(!isset($row[$field->name])) continue;

				$values[$field->name]= $row[$field->name];
			}

			if($this->field_id !== null) $values["row_id"]= $row[$this->fields[$this->field_id]->name];
		}

		/**
		 * Fill a values array using the result of a query
		 *
		 * @param $res: The result from a query.
		 * @param $values: Defined previously as array, passed by reference.
		 * @param $real: Determines if the value that must return is the real database.field value or the datawindow.field value
		 */
		public function get_values_from_query($res, &$values, $real=0) {

			$row= $this->db->dbms_fetch_array($res);

			foreach($this->fields as $field) {
				if($field->type == "dummy") continue;

				if($real) {
					$values[$field->name]= $row[$field->name];
				} else {
					$values[$field->name]= $field->get_value($row[$field->name], false);
				}
			}

			if($this->field_id !== null) $values["row_id"]= $row[$this->fields[$this->field_id]->name];
		}

		/**
		 * Fill the values of the row identified by row_id
		 *
		 * @param $row_id: The row identifier
		 * @param $values: Defined previously as array, passed by reference.
		 */
		public function get_values_from_id($row_id, &$values) {

			$query= $this->get_row_query($row_id);
			$res=$this->db->dbms_query($query);

			if(!$this->db->dbms_check_result($res)) {
				$this->db->dbms_free_result($res);
				return 0;
			}

			$this->get_values_from_query($res, $values);
			$this->db->dbms_free_result($res);
			return 1;
		}

		/**
		 * Fill the fields of the form using the result of a query
		 *
		 * @param db_result $res
		 */
		public function recover_values_from_query($res) {

			// No actions allowed on CLI mode
			if(CLI_MODE) return 0;


			$values=Array();
			$row= $this->db->dbms_fetch_array($res);

			foreach($this->fields as $field) {
				if(!isset($row[$field->name])) continue;
				// TODO: Here is the place where the value is set to the field
				// There is a kind of error when the value contains, p.e. </script>
				$field->set_form_value($this->parent_datawindow->form_name, $field->get_real_value($row[$field->name]));
			}

			if($this->field_id !== null) {
				$field= $this->fields[$this->field_id];
				if(!isset($row[$field->name])) {
					if(DEBUG) html_showError("datawindow_query::recover_values_from_query: Object is editable, but no row_id found at row set");
					else html_showError("Error code DWQ[2472].");
				}
				html_set_field_value($this->parent_datawindow->form_name,"row_id_" . $this->parent_datawindow->doc_name,$field->get_value($row[$field->name], false));
			}

			$values["row_id"]= $row[$field->name];
		}

		/**
		 * Fill the fields of the form using the array.
		 * The array must be in the form key=>value, where key is the name of the field.
		 *
		 * @param array $values
		 */
		public function set_form_values($values) {

			// No actions allowed on CLI mode
			if(CLI_MODE) return 0;


			foreach($this->fields as $field) {
				$value= isset($values[$field->name]) ? $values[$field->name] : "";

				if($value != "") html_set_field_value($this->parent_datawindow->form_name, $field->plain_name, $value);
			}
		}

		public function get_cell_color($field, $value) { return ""; }
	}
?>
