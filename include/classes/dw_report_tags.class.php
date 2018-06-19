<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage report
 *
 * Datawindow class for parameters management.
 */

/*
	Table definition

	+-------------+--------------+------+-----+---------+----------------+
	| Field       | Type         | Null | Key | Default | Extra          |
	+-------------+--------------+------+-----+---------+----------------+
	| id_tag      | mediumint(9) | NO   | PRI | NULL    | auto_increment |
	| tag_name    | varchar(60)  | NO   | UNI |         |                |
	| calc_method | varchar(15)  | YES  |     | NULL    |                |
	| description | varchar(255) | YES  |     | NULL    |                |
	| value       | text         | YES  |     | NULL    |                |
	+-------------+--------------+------+-----+---------+----------------+


*/

	include_once "../include/init.inc.php";
	include_once INC_DIR . "/forms/field_types/listbox.inc.php";
	include_once INC_DIR . "/forms/field_ext.inc.php";
	include_once INC_DIR . "/forms/form_elements/datawindow_ext.inc.php";
	include_once INC_DIR . "/forms/form_elements/search_box_ext.inc.php";


	class dw_report_tags extends datawindow_ext {

		public $untouchables= array();
		/*
			"CONS_DATE_FORMAT",
			"CONS_DATE_FORMAT_PHP",
			"CONS_DATE_FORMAT_SQL",
			"CONS_DATE_TIME_FORMAT",
			"CONS_DATE_TIME_FORMAT_PHP",
			"CONS_DATE_TIME_FORMAT_SQL",
			"OP_DATE_LAST_WEEK",
			"OP_DATE_TODAY",
			"OP_DATE_TODAY_MONTH",
			"OP_DATE_TODAY_YEAR",
			"OP_DAY_OF_WEEK",
			"OP_LAST_MONDAY",
			"OP_LAST_SATURDAY",
			"OP_LAST_SUNDAY",
			"USER_GROUP",
			"USER_GROUP_NAME",
			"USER_ID",
			"USER_LEVEL",
			"USER_LEVEL_NAME",
			"USER_NAME",
			"USER_REAL_NAME");
			*/

		private $current_tag_type="";

		public function dw_report_tags(&$optional_db=null) {

			global $MESSAGES, $USER_LEVEL;

			$null_ref=null;

			$is_admin= $USER_LEVEL == 0;

			// Datawindow Query
			$qry= new datawindow_query();


			$public_options= new listbox();

			$public_options->lb[0]= $MESSAGES["IS_NOT_PUBLIC"];
			$public_options->lb[1]= $MESSAGES["IS_PUBLIC_FOR_ANYBODY"];
			$public_options->lb[2]= $MESSAGES["IS_PUBLIC_FOR_MY_GROUP"];

			// Fields
			$fields= Array();


			$list_connectors= new list_dir("");
			$list_connectors->lb["APP_GENERIC_CONN"]="Generic";
			$list_connectors->add_dir("/include/reports/conn",".class.php");
			$list_connectors->add_dir("/my_include/reports/conn",".class.php");

			$fields[0]= new field_ext("report_tags.id_tag","","auto",false,true,0,false);
//			$fields[]= new field_ext("report_tags.tag_name",$MESSAGES["TAGS_FIELD_NAME"],"string",true,true,1,true);
			$fields[1]= new master_field_ext(HOME . "/tools/report_tag_preview.php","tag_id",$fields[0],"report_tags.tag_name",$MESSAGES["TAGS_FIELD_NAME"],"long_string",true,true,1,true);
			$fields[1]->add_parameter("show_header=true");

			$fields[2]= new field_ext("report_tags.calc_method",$MESSAGES["TAGS_FIELD_CALC_METHOD"],"tags_types_list",false,false,2,true);
			$fields[3]= new field_ext("report_tags.value",$MESSAGES["TAGS_FIELD_VALUE"],"text_tag",true,false,3,true);
			$fields[4]= new field_ext("report_tags.description",$MESSAGES["TAGS_FIELD_DESCRIPTION"],"short_text", false,false,4,true);
			$fields[5]= new field_ext("report_tags.extrainfo",$MESSAGES["TAGS_FIELD_EXTRAINFO"],"text",false,false,5,true);
			$fields[6]= new field_ext("report_tags.connection","SQL Connect","list_dir",false,false,5,true,null,$list_connectors);
			$fields[7]= new field_ext("report_tags.is_public",$MESSAGES["TAGS_FIELD_IS_PUBLIC"],"listbox", false,false,6,true,1,$public_options);
			$fields[8]= new field_ext("report_tags.id_user","","integer", false,false,0,true);
			$fields[9]= new field_ext("report_tags.id_group","","integer", false,false,0,true);
			$fields[10]= new field_ext("report_tags.is_protected","System tag?","boolean", false,false,$is_admin ? 10 : 0, true, 0);

			$fields[1]->new_window=true;
			$fields[3]->visible=false;
			$fields[5]->visible=false;
			$fields[8]->hide_on_insert=true;
			$fields[8]->hide_on_update=true;
			$fields[9]->hide_on_insert=true;
			$fields[9]->hide_on_update=true;
			$fields[10]->hide_on_insert=!$is_admin;
			$fields[10]->hide_on_update=!$is_admin;


			// Creation of table and add it to query
			$can_manage= ($USER_LEVEL == 0);
			$table_tags= new datawindow_table("report_tags", $fields, 0, $can_manage, $can_manage, $can_manage);
			$qry->add_table($table_tags);

			$search_fields= array($fields[1],$fields[2],$fields[3], $fields[4], $fields[6]);
			if($is_admin) $search_fields[]= $fields[10];
			$sb= new search_box_ext($search_fields,"tags_search",$MESSAGES["SEARCH"], 1, false);

			// Set the order by
			$qry->add_order_by("tag_name");

			$qry->add_order_by_field($table_tags,1);
			$qry->add_order_by_field($table_tags,2);

			parent::datawindow_ext($qry);
			$this->add_search_box($sb);
			$this->allow_save_and_continue= true;
			$this->max_lines_per_page=100;

			// Multiple row actions
			if($can_manage) {
				$this->row_selection_enabled= true;
				$this->add_group_action($MESSAGES["DELETE"],"group_delete");
				if($USER_LEVEL == 0) $this->add_group_action("Set system tag (protect)","group_set_system_tag");
				if($USER_LEVEL == 0) $this->add_group_action("Unset system tag (free)","group_unset_system_tag");
			}


			if($can_manage) {
				$this->add_global_action("add new tag","","","show_create_tags");
			}
		}

		function show_create_tags($text,$img,$action) {

			global $MESSAGES;

			$aux= new tags_types_list();

			$ret="";
			$form_ref= "document.forms." . $this->form_name;
			$row_id_ref= $form_ref . ".row_id_" . $this->doc_name;
			$action_ref= $form_ref . ".dw_action_" . $this->doc_name;


			$ret.="&nbsp;<select class='action' name='add_new_tag" . $this->doc_name . "'>\n";

			foreach($aux->lb_simple as $type => $description) {
				$ret.="<option value='$type'>$description</option>\n";
			}

			$ret.="</select>";

			echo $ret;
		}

		/**
		 * Show a create form for this table definition
		 *
		 * @param ["insert_row" | "update_row"] $action
		 * @return null
		 */

		function & show_insert_fields($field_list, $for_update=false) {

			$ret=0;

			if($for_update) {
				if($this->current_tag_type=="") {
					html_showError("Error, tag type not set.");
					return $ret;
				}
				$tag_type= $this->current_tag_type;
			} else {
				$tag_type= get_http_post_param("add_new_tag" . $this->doc_name);
				if($tag_type=="") {
					html_showError("Tag type must be set");
					$this->show_return_form();
					return $ret;
				}
			}

			echo "<input type='hidden' name='add_new_tag" . $this->doc_name . "' value='$tag_type'>";

			$class= $this->load_tag_class($tag_type);

			$tag_name= substr($class,strpos($class, "_") + 1);
			echo "<h2 class='title'>tag::$tag_name</h2>";

			// For PHP 5.3.0
			//eval("$class::show_insert_form(\$field_list, \$for_update);");

			// For earlier versions:
			$object= new $class("");
			$first_field= $object->show_insert_form($field_list, $for_update);
			$this->current_tag_type=$tag_type;

			return $first_field;
		}

		protected function show_update_form($row_id) {
			global $MESSAGES;

			if($row_id === false) {
				if(DEBUG) html_showError("Datawindow::ERROR: Identifier not defined");
				else html_showError("DW[9]: Construction error. Contact administrator");
				return 0;
			}

			$query= $this->datawindow_query->get_row_query($row_id);
			if(!$res=$this->db->dbms_query($query)) {
				if(DEBUG) html_showError("Datawindow::ERROR: " . $MESSAGES["QUERY_ERROR"] . ": " . $this->db->dbms_error());
				else html_showError("DW[10]: Construction error. Contact administrator");
				return 0;
			}

			$numRows= $this->db->dbms_num_rows($res);

			if(!$numRows) {
				html_showInfo($MESSAGES["NO_ROWS_FOR_ID"]);
				$this->db->dbms_free_result($res);
				return 0;
			}

			$row= $this->db->dbms_fetch_array($res);
			$type= $row["report_tags.calc_method"];

			$this->db->dbms_data_seek($res, 0);

			// If there is any result to be shown... show it!
			$this->current_tag_type= $type;
			$this->show_insert_form("update_row");
			$this->datawindow_query->recover_values_from_query($res);

			// Shadow POST DATA protection
			$shadow_id= $this->shadow->get_shadow_id($row_id);
			$this->shadow->add_shadowed_id($row_id, $shadow_id, "update_row");
			html_set_field_value($this->form_name,"row_id_" . $this->doc_name, $shadow_id, false);

			$this->db->dbms_free_result($res);

			return 1;
		}


		function pre_show_row(&$values, &$can_update, &$can_delete) {

			// Check here for parameters that can't be deleted
			if(in_array($values["report_tags.tag_name"],$this->untouchables) or $values["report_tags.is_protected"] == "1") {
				$can_update=false;
				$can_delete=false;
			}

			return true;
		}

		function post_show_row($values) {
			global $MESSAGES;

			$msg= sprintf($MESSAGES["TAGS_QUESTION_DUPLICATE"], $values["report_tags.calc_method"] . " tag " . $values["report_tags.tag_name"]);
			echo "<table border=0><td><td>";
			echo $this->create_row_action_with_confirmation("Copy","copy_tag",$values["row_id"],ICONS . "/kdoc.png",$msg);
			echo "</td><td>";
			echo $this->create_row_redirection("Preview",HOME . "/tools/report_tag_preview.php?detail_tag_id=" . $values["report_tags.id_tag"],ICONS . "/file.png");
			echo "</td></tr></table>";
		}

		function action_group_delete($rows) {

			foreach($rows as $row_id) {
				$this->action_start_delete($row_id);
			}
		}

		function action_group_set_system_tag($rows) {

			global $USER_LEVEL;
			global $global_db, $MESSAGES;

			if($USER_LEVEL != 0) return;

			$ok=true;
			foreach($rows as $row_id) {
				$query="update report_tags set is_protected = 1 where id_tag='$row_id'";
				$ok= $ok and $global_db->dbms_query($query);
			}

			if($ok) html_showSuccess($MESSAGES["MGM_MODIFIED"]);
			else html_showError($MESSAGES["MGM_ERROR_MODIFYING"]);
		}

		function action_group_unset_system_tag($rows) {

			global $USER_LEVEL;
			global $global_db, $MESSAGES;

			if($USER_LEVEL != 0) return;

			$ok=true;
			foreach($rows as $row_id) {
				$query="update report_tags set is_protected=0 where id_tag='$row_id'";
				$ok= $ok and $global_db->dbms_query($query);
			}

			if($ok) html_showSuccess($MESSAGES["MGM_MODIFIED"]);
			else html_showError($MESSAGES["MGM_ERROR_MODIFYING"]);
		}


		function pre_delete($row_id, $values) {

			global $MESSAGES;

			// Check here for parameters that can't be deleted
			if(in_array($values["report_tags.tag_name"],$this->untouchables)) {
				html_showError($MESSAGES["PARAMETER_CANT_BE_DELETED"]);
				return 0;
			}

			$list_report= $this->is_tag_in_use($values["report_tags.tag_name"]);
			if($list_report != "") {

				html_showError($MESSAGES["TAGS_CANT_BE_DELETE"] . $list_report);
				return 0;
			}

			return 1;
		}

		function pre_insert(& $values) {

			global $USER_ID, $USER_GROUP;

			$tag_type= get_http_post_param("add_new_tag" . $this->doc_name);
			$values["report_tags.calc_method"]= $tag_type;

			$values["report_tags.id_user"]= $USER_ID;
			$values["report_tags.id_group"]= $USER_GROUP;


			return $this->check_tag($values["report_tags.tag_name"], $values["report_tags.calc_method"],$values["report_tags.value"]);
		}

		function pre_update($row_id, $old_values, &$new_values) {

			global $MESSAGES;
			global $USER_ID, $USER_GROUP;

			$new_values["report_tags.id_user"]= $old_values["report_tags.id_user"];
			$new_values["report_tags.id_group"]= $old_values["report_tags.id_group"];

			$new_values["report_tags.calc_method"]= $old_values["report_tags.calc_method"];

			if($old_values["report_tags.tag_name"] != $new_values["report_tags.tag_name"]) {

				$list_report= $this->is_tag_in_use($old_values["report_tags.tag_name"]);
				if($list_report != "") {
					html_showError($MESSAGES["TAGS_CANT_BE_RENAMED"] . $list_report);
					return 0;
				}
			}

			return $this->check_tag($new_values["report_tags.calc_method"], $new_values["report_tags.calc_method"],$new_values["report_tags.value"]);
		}

		/**
		 * Check if is defined a tag_$tag_type.class.php file for the specified
		 * $tag_type. If there is not any will use tag_element as default.
		 *
		 * Execute an include_once for class and return the class name to inherit.
		 *
		 * @param string $tag_type
		 * @return string
		 */
		private function load_tag_class($tag_type) {
			// LOAD TAG CLASS
			$class= "tag_" . $tag_type;
			$class_file= INC_DIR . "/reports/tags/" . $class . ".class.php";
			if(file_exists($class_file)) {
				include_once $class_file;
			} else {
				$class_file= MY_INC_DIR . "/reports/tags/" . $class . ".class.php";
				if(file_exists($class_file)) {
					include_once $class_file;
				} else {
					html_showWarning("Tag class $class not found, using default 'tag_element'.");
					$class= "tag_element";
					$class_file= INC_DIR . "/reports/tags/" . $class . ".class.php";
					include_once $class_file;
				}
			}

			return $class;
		}

		/**
		 * Check if there is any report that is using the TAG
		 *
		 * @param unknown_type $tag_name
		 */
		private function is_tag_in_use($tag_name) {

			global $MESSAGES;

			$list_report="";

			$query="select report_name as report from reports where content like '%{" . $tag_name . "}%'";
			$res=$this->db->dbms_query($query);

			if($this->db->dbms_check_result($res)) {
				while ($row = $this->db->dbms_fetch_array($res)) {
					$list_report .= "<li> [REPORT] " . $row[0] . "</li>";
				}

				$this->db->dbms_free_result($res);
			}

			$query="select tag_name from report_tags where value like '%{" . $tag_name . "}%'";
			$res=$this->db->dbms_query($query);

			if($this->db->dbms_check_result($res)) {
				while ($row = $this->db->dbms_fetch_array($res)) {
					$list_report .= "<li> [TAG - " . $MESSAGES["TAGS_FIELD_VALUE"] . "] " . $row[0] . "</li>";
				}

				$this->db->dbms_free_result($res);
			}

			$query="select tag_name from report_tags where extrainfo like '%{" . $tag_name . "}%'";
			$res=$this->db->dbms_query($query);

			if($this->db->dbms_check_result($res)) {
				while ($row = $this->db->dbms_fetch_array($res)) {
					$list_report .= "<li> [TAG - " . $MESSAGES["TAGS_FIELD_EXTRAINFO"] . "] " . $row[0] . "</li>";
				}

				$this->db->dbms_free_result($res);
			}


			if($list_report != "") $list_report= "<ul>$list_report</ul>";

			return $list_report;
		}

		private function check_tag($tag_name, $tag_type, $value) {

			global $MESSAGES, $USER_ID;

			// Check for self reference to avoid loops
			if(strpos($value, '{' . $tag_name . '}') !== false) {
				html_showError($MESSAGES["TAGS_CANT_REFERENCE_ITSELF"]);
				return 0;
			}

			// Call for tag own check method
			$class= $this->load_tag_class($tag_type);

			$tag_name= substr($class,strpos($class, "_") + 1);
			$object= new $class("");

			$ret= $object->check_value($value);

			return $ret;
		}


		public function action_copy_tag($row_id) {

			$query= "insert into report_tags (tag_name, calc_method, description, value, extrainfo, connection, is_public) (" .
					"select " . $this->db->dbms_concat("_", "tag_name", "2")  . ", calc_method, description, value, extrainfo, connection, is_public from report_tags where id_tag='$row_id' ".
					")";
			if(!$this->db->dbms_query($query)) html_showError($this->db->dbms_error());
			else html_showSuccess("Duplicated");
		}
	}

	class tags_types_list extends listbox {

		public $lb_simple;
		public $lb_extended;


		public function tags_types_list() {

			$tag_type=null;
			$xtag_type=null;

			parent::listbox();

			$files= read_dir(INC_DIR . "/reports/tags/");

			foreach($files as $file) {
				$pos= strpos($file, "tag_");
				if(($pos !== false) and ($file != "tag_element.class.php")) {
					$tag_type= substr($file, $pos + 4);
					$pos= strpos($tag_type, ".");
					$tag_type= substr($tag_type, 0, $pos);
					$xtag_type= str_replace("_"," ", $tag_type);
				}
				$this->lb_simple[$tag_type]=$xtag_type;
				$this->lb_extended[$tag_type]= $xtag_type;
			}

			if(file_exists(MY_INC_DIR . "/reports/tags")) {
				$files= read_dir(MY_INC_DIR . "/reports/tags/");

				foreach($files as $file) {
					$pos= strpos($file, "tag_");
					if(($pos !== false) and ($file != "tag_element.class.php")) {
						$tag_type= substr($file, $pos + 4);
						$pos= strpos($tag_type, ".");
						$tag_type= substr($tag_type, 0, $pos);
						$xtag_type= str_replace("_"," ",$tag_type);
					}
					$this->lb_simple[$tag_type]=$xtag_type;
					$this->lb_extended[$tag_type]= $xtag_type;
				}
			}

			asort($this->lb_simple);

			foreach($this->lb_extended as $key => $value) {
				switch($key) {
					case "constant":
						$description= "will be used the value set on the value field";
						break;
					case "query":
						$description= "Show results of a query";
						break;
					case "graph":
						$description= "Graphs defined on the [include | my_include]/report/graphs directories";
						break;
					case "image":
						$description= "Set the absolute URL to the image file";
						break;
					case "system_var":
						$description= "Return the value of a variable from the application (wich must exists)";
						break;
					case "operation":
						$description= "Expressions with one or more number and/or TAGs";
						break;
					case "webservice":
						$description= "Any web service defined on plugins/ws directory";
						break;
					case "generic_graph_bar":
						$description= "Will plot the results of a query into a bar graph";
						break;
					case "generic_graph_bar_90":
						$description= "Will plot the results of a query into a bar graph (rotated)";
						break;
					case "generic_graph_bar_double":
						$description= "Will plot the results of a query into a bar graph with two different scales";
						break;
					case "generic_graph_bar_double_90":
						$description= "Will plot the results of a query into a bar graph with two different scales (rotated)";
						break;
					case "generic_graph_linear":
						$description= "Will plot the results of a query into a line graph";
						break;
					case "generic_graph_pie":
						$description= "Will plot the results of a query into a pie graph";
						break;
					case "generic_graph_pie_perc":
						$description= "Will plot the results of a query into a pie graph using percent values";
						break;
					case "generic_graph_radar":
						$description= "Will plot the results of a query into a radar graph";
						break;
					case "php_code":
						$description= "A tag containing PHP code which is executed";
						break;
					case "system_command":
						$description= "Execute any program from system directory";
						break;
					case "script":
						$description= "An application PHP script on the [include | my_include]/report/scripts directories";
						break;
					default:
						$description= "User defined";
				}

				$this->lb_extended[$key]= str_pad($value,30, ".") . $description;
			}

			asort($this->lb_extended);

			$this->lb= $this->lb_simple;
			$this->col_modifier= "class='courier'";
		}

		function show($field_name, $readonly, $for_search=false) {

			$this->lb= $readonly ? $this->lb_simple : $this->lb_extended;
			parent::show($field_name, $readonly, $for_search);
		}

		function get_value($field_value, $for_show= true) {

			$this->lb= $for_show ? $this->lb_simple : $this->lb_extended;
			return parent::get_value($field_value, $for_show);
		}
	}


	class text_tag extends short_ftext {

		protected $list_tags;

		public function text_tag() {

			$this->list_tags= new list_tags();

			parent::short_ftext();
		}
/*
		public function show($field_name, $readonly) {

			if(!$readonly) $this->list_tags->show($field_name,false);
			parent::show($field_name, $readonly);
		}
*/
		public function show($field_name, $readonly) {

			if(!$readonly) $this->list_tags->show($field_name,false);

			$this->readonly= $readonly;

			$add= $readonly ? "class='readonly' readonly='1'":"";
			$def= $this->default_value != null ? $this->default_value: "";
			echo "<textarea class='mini_courier' rows=25 cols=100 name='$field_name' $add>$def</textarea>\n";
		}
	}


	class list_tags extends listbox {

		public $object_referenced;
		protected $rand_name;

		function list_tags() {
			parent::listbox();
		}

		function show($referenced_field_name, $readonly, $for_search=false) {

			$this->object_referenced= $referenced_field_name;
			$this->rand_name="lb_tags_" . rand(1000000,getrandmax());

			$this->get_list_values();

			$def_value= $this->default_value != null ? $this->default_value: "";

			if($readonly) {
				echo "<input type='hidden' name='". $this->rand_name . "' >";
				echo "<select class='action' name='_" . $this->rand_name . "' disabled='1'>\n";
			} else {
				echo "<select class='courier' name='" . $this->rand_name . "'
					onchange='javascript:if(" . $this->rand_name . ".selectedIndex > 1) {
						var val = \"{\" + " . $this->rand_name . ".value  + \"}\";
						$this->object_referenced.value= $this->object_referenced.value + val;
//						$this->object_referenced.value= val;
					}'>\n";
			}

			foreach ($this->lb as $key => $value) {

				if($key == $def_value) {
					echo "<option value='$key' selected>$value</option>\n";
				} else {
					echo "<option value='$key'>$value</option>\n";
				}
			}
			echo "</select>";
		}

		function get_value($field_value, $for_show = true) {

			return "";
		}

		protected function get_list_values() {

			global $global_db;

			$query="select tag_name, calc_method, " . $global_db->dbms_left("description",50) . " from report_tags order by tag_name";
			$res= $global_db->dbms_query($query);

			$tags= array();
			$max_len_tag=$max_len_type=$max_len_desc=0;

			if($global_db->dbms_check_result($res)) {

				while(list($tag, $type, $description)= $global_db->dbms_fetch_row($res)) {
					$max_len_tag= max($max_len_tag, strlen($tag));
					$max_len_type= max($max_len_tag, strlen($type));
					$max_len_desc= max($max_len_tag, strlen($description));
					$tags[]= array($tag, $type, $description);
				}
				$global_db->dbms_free_result($res);
			}

			$new_tags= array();
			foreach($tags as $aux_tag) {
				$tag= $aux_tag[0];
				$type= $aux_tag[1];
				$description= $aux_tag[2];
				$new_tags[$tag]= str_pad($tag,$max_len_tag,".") . " | " . str_pad($type, $max_len_type, ".") . " | $description";
			}

			asort($new_tags);

			$this->lb[0]= str_pad("-TAG NAME",$max_len_tag, ".") . " | " . str_pad("TYPE", $max_len_type, ".") . " | DESCRIPTION";
			$this->lb[1]= str_pad("-",$max_len_tag, "-") . "-|-" . str_pad("-", $max_len_type, "-") . "-+-" . str_pad("-", $max_len_desc, "-");
			$this->lb= array_merge($this->lb, $new_tags);
		}
	}


	class list_tags_fck extends list_tags {

		function show($referenced_field_name, $readonly, $for_search=false) {

			$this->object_referenced= $referenced_field_name;
			$this->rand_name="lb_tags_" . rand(1000000,getrandmax());

			$this->get_list_values();

			$def_value= $this->default_value != null ? $this->default_value: "";

?>
			<div style='display: table-row;'>
<?php
			if($readonly) {
?>
				<input type='hidden' name='<?php echo $this->rand_name; ?>'>
				<select class='action' name='_<?php echo $this->rand_name; ?>' disabled='1'>
<?php
			} else {
?>
 				<select class='courier' name='<?php echo $this->rand_name; ?>'
					onchange='javascript:if(<?php echo $this->rand_name; ?>.selectedIndex > 1) {
						var val = "{" + <?php echo $this->rand_name; ?>.value  + "}";
						ckInsertHTML("<?php echo $this->object_referenced; ?>", val);
					}'>
<?php
			}

			foreach ($this->lb as $key => $value) {

				if($key == $def_value) {
					echo "<option value='$key' selected>$value</option>\n";
				} else {
					echo "<option value='$key'>$value</option>\n";
				}
			}

?>
				</select>
			</div>
<?php
		}
	}
?>
