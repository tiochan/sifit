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
	include_once SYSHOME . "/include/html.inc.php";

	global $GLOBAL_SCRIPTS;


	$my_function="".
"	function goto_detail(URL, param, new_window) {
";
	if(isset($GLOBALS["is_master"]) and ($GLOBALS["is_master"] == "1")) {
		$my_function.= "parent.detail_frame.location.href= URL + \"?\" + param;";
	} else {
		$my_function.="
		if(new_window) {
			openWin(URL);
		} else {
			openURL(URL);
		}
";
	}
	$my_function.= "	}";


	$GLOBAL_SCRIPTS["fields_goto_detail"]= $my_function;


	class field {
		public $name;
		public $alias;
		public $html_reference;

		public $type;
		public $required=false;			// Is this field required?
		public $is_unique=false;		// Must its value be unique?
		public $visible=true;			// Must this field be shown?
		public $hide_on_update=false;	// Must this field be shown on update?
		public $reference;				// Reference to the element (optional)
		public $updatable=false;
		public $default_value;

		public $is_detail;				// Is this field referenced from other form?
		public $last_value;				// Last value returned on las get_value call

		public $col_modifier;

		public $show_max_len;			// If != 0, then will be wrapped to len (for show)

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
		 * @return field
		 */

		function field($field_name, $field_alias, $field_type, $required, $is_unique, $visible, $updatable, $default_value=null, $reference=NULL) {

			if($field_type == "text") $field_type="ftext";

			$this->name=$field_name;
			$this->alias=$field_alias;
			$this->html_reference= str_replace(".","_",$field_name);
			$this->type=$field_type;
			$this->required=$required;
			$this->is_unique=$is_unique;
			$this->visible=$visible;
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

			// Now check for a master form references to this field
			if(strpos($field_name,".")) {
				$field_name=substr($field_name, strpos($field_name,".") + 1);
			}

			// Patch 0x1093
			// Avoid SQL Injection from detail references.
			// Is referenced from master?
			// Only numeric references are allowed.
			$master_value= get_http_param("detail_" . $field_name);
			if(($master_value != null) and is_numeric($master_value)) {
				$this->is_detail=true;
				$this->default_value=$master_value;
			}

			$this->col_modifier= $this->reference->col_modifier;

			$this->value= $this->get_value_from_post();
			if(!$this->value) $this->value= $default_value;
		}


		public function set_default_value($default_value) {

			$this->default_value=$default_value;
			$this->reference->default_value=$default_value;
			if(!$this->value) $this->value= $default_value;
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
		function will_be_displayed($for_update) {

			if($for_update) {
				if($this->type== "auto") return false;
				if($this->type== "hidden") return false;
				if($this->hide_on_update) return false;
				if(!$this->updatable and !$this->visible) return false;

				return true;
			} else {
				return $this->visible;
			}
		}

		/**
		 * Show the field.
		 * The field name must be set again, so objects can change the field name
		 *
		 * @param unknown_type $field_name
		 * @param unknown_type $for_update
		 */
		function show($field_name,$for_update) {

			if($this->is_detail) {
				$for_update=true;
			}

			$field_name= str_replace(".","_",$field_name);
			if($this->will_be_displayed($for_update)) {
				$this->reference->show($field_name, $for_update and !$this->updatable);
			}
		}

		/**
		 * Render the most basic structure of an html field
		 *
		 * @param string $field_name
		 */
		function force_show($field_name) {
			$field_name= str_replace(".","_",$field_name);
			$this->reference->show_simply($field_name);
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
			return $this->reference->get_value_from_post($this->html_reference);
		}

		/**
		 * Using its field name, get the value from last submit (POST and GET)
		 *
		 * @return mixed
		 */
		function get_value_from_submit() {
			return $this->reference->get_value_from_submit($this->html_reference);
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
				return $this->reference->set_form_value($form_name, $this->html_reference, $value,$this->updatable);
			} else {
				return 0;
			}
		}

		function set_form_default_value($form_name) {
			return $this->reference->set_form_default_value($form_name, $this->html_reference,$this->updatable);
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
		 * @param string $field_name
		 */
		function show_hidden($field_name) {

			$field_name= str_replace(".","_",$field_name);

			if($this->is_detail) {
				$var_name= $this->name;
				if(strpos($var_name,".")) {
					$var_name=substr($var_name, strpos($var_name,"."));
				}

				//$var_name= "detail_" . str_replace(".","_",$this->name);
				echo "<input type='hidden' name='detail_" . $var_name . "' value='" . $this->default_value . "'>\n";
				if(isset($GLOBALS["is_detail"])) {
					echo "<input type='hidden' name='is_detail' value='1'>\n";
				}
			}

			$this->reference->show_hidden($field_name);
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

			// TODO: Check for unique fields when action='update_row'
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

				if(($value === false) or ($value == "")) {
					html_showError(sprintf($MESSAGES["FIELD_REQUIRED"],$this->alias) . "<br>");
					return 0;
				}
			}

			return 1;
		}
	}


	class master_field extends field {

		public $detail_URL;
		public $detail_field;
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
		function master_field($detail_URL,&$detail_field, $field_name, $field_alias, $field_type, $required, $is_unique, $visible, $updatable, $default_value=null, $reference=NULL) {

			$this->detail_URL= $detail_URL;
			$this->detail_field= $detail_field;
			$this->parameters= null;

			parent::field($field_name, $field_alias, $field_type, $required, $is_unique, $visible, $updatable, $default_value, $reference);
		}

		function set_detail_field(&$detail_field) {
			$this->detail_field= &$detail_field;
		}

		function add_parameter($parameter) {
			$this->parameters[]= $parameter;
		}

		function get_value($initial_value, $for_show=true) {
			
			$add= $this->new_window ? "1" : "0";

			if($for_show) {

				if($this->detail_field != null) {

					$var_name= $this->detail_field->name;
					if(strpos($var_name,".")) {
						$var_name=substr($var_name, strpos($var_name,".") + 1);
					}

					$param="detail_" . $var_name . "=" . $this->detail_field->get_last_value();
					//$param="detail_" . $var_name . "=" . $this->detail_field->get_value($initial_value);
					if(isset($GLOBALS["is_master"])) {
						$param.= "&is_detail=1";
					}
					if(count($this->parameters)) {
						foreach($this->parameters as $c_parameter) {
							$param.="&$c_parameter";
						}
					}
					$value="<a href=\"javascript:void(0)\" onclick='javascript:goto_detail(\"" . $this->detail_URL . "?$param\", \"\", $add);'>". parent::get_value($initial_value) . "</a>";

				} else {

					$var_name= $this->name;
					if(strpos($var_name,".")) {
						$var_name=substr($var_name, strpos($var_name,".") + 1);
					}

					$param="detail_" . $var_name . "=" . $initial_value;
					if(count($this->parameters)) {
						foreach($this->parameters as $c_parameter) {
							$param.="&$c_parameter";
						}
					}

					if(isset($this->is_master)) {
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


		function show_hidden($field_name="") {
			if(isset($this->is_master)) {
				echo "<input type='hidden' name='is_master' value='1'>\n";
			}
		}
	}
