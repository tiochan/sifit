<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage forms
 *
 */


	require_once "field_types/basic.inc.php";
	require_once "field_types/listbox.inc.php";
	require_once "field_types/lookups.inc.php";
	require_once "field_types/foreign_key.inc.php";


	class field_ext {

		public $parent;

		public $name;
		public $plain_name;				// Unique field name (changes over adoptions)
		public $html_name;				// Unique plain name for HTML (replaces "." for "_"), which doesn't changes over adoptions.
		public $alias;

		public $type;
		public $required=false;			// Is this field required?
		public $is_unique=false;		// Must its value be unique?
		public $visible=true;			// Must this field be shown?

		public $hide_on_insert=false;	// Must this field be shown on insert?
		public $hide_on_update=false;	// Must this field be shown on update?

		public $show_order=0;			// 0 = not visible.
		public $reference;				// Reference to the element (optional)
		public $updatable=false;
		public $default_value;

		public $is_detail;				// Is this field referenced from other form?
		public $persist_detail=false;	// If detail must persist on next events (show detail value as hidden field)
		public $master_value;			// If is detail: value referenced from master
		public $last_value;				// Last value returned on las get_value call

		public $col_modifier;

		public $show_max_len;			// If != 0, then will be wrapped to len (for show)

		public $can_order=false;		// If true can be able to add order symbols on data objects
		public $default_order_by;		// in case that can order, its the default order by value.
		public $order_by;				// in case that can order, this property is initialized with
										// last call parameters.

		public $value;

		/**
		 * field constructor
		 *
		 * @param string $field_name, Name of the field (in the query result)
		 * @param string $field_alias, Name shown at the field header
		 * @param string $field_type, auto,integer,bool,string,password,date,text,foreign_key,link,listbox
		 * @param bool   $required, if true, then at new, update this field will be checked to have value
		 * @param bool   $is_unique, if true, at new, update this field will be checked to have an unique value
		 * @param bool   $visible, if true it will be hidden
		 * @param bool   $updatable, if true it can be modified.
		 * @param string $default_value=null, default value for this field
		 * @param string $reference=NULL, the reference for special fields: foreign_key,link and listbox
		 * @return master_field
		 */

		function field_ext($field_name, $field_alias, $field_type, $required, $is_unique, $show_order, $updatable, $default_value=null, $reference=NULL) {

			if($field_type == "text") $field_type="ftext";

			$this->parent= null;

			$this->name=$field_name;
			$this->plain_name= str_replace(".","_",$field_name);
			$this->html_name= $this->plain_name;
			$this->alias=$field_alias;
			$this->type=$field_type;
			$this->required=$required;
			$this->is_unique=$is_unique;
			$this->visible=$show_order != 0;
			$this->show_order=$show_order;
			$this->reference=$reference;
			$this->updatable=$updatable;
			$this->last_value=null;
			$this->default_value=$default_value;

			$this->show_max_len=0;

			if(is_null($this->reference)) {
				if(class_exists($field_type)) {
					// Note that file containing new field type must be included before the call.
					$this->reference= new $field_type($default_value);
				} else {
					html_showError("ERROR: field::field, field type not supported: $field_type.<br>Class not defined or not included.");
					exit;
				}
			}

			if($field_type == "auto") $this->is_unique= false;

			$this->reference->default_value=$default_value;
			$this->is_detail=false;

			$this->initial_tasks();
		}

		/**
		 * All tasks to be executed initially or when is adopted.
		 * Basically all whose depends on field plain name, which depends
		 * on parents.
		 *
		 */
		protected function initial_tasks() {

			// Is referenced from master?
			$master_value= get_http_param("detail_" . $this->plain_name);

			if($master_value != null) {
				$this->is_detail=true;
				$this->default_value=$master_value;
				$this->reference->default_value= $master_value;
				$this->master_value= $master_value;
			}

			// Now check for a master form references to this field
			$this->order_by= get_http_param("order_by_" . $this->plain_name, $this->default_order_by);
			$this->col_modifier= $this->reference->col_modifier;

			$this->value= $this->get_value_from_post();

			if(!$this->value) $this->value= $this->reference->default_value;
		}

		/**
		 * Adopted is called from its parent when field is added to it.
		 *
		 * @param mixed $parent
		 */
		public function adopted(& $parent) {

			$this->parent= $parent;

			$this->plain_name= str_replace(".","_",$parent->doc_name) . "_" . $this->plain_name;
			$this->initial_tasks();
		}

		/**
		 * Will the field be shown?
		 *
		 * Fields that can define the visibility of a field:
		 *
		 * - If is not for update then, if the field is visible then it can
		 *   be shown
		 * - If is for update then, will depend on:
		 *   - type, not (auto or hidden)
		 *   - visible, set to true
		 *
		 * Order of preference:
		 *   type: if a field is defined as "auto" then it will not be shown.
		 *   visible: if is visible will be shown.
		 *
		 * @param boolean $for_update
		 * @return boolean
		 */
		function will_be_displayed($for_insert, $for_update) {

			if($for_insert) {
				if($this->type== "auto") return false;
				if($this->type== "hidden") return false;
				if($this->hide_on_insert) return false;
				if(!$this->updatable and !$this->visible) return false;

				return true;
			}
			if($for_update) {

				if($this->type== "auto") return false;
				if($this->type== "hidden") return false;
				if($this->hide_on_update) return false;
				if(!$this->updatable and !$this->visible) return false;

				return true;
			}

			return $this->visible;
		}

		/**
		 * Show the field.
		 * The field name must be set again, so objects can change the field name
		 *
		 * @param unknown_type $for_update
		 */
		function show($for_insert, $for_update) {

			if($this->is_detail) {
				$for_update=true;
			}

			if($this->will_be_displayed($for_insert, $for_update)) {
				$this->reference->show($this->plain_name, (($for_insert or $for_update) and !$this->updatable));
			}
		}

		/**
		 * For data objects, will show the field header: alias, column order options, etc.
		 *
		 * @return string
		 */
		function get_field_header($data_object) {

			$ret= $this->alias;

			if($this->can_order) {

				$ret.="<div class='data_order_by'>";
				$ret.= "&nbsp;&nbsp;";
				if($this->order_by!="a") $ret.= $data_object->get_action_button(FE_UP_ICON,"ascending","order_by_" . $this->plain_name, "a");
				else $ret.= $data_object->get_action_button(FE_UP_SELECTED_ICON,"ascending","order_by_" . $this->plain_name, "");
				if($this->order_by!="d") $ret.= $data_object->get_action_button(FE_DOWN_ICON,"descending","order_by_" . $this->plain_name, "d");
				else $ret.= $data_object->get_action_button(FE_DOWN_SELECTED_ICON,"descending","order_by_" . $this->plain_name, "");
				$ret.="</div>";
			}

			return $ret;
		}

		/**
		 * If this field can order, return current value of ordering, if there is
		 * not order value, return an empty string.
		 * else return false.
		 *
		 * @return string
		 */
		function get_order_by() {

			if($this->can_order) {
				if($this->order_by == "a") return $this->name . " ASC";
				elseif($this->order_by == "d") return $this->name . " DESC";
				else return "";
			}
			else return false;
		}

		function set_focus($form_name) {
			$this->reference->set_focus($form_name, $this->plain_name);
		}

		/**
		 * Render the most basic structure of an html field
		 *
		 */
		function force_show($forced_field_name="") {

			$field_name_to_show= $forced_field_name!="" ? $forced_field_name : $this->plain_name;
			$this->reference->show_simply($field_name_to_show);
		}

		/**
		 * Checks if the specified value is allowed for the field type.
		 * Returns true if the value pass the test, else returns false.
		 *
		 * @param mixed $field_value
		 * @return boolean
		 */
		function check($field_value) {
			return $this->reference->check($field_value);
		}

		/**
		 * How to reference the value of this field type on a SQL query.
		 * Returns the set of chars that must be added at the start and the end
		 * of a value reference.
		 * For example, for a string field type will return "'", so this char
		 * will be used before and after the value:
		 * "'" . "$string_value . "'"
		 *
		 * @return string
		 */
		function get_field_ref() {
			return $this->reference->value_reference;
		}

		/**
		 * Using its field name, get the value from last submit
		 *
		 * @return unknown
		 */
		function get_value_from_post() {
			return $this->reference->get_value_from_post($this->plain_name);
		}

		/**
		 * This method is used to set the value of a field on an HTML form.
		 * Be careful, because this will not set the field value, else will
		 * echo the html/javascript code to set the value to this field on the
		 * form.
		 *
		 * @param string $form_name
		 * @param string $value
		 * @return string
		 */
		function set_form_value($form_name, $value) {

			if($this->visible or ($this->required and ($this->type!="auto")) or	($this->updatable and ($this->type!="auto"))) {
				return $this->reference->set_form_value($form_name, $this->plain_name, $value);
			} else {
				return 0;
			}
		}

		function set_form_default_value($form_name) {
			return $this->reference->set_form_default_value($form_name, $this->plain_name,$this->updatable);
		}

		/**
		 * Given a value, returns its addapted to the field type.
		 * Must indicate if the value will be used for show, or not.
		 *
		 * @param string $initial_value
		 * @param boolean $for_show
		 * @return mixed
		 */
		function get_value($initial_value, $for_show= true) {

			$this->last_value= $this->reference->get_value($initial_value, $for_show);
			if($for_show and $this->show_max_len != 0) {
				$new_field_value= strlen($this->last_value) > $this->show_max_len ? substr($this->last_value, 0, $this->show_max_len - 2) . ".." : $this->last_value;
				return $new_field_value;
			} else {
				return $this->last_value;
			}
		}

		/**
		 * Returns the default value
		 *
		 * @return mixed
		 */
		function get_default_value() {
			return $this->default_value;
		}

		/**
		 * Oriented for field types like foreign keys, where the real value is
		 * the referenced value, not the current value.
		 *
		 * @param mixed $field_value
		 * @return mixed
		 */
		function get_real_value($field_value) {
			return $this->reference->get_real_value($field_value);
		}

		/**
		 * How to reference a field into a SQL query
		 *
		 * @return string
		 */
		function get_select_string() {
			return $this->reference->get_select_string($this->name);
		}

		/**
		 * How to set the value into a SQL query
		 *
		 * @param mixed $field_value
		 * @return string
		 */
		function get_sql_value($field_value) {
			return $this->reference->get_sql_value($field_value);
		}

		/**
		 * For search purposes.
		 * The value may contain 'OR', 'AND' and more.
		 * This function returns the SQL code to paste into a SQL query for
		 * search values for this field type (and name).
		 *
		 * @param mixed $field_value
		 * @return string
		 */
		function get_query_string($field_value) {
			return $this->reference->get_query_string($this->name, $field_value);
		}

		/**
		 * Returns the last value used for this field
		 *
		 * @return mixed
		 */
		function get_last_value() {
			return $this->last_value;
		}

		/**
		 * This is the space to show all hidden HTML code as necessary. Here,
		 * the field name can be changed.
		 *
		 */
		function show_hidden() {

			if($this->is_detail and $this->persist_detail) {
				echo "<input type='hidden' name='detail_" . $this->html_name . "' value='" . $this->default_value . "'>\n";
			}
/*			if($this->is_detail) {

				//$var_name= "detail_" . str_replace(".","_",$this->name);
				echo "<input type='hidden' name='detail_" . $this->plain_name . "' value='" . $this->default_value . "'>\n";
				if(isset($GLOBALS["is_detail"])) {
					echo "<input type='hidden' name='is_detail' value='1'>\n";
				}
			}
*/
			if($this->can_order) {
				echo "<input type='hidden' name='order_by_" . $this->plain_name . "' value='" . $this->order_by . "'>";
			}

			$this->reference->show_hidden($this->plain_name);
		}

		/**
		 * Is there any restriction to add to the query?
		 *
		 * @return string
		 */
		function get_restriction() {
			$ret= "";
			if($this->is_detail) {
				$ret= $this->name . "=" . $this->reference->value_reference . $this->default_value . $this->reference->value_reference;
			}
			return $ret;
		}

		/**
		 * Field post-operations.
		 *
		 * Perhaps the field needs to do something after the operation.
		 * For example, file fields needs to move the uploaded file after inserting.
		 *
		 */


		/**
		 * Do you need to do something afer inserting?
		 *
		 * @param string $field_value , the current field value
		 */
		function field_insert($field_value) {
			$this->reference->field_insert($this->name, $field_value);
		}

		/**
		 * Do you need to do something afer updating?
		 *
		 * @param string $old_field_value , the old field value
		 * @param string $new_field_value , the new field value
		 */
		function field_update($old_field_value, $new_field_value) {
			$this->reference->field_update($this->name, $old_field_value, $new_field_value);
		}

		/**
		 * Do you need to do something afer deleting?
		 *
		 * @param string $field_value , the current field value (if any)
		 */
		function field_delete($field_value) {
			$this->reference->field_delete($this->name, $field_value);
		}

		/**
		 * Checks for the post value, concerning of the field properties: if
		 * it is unique, required and / or updatable.
		 *
		 * Will return 0, if there is any field table with error, or 1 if all
		 * is ok.
		 *
		 * @param boolean $for_insert
		 * @param dbms_class $db
		 * @param string $table_name
		 * @return integer
		 */
		function check_field_value($value, $for_insert, & $db=null, $table_name="") {

			global $MESSAGES;


			if($value!="" and !$this->check($value)) {
				html_showError(sprintf($MESSAGES["FIELD_TYPE_INCORRECT"],$this->alias) . "<br>");
				return 0;
			}

			// Check for unique fields when action='update_row'
			// If the field is defined as unique, then must check it at table..
			if($for_insert and $this->is_unique and $value !== false and $table_name!="" and $db!=null) {

				$query= "select count(*) from " . $table_name . " where " . $this->name . "=" . $this->get_field_ref() . $value . $this->get_field_ref();
				$tmp_res=$db->dbms_query($query);

				if($db->dbms_check_result($tmp_res)) {
					list($num_rows)= $db->dbms_fetch_row($tmp_res);
					$db->dbms_free_result($tmp_res);
					if($num_rows > 0) {
						html_showError(sprintf($MESSAGES["FIELD_EXISTS"],$this->alias) . "<br>");
						return 0;
					}
				}
			}

			if($this->required and $this->updatable) {

				if(!$for_insert and ( strpos($this->type, "password") !== false) ) return 1;
				if($for_insert and $this->hide_on_insert) return 1;
				if(!$for_insert and $this->hide_on_update) return 1;

				if(($value === false) or ($value == "")) {
					html_showError(sprintf($MESSAGES["FIELD_REQUIRED"],$this->alias) . "<br>");
					return 0;
				}
			}

			return 1;
		}
	}

	class master_field_ext extends field_ext {

		public $detail_URL;
		public $detail_field_name;
		public $get_value_from_field;
		public $parameters;

		public $new_window=false;

		/**
		 * master_field constructor
		 *
		 * @param string_URL $detail_URL, the URL to go when field is clicked
		 * @param field * $detail_field, If the field that contains the identifier is not itself, indicate it. If is it self, pass a null value as reference.
		 * @param string $field_name, Name of the field (in the query result)
		 * @param string $field_alias, Name shown at the field header
		 * @param string $field_type, auto,integer,bool,string,password,date,text,foreign_key,link,listbox
		 * @param bool   $required, if true, then at new, update this field will be checked to have value
		 * @param bool   $is_unique, if true, at new, update this field will be checked to have an unique value
		 * @param bool   $visible, if true it will be hidden
		 * @param bool   $updatable, if true it can be modified.
		 * @param string $default_value=null, default value for this field
		 * @param string $reference=NULL, the reference for special fields: foreign_key,link and listbox
		 * @return master_field
		 */
		function master_field_ext($detail_URL,$detail_field_name, &$get_value_from_field, $field_name, $field_alias, $field_type, $required, $is_unique, $show_order, $updatable, $default_value=null, $reference=NULL) {

			$this->detail_URL= $detail_URL;
			$this->detail_field_name= str_replace(".","_",$detail_field_name);
			$this->get_value_from_field= $get_value_from_field;
			$this->parameters= null;

			parent::field_ext($field_name, $field_alias, $field_type, $required, $is_unique, $show_order, $updatable, $default_value, $reference);
		}

		function add_parameter($parameter) {
			$this->parameters[]= $parameter;
		}

		function get_value($initial_value, $for_show=true) {

			global $is_master;

			$add= $this->new_window ? "1" : "0";

			if($for_show) {

				// Which is the field from which the value is get? current object o other?
				if($this->get_value_from_field != null) {

					$param="detail_" . $this->detail_field_name . "=" . $this->get_value_from_field->get_last_value();

					if(isset($is_master)) {
						$param.= "&is_detail=1";
					}

					if(count($this->parameters)) {
						foreach($this->parameters as $c_parameter) {
							$param.="&$c_parameter";
						}
					}

					$value="<a href=\"javascript:void(0)\" onclick='javascript:goto_detail(\"" . $this->detail_URL . "?$param\", \"\", $add);'>". parent::get_value($initial_value) . "</a>";

				} else {

					$param="detail_" . $this->detail_field_name . "=" . $initial_value;

					if(count($this->parameters)) {
						foreach($this->parameters as $c_parameter) {
							$param.="&$c_parameter";
						}
					}

					if(isset($is_master)) {
						$param .= "&is_master=1";
					}
					$value="<a href=\"javascript:void(0)\" onclick='javascript:goto_detail(\"" . $this->detail_URL . "?$param\", \"\", $add);'>". parent::get_value($initial_value) . "</a>";
				}

			} else {
				$value= parent::get_value($initial_value, $for_show);
			}

			return $value;
		}

		function get_parent_value($initial_value, $for_show=true) {
			return parent::get_value($initial_value, $for_show);
		}


		function show_hidden() {

			global $is_master;

			if(isset($is_master)) {
				echo "<input type='hidden' name='is_master' value='1'>\n";
			}

			parent::show_hidden();
		}
	}
?>