<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage forms
 *
 */

	/**
	 * Basic definition for any html form field element.
	 * Those are used from field.inc.php.
	 */
	class field_type {

		public $size=20;
		public $value_reference="'";		// How must the value be referenced in a query, p.e. user.name = 'value'
		public $default_value;
		public $default_search_value;
		public $col_modifier;
		public $readonly;					// Stores if was shown in readonly mode or not.

		protected $reserved_words=array( "<script" => "< script",
										 "</script" => "< /script",
										 "PHPSESSID" => "reserved word",
										 "javascript" => "reserved word",
										 "readCookie" => "** reserved word **");

		protected $corrected_words= array("
" => "<br>");

		protected $corrected_words_html= array();

		public function field_type($default_value="") {
			$this->default_value= $default_value;
			$this->col_modifier="style='text-align: left'";
		}

		/**
		 * Render function.
		 * Echo the html code to show the field
		 *
		 * @param string $field_name
		 * @param bool $readonly
		 */
		public function show($field_name, $readonly) {

			$this->readonly= $readonly;

			$add= $readonly ? " class='readonly' readonly='1'":"";
			$def= $this->default_value != null ? "value='$this->default_value'": "";
			echo "<input type='text' size='" . $this->size . "' name='" . $field_name . "' $add $def>\n";
		}

		/**
		 * Render the most basic structure of an html field
		 *
		 * @param string $field_name
		 */
		public function show_simply($field_name) {
			echo "<input type='text' size='" . $this->size . "' name='" . $field_name . "'>\n";
		}

		/**
		 * Once shown the page, will set the input focus to this field.
		 *
		 */
		public function set_focus($form_name, $field_name) {
?>
			<script language="JavaScript" type="text/javascript">
				if(document.forms.<?php echo $form_name; ?>.<?php echo $field_name; ?>) {
					document.forms.<?php echo $form_name; ?>.<?php echo $field_name; ?>.focus();
				}
			</script>
<?php
		}

		/**
		 * Check a value for its field type
		 *
		 * @param string $value
		 * @return bool
		 */
		public function check($value) {
			return 1;
		}

		/**
		 * Given a value, the field converts it to a format.
		 *
		 * @param string $field_value
		 * @param bool $for_show
		 * @return mixed
		 */
		public function get_value($field_value, $for_show= true) {
			$ret= $field_value;

			if($for_show) {
				foreach($this->corrected_words as $orig => $repl) {
					$ret= str_ireplace($orig,$repl,$ret);
				}
			}

			$ret= stripslashes($ret);
			return $ret;
		}

		/**
		 * Returns the real value of a field, but changing the reserved words.
		 *
		 * @param string $field_value
		 * @return mixed
		 */
		public function get_real_value($field_value) {

			return stripslashes($field_value);
		}

		public function get_select_string($field_name) {
			return $field_name;
		}

		public function get_sql_value($field_value) {
			global $global_db;

			// BUG Corrected. Only HTML field type could contain HTML tags.
			$ret= htmlentities($field_value, ENT_QUOTES);

			return $this->value_reference .  $global_db->dbms_escape_string($ret) . $this->value_reference;
		}

		public function show_hidden($field_name) {
		}

		public function get_value_from_post($field_name) {
			$ret= get_http_post_param($field_name, false);
			if(!is_array($ret)) {
				return stripslashes(get_http_post_param($field_name, false));
			} else {
				return $ret;
			}
		}

		public function get_value_from_submit($field_name) {
			return get_http_param($field_name, false);
		}

		public function set_form_default_value($form_name, $field_name, $visible=true) {

			$this->set_form_value($form_name, $field_name, $this->default_value, $visible);
		}

		public function set_form_value($form_name, $field_name, $value, $visible=true) {
			return html_set_field_value($form_name, $field_name, $value);
		}

		/**
		 * This function is used to compose a query string as " <field_name> like '%hello%' ".
		 * $value is the string which will be into the '%...%'
		 *
		 * This function is needed so herences like foreign keys can give the associated values
		 *
		 * @param unknown_type $value
		 * @return unknown
		 */
		public function get_query_string($field_name, $field_value) {
			global $global_db;

			$u_field_name= $global_db->dbms_add_upper_function($field_name);
			return $global_db->dbms_parse_search_query($u_field_name, $field_value);
		}

		/**
		 * Does the field need to change something after update, insert or delete?
		 *
		 */
		public function field_insert($field_name, $field_value) {
			return 1;
		}

		public function field_update($field_name, $old_field_value, $new_field_value) {
			return 1;
		}

		public function field_delete($field_name, $field_value) {
			return 1;
		}
	}

	class dummy extends field_type {
	}


	class general extends field_type {
		function general($default_value="") {
			parent::field_type($default_value);
		}
	}

	class string extends field_type {
		function string($default_value="") {
			parent::field_type($default_value);
		}
	}

	class long_string extends field_type {
		function long_string($default_value="") {
			parent::field_type($default_value);
			$this->size=40;
		}
	}


	class currency extends string {

		public $decimals=2;

		public function currency($default_value="") {
			parent::field_type($default_value);
			$this->col_modifier="style='text-align: right'";
		}

		function get_value($field_value, $for_show= true) {

			global $MESSAGES;

			if($for_show) {
				return number_format($field_value, $this->decimals, MON_DECIMAL_SEP, MON_THOUSAND_SEP) . " " . $MESSAGES["CURRENCY"];
//				return $field_value . " " . $MESSAGES["CURRENCY"];
			}

			return parent::get_value($field_value, $for_show);
		}

	}

	class short_string extends field_type {

		function get_value($field_value, $for_show= true) {
			$new_field_value= cut_string($field_value, 150, " [..]");
			return parent::get_value($new_field_value, $for_show);
		}
	}

	class hidden extends field_type {

		function show($field_name, $readonly) {
			$this->readonly= $readonly;
		}

		function show_hidden($field_name) {
			$def= $this->default_value != null ? "value='$this->default_value'": "";
			echo "<input type='hidden' name='" . $field_name . "' $def>\n";
		}
	}

	class integer extends field_type {
		function integer($default_value=null) {
			$this->col_modifier="style='text-align: right'";
			$this->default_value=$default_value;
			$this->value_reference="";
		}

		function check($value) {
			return is_numeric($value);
		}

		function get_sql_value($field_value) {
			global $global_db;

			if($field_value == "") {
				return "null";
			} else {
				$ret= htmlentities($field_value, ENT_QUOTES);
				return htmlspecialchars($global_db->dbms_escape_string($ret));
			}
		}
	}

	class auto extends integer {
		var $sequence_name;

		function auto($sequence_name) {
			$this->col_modifier="style='text-align: right'";
			$this->sequence_name= $sequence_name;
		}

		function get_value_from_post($field_name) {
			global $global_db;

			if(($ret= parent::get_value_from_post($field_name)) === false) {
				return $global_db->dbms_sequence($this->sequence_name);
			}
			return $ret;
		}
	}

	class email extends string {

		public function show($field_name, $readonly) {

			$this->readonly= $readonly;

			$add= $readonly ? " class='readonly' readonly='1'":"";
			$def= $this->default_value != null ? "value='$this->default_value'": "";
			echo "<input type='text' name='" . $field_name . "' $add $def size='40'>\n";
		}
		function check($value) {
			// TODO
			// Check regexp: /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
			return 1;
		}
	}

	/**
	 *
	 * Date field using jquery datepicker
	 * @author tiochan
	 *
	 */
	class date extends field_type {

		public function date($default_value="") {

			parent::field_type($default_value);

			global $GLOBAL_HEADERS;

			$GLOBAL_HEADERS["jquery_base"]='
					<link rel="stylesheet" href="' . HOME . '/include/jquery/themes/base/jquery.ui.all.css">
					<script src="' . HOME . '/include/jquery/jquery-1.6.2.js"></script>
					<script src="' . HOME . '/include/jquery/ui/jquery.ui.core.js"></script>
					<script src="' . HOME . '/include/jquery/ui/jquery.ui.widget.js"></script>
					<script src="' . HOME . '/include/jquery/ui/jquery.ui.mouse.js"></script>
					<script src="' . HOME . '/include/jquery/ui/jquery.ui.button.js"></script>
					<script src="' . HOME . '/include/jquery/ui/jquery.ui.dialog.js"></script>
					<script src="' . HOME . '/include/jquery/ui/jquery.ui.draggable.js"></script>
					<script src="' . HOME . '/include/jquery/ui/jquery.ui.position.js"></script>
					<script src="' . HOME . '/include/jquery/ui/jquery.ui.resizable.js"></script>
					';

			$GLOBAL_HEADERS["jquery_datepicker"]='
					<script src="' . HOME . '/include/jquery/ui/jquery.ui.datepicker.js"></script>';

			// set the format in which the date to be returned
			switch(DATE_FORMAT) {
				case "d-m-y":
					$this->format= "dd/mm/yy";
					break;
				case "m-d-y":
					$this->format= "mm/dd/yy";
					break;
				case "y-m-d":
					$this->format= "yy/mm/dd";
					break;
				default:
					$this->format= "dd/mm/yy";
					break;
			}

			$this->value_reference="'";
		}

		public function check($value) {
			// TODO: define check function for date data type.
			return 1;
		}

		public function get_select_string($field_name) {
			global $global_db;

			return $global_db->dbms_date_to_string($field_name);
		}

		public function get_sql_value($field_value) {
			global $global_db;

			if(trim($field_value) == "") {
				return "null";
			} else {
				//return "'" . $field_value . "'";
				return $global_db->dbms_to_date($field_value);
			}
		}

		/**
		 * This function is used to compose a query string as " <field_name> like '%hello%' ".
		 * $value is the string which will be into the '%...%'
		 *
		 * This function is needed so herences like foreign keys can give the associated values
		 *
		 * @param unknown_type $value
		 * @return unknown
		 */
		public function get_query_string($field_name, $field_value) {
			global $global_db;

			$field_name= $global_db->dbms_date_to_string($field_name);

			$u_field_name= $global_db->dbms_add_upper_function($field_name);
			return $global_db->dbms_parse_search_query($u_field_name, $field_value);
		}

		public function show_simply($field_name) {
			$this->show($field_name, false);
		}

		public function show($field_name, $readonly, $maxlength=10) {

			global $MESSAGES, $GLOBAL_HEADERS;

			$this->readonly= $readonly;

			$add= $readonly ? "class='readonly' readonly" : "";

			echo "<input type='text' $add size='" . ($maxlength + 5) . "' maxlength='$maxlength' name='$field_name' id='$field_name'>";

			if(!$readonly) {

				$def="";
				if($this->default_value != null) {
					$def= "$( \"#" . $field_name . "\" ).datepicker( 'setDate', '" . $this->default_value . "' );";
				}

				$GLOBAL_HEADERS[$field_name . "jquery"]="
					<script> $(function() {
						$( \"#" . $field_name . "\" ).datepicker( {
							dateFormat: '" . $this->format . "',
							showOn: 'button',
							buttonImage: '" . FE_DATEPICK_ICON . "',
							buttonImageOnly: true,
							changeMonth: true,
							changeYear: true
						});
						$def
						});
					</script>";
			}

			$help= isset($MESSAGES[$this->format]) ? $MESSAGES[$this->format] : $this->fomat;
			echo "<i>&nbsp;&nbsp;(" . $MESSAGES[$this->format] . ")</i>";
		}
	}

		/**
	 *
	 * Date field using jquery datepicker
	 * @author tiochan
	 *
	 */
	class short_date extends date {

		public function get_select_string($field_name) {
			global $global_db;

			return $global_db->dbms_date_to_string($field_name, "", false);
		}

		public function get_sql_value($field_value) {
			global $global_db;

			if(trim($field_value) == "") {
				return "null";
			} else {
				//return "'" . $field_value . "'";
				return $global_db->dbms_to_date($field_value, "", false);
			}
		}
	}

	class date_time extends date {

		public function get_query_string($field_name, $field_value) {

			$field_value .= "%";
			return parent::get_query_string($field_name, $field_value);
		}

		public function show($field_name, $readonly, $maxlength=10) {
			return parent::show($field_name, $readonly, 19);
		}
	}

	class time extends field_type {

		public function show($field_name, $readonly) {

			$this->readonly= $readonly;

			$add= $readonly ? "class='readonly' readonly='1'":"";
			$def= $this->default_value != null ? "value='$this->default_value'": "";
			echo "<input type='text' name='" . $field_name . "_hours' $add $def  MAXLENGTH='4' size='4'> h\n";
			echo "<input type='text' name='" . $field_name . "_minutes' $add $def MAXLENGTH='2' size='2'> m\n";
		}

		public function get_value_from_post($field_name) {

			$hours= get_http_post_param($field_name . "_hours", false);
			$minutes= get_http_post_param($field_name . "_minutes", false);

			$total_hours= $hours + ($minutes / 60);

			return $total_hours;
		}

		public function set_form_value($form_name, $field_name, $value, $visible=true) {

			$hours= round($value,0);

			$minutes= round( (($value - $hours) * 60) , 0);

			html_set_field_value($form_name, $field_name . "_hours", $hours);
			html_set_field_value($form_name, $field_name . "_minutes", $minutes);

			return true;
		}
	}

	class ftext extends field_type {

		function ftext() {
			$this->value_reference="'";
		}

		function show($field_name, $readonly) {

			$this->readonly= $readonly;

			$add= $readonly ? "class='readonly' readonly='1'":"";
			$def= $this->default_value != null ? $this->default_value: "";
			echo "<textarea name='$field_name' cols='60' rows='10' $add>$def</textarea>\n";
		}

		function get_sql_value($field_value) {
			global $global_db;


//			$ret= htmlentities($field_value, ENT_QUOTES);
			$ret=$field_value;

			return "'" . str_ireplace("< /script>","</script>",$global_db->dbms_escape_string($ret)) . "'";
		}

		public function get_real_value($field_value) {

			$ret= parent::get_real_value($field_value);
			$ret= stripcslashes($ret);

			return $ret;
		}
	}

	class short_ftext extends ftext {

		function get_value($field_value, $for_show= true) {

			global $global_db;

			$new_field_value= $field_value;
			if(strlen($field_value) > 50)
				$new_field_value= cut_string($new_field_value, 50, " [..]");

			$new_field_value= "'" . str_ireplace("< /script>","</script>",$global_db->dbms_escape_string($new_field_value)) . "'";

			return parent::get_value($new_field_value, $for_show);
		}
	}

	class short_text extends ftext {

		function show($field_name, $readonly) {

			$this->readonly= $readonly;

			$add= $readonly ? "class='readonly' readonly='1'":"";
			$def= $this->default_value != null ? $this->default_value: "";
			echo "<textarea name='$field_name' cols='60' rows='3' $add>$def</textarea>\n";
		}

		function get_value($field_value, $for_show= true) {

			$new_field_value= $field_value;
			if(strlen($field_value) > 50)
				$new_field_value= cut_string($new_field_value, 50, " [..]");

			return parent::get_value($new_field_value, $for_show);
		}
	}

	class query extends ftext {
		function get_value($field_value, $for_show=true) {
			return $field_value;
		}
	}

	class html extends ftext {

		function html() {
			$this->value_reference="'";
		}

		function show($field_name, $readonly) {

			$this->readonly= $readonly;

			if($readonly) {
				echo "<div name='$field_name' id='$field_name'></div>";

			} else {

				include_once(SYSHOME.'/include/ckeditor/ckeditor.php');

			  	$oCKeditor = new CKeditor(HOME.'/include/ckeditor/');
			  	$oCKeditor->BasePath = HOME.'/include/ckeditor/';

				$config = array();

				if($this->readonly) {
					$config["readOnly"]= true;
					$config["toolbarStartupExpanded"]= false;
					$config["toolbar"]= "Basic";
//					$config["toolbar"]= array( array( 'Source' ) );
				} else {
					$config['toolbar'] = array(
					    array( 'Source','Code','-',
					          'Cut','Copy','Paste','PasteText','PasteFromWord','-',
					          'Undo','Redo','-',
					          'Find','Replace','-',
					          'SelectAll','RemoveFormat','-',
					          'Maximize', 'ShowBlocks'),
					    '/',
					    array('Bold','Italic','Underline','Strike','-',
					          'Subscript','Superscript','-',
					          'NumberedList','BulletedList','-',
					          'Outdent','Indent','Blockquote','-',
					          'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-',
					          'Link','Unlink','Anchor','-',
					          'Image','Flash','Table','HorizontalRule','SpecialChar'
					          ),
					    '/',
					    array('Format','Font','FontSize','-',
					          'TextColor','BGColor', 'Code')
					);
				}

//				$config["extraPlugins"] = "syntaxhighlight";
//				$config["syntaxhighlighthLangDefault"]= "SQL";
				$config["width"]= "700";
				$config["height"]= "400";
//				$config['skin']= "v2";
//				$config['skin']= "office2003";
				$config['skin'] = 'kama';

				$config['uiColor']='#eeeecc';

				$oCKeditor->editor($field_name, "", $config);
			}
		}

		public function check($value) {

			foreach($this->reserved_words as $reserved_word => $replacement) {
				if(stripos($value, $reserved_word)!== false) {
					html_showWarning("Field contains reserved word: $reserved_word");
					return 0;
				}
			}

			return 1;
		}

/*		function get_value($field_value, $for_show=true) {

			return str_ireplace("</script>","< /script>",$field_value);
		}
*/

		/**
		 * Given a value, the field converts it to a format.
		 *
		 * @param string $field_value
		 * @param bool $for_show
		 * @return mixed
		 */
		public function get_value($field_value, $for_show= true) {
			$ret= $field_value;

			if($for_show) {
				foreach($this->corrected_words_html as $orig => $repl) {
					$ret= str_ireplace($orig,$repl,$ret);
				}
			}

			$ret= stripslashes($ret);
echo "<h1>GET VALUES</h1>";
echo htmlentities($ret);
			return $ret;
		}

		function get_sql_value($field_value) {
			global $global_db;

			// $ret= htmlentities($field_value, ENT_QUOTES);
			$ret= $field_value;

			// TODO
			// Notified by Crhistian King
			// How to avoid malicius code. On HTML field types the HTML tags
			// can't be removed, but anyone can insert XSS code to get user
			// info.
			// First security measure is to remove all references to scripting.
			foreach($this->reserved_words as $orig => $repl) {
				$ret= str_ireplace($orig, $repl,$ret);
			}

			$ret= "'" . str_ireplace("< /script>","</script>",$global_db->dbms_escape_string($ret)) . "'";
echo "<h1>GET SQL VALUES</h1>";
echo htmlentities($ret);
			return $ret;
		}

		public function set_form_value($form_name, $field_name, $value, $visible=true) {
echo "<h1>SET VALUE</h1>";
echo htmlentities($value);

			if($this->readonly) {
				$value= str_replace('"', "'",$value);
				$value= str_replace("\r\n", "\\\r\n",$value);
				$value= str_replace("\n", "\\\n",$value);
?>
				<script type="text/javascript">
					var content;
					content= "<?php echo $value; ?>";
					ReplaceContentInContainer("<?php echo $field_name; ?>",content);
				</script>
<?php
			} else {
				return field_type::set_form_value($form_name, $field_name, $value, $visible);
			}
		}
	}

	class short_html extends html {

		function get_value($field_value, $for_show= true) {

			$new_field_value= $field_value;
			if(strlen($field_value) > 150)
				$new_field_value= cut_string($new_field_value, 150, " [..]");

			return parent::get_value($new_field_value, $for_show);
		}
	}

	/**
	 * This class trust the contents of HTML fields and does not check values for RESERVED WORDS
	 * Be careful on this.
	 */
	class html_trusted extends html {
		function get_sql_value($field_value) {
			return $field_value;
		}

	}

	class clear_password extends field_type {

		function clear_password() {
			$this->value_reference="'";
		}

		function show($field_name, $readonly) {

			$this->readonly= $readonly;

			$add= $readonly ? "class='readonly' readonly='1'":"";
			$def= $this->default_value != null ? "value='$this->default_value'": "";
			echo "<input type='password' name='" . $field_name . "' autocomplete=off $add $def>\n";
		}
	}

	class password extends field_type {

		protected $correct= true;
		public $invalid_value= "         ";

		function password() {
			$this->value_reference="'";
		}

		function show($field_name, $readonly) {

			global $MESSAGES;

			$this->readonly= $readonly;

			$add= $readonly ? "class='readonly' readonly='1'":"";
			$def= $this->default_value != null ? "value='$this->default_value'": "";
			echo "<div align='left'><input type='password' name='" . $field_name . "' $add $def size='15'>\n";

			if(!$readonly) echo " " . $MESSAGES["CONFIRM_PASSWORD"] . ": <input type='password' name='" . $field_name . "_confirm' size='15'>\n";

			echo "</div>";
		}

		function get_sql_value($field_value) {
			return $this->value_reference . md5($field_value) . $this->value_reference;
		}

		function set_form_value($form_name, $field_name, $value, $visible=true) {
			return parent::set_form_value($form_name, $field_name, "", $visible);
		}

		public function check($value) {
			if($value == $this->invalid_value) return 0;
			return 1;
		}

		public function get_value_from_post($field_name) {
			$pass1= get_http_post_param($field_name, false);
			$pass2= get_http_post_param($field_name . "_confirm", false);

			if($pass1 != $pass2) return $this->invalid_value;
			else return $pass1;
		}
	}

	class bool extends field_type {

		function bool() {
			$this->value_reference="'";
		}

		function check($value) {
			if(($value!="1") and ($value!="0")) {
				return 0;
			}
			return 1;
		}

		function show($field_name, $readonly, $for_search=false) {

			global $MESSAGES;

			$this->readonly= $readonly;

			$def= $this->default_value != null ? "value='$this->default_value'": "";

			if($readonly) {
				echo "<input type='hidden' name='$field_name'>";
				echo "<select name='_" . $field_name . "' disabled='1' class='readonly' $def>\n";
			} else {
				echo "<select class='action' name='$field_name' $def>\n";
			}

			if($for_search) echo "  <option></option>";
			echo "	<option value='1'>" . $MESSAGES["TRUE"] . "</option>\n";
			echo "	<option value='0'>" . $MESSAGES["FALSE"] . "</option>\n";
			echo "</select>\n";
		}

		function show_simply($field_name) {
			$this->show($field_name, false, true);
		}


		function get_value($field_value, $for_show=true) {
			global $MESSAGES;

			$value= ($field_value == "1") ? $MESSAGES["TRUE"] : $MESSAGES["FALSE"];
			return $value;
		}
	}

	class boolean extends bool {
	}

	class str_bool extends bool {

		function check($value) {

			if(($value!="true") and ($value!="false")) {
				return 0;
			}
			return 1;
		}

		function show($field_name, $readonly, $for_search=false) {

			global $MESSAGES;

			$this->readonly= $readonly;

			$def= $this->default_value != null ? "value='$this->default_value'": "";

			if($readonly) {
				echo "<input type='hidden' name='$field_name'>";
				echo "<select name='_" . $field_name . "' disabled='1' class='readonly' $def>\n";
			} else {
				echo "<select class='action' name='$field_name' $def>\n";
			}

			if($for_search) echo "  <option></option>";
			echo "	<option value='true'>" . $MESSAGES["TRUE"] . "</option>\n";
			echo "	<option value='false'>" . $MESSAGES["FALSE"] . "</option>\n";
			echo "</select>\n";
		}

		function show_simply($field_name) {
			$this->show($field_name, false, true);
		}


		function get_value($field_value, $for_show=true) {
			global $MESSAGES;

			$value= ($field_value == "true") ? $MESSAGES["TRUE"] : $MESSAGES["FALSE"];
			return $value;
		}

		public function set_form_value($form_name, $field_name, $value, $visible=true) {

			$new_value= $value == "true" ? 'true':'false';

			parent::set_form_value($form_name, $field_name, $new_value, $visible);
		}
	}

	class sex extends field_type {

		function sex() {
			$this->value_reference="'";
		}

		function check($value) {
			if(($value!="1") and ($value!="0")) {
				return 0;
			}
			return 1;
		}

		function show($field_name, $readonly, $for_search=false) {

			global $MESSAGES;

			$this->readonly= $readonly;

			$def= $this->default_value != null ? "value='$this->default_value'": "";

			if($readonly) {
				echo "<input type='hidden' name='$field_name'>";
				echo "<select name='_" . $field_name . "' disabled='1' class='readonly' $def>\n";
			} else {
				echo "<select class='action' name='$field_name' $def>\n";
			}

			if($for_search) echo "  <option></option>";
			echo "	<option value='0'>" . $MESSAGES["FIELD_MALE"] . "</option>\n";
			echo "	<option value='1'>" . $MESSAGES["FIELD_FEMALE"] . "</option>\n";
			echo "</select>\n";
		}

		function show_simply($field_name) {
			$this->show($field_name, false, true);
		}


		function get_value($field_value, $for_show=true) {
			global $MESSAGES;

			$value= ($field_value == "1") ? $MESSAGES["FIELD_FEMALE"] : $MESSAGES["FIELD_MALE"];
			return $value;
		}
	}

	class target extends field_type {

		function target() {
			$this->value_reference="'";
		}

		function check($value) {
			if(($value!="OWN") and ($value!="NEW") and ($value!="DETAIL")) {
				return 0;
			}
			return 1;
		}

		function show($field_name, $readonly, $for_search=false) {

			global $MESSAGES;

			$this->readonly= $readonly;

			$def= $this->default_value != null ? "value='$this->default_value'": "";

			if($readonly) {
				echo "<input type='hidden' name='$field_name'>";
				echo "<select name='_" . $field_name . "' disabled='1' class='readonly' $def>\n";
			} else {
				echo "<select class='action' name='$field_name' $def>\n";
			}

			if($for_search) echo "  <option></option>";
			echo "	<option value=''>" . $MESSAGES["FIELD_TARGET_NOTHING"] . "</option>\n";
			echo "	<option value='OWN'>" . $MESSAGES["FIELD_TARGET_OWN"] . "</option>\n";
			echo "	<option value='NEW'>" . $MESSAGES["FIELD_TARGET_NEW"] . "</option>\n";
			echo "	<option value='DETAIL'>" . $MESSAGES["FIELD_TARGET_DETAIL"] . "</option>\n";
			echo "</select>\n";
		}

		function show_simply($field_name) {
			$this->show($field_name, false, true);
		}


		function get_value($field_value, $for_show=true) {
			global $MESSAGES;

			if ($field_value =="OWN")  $value=$MESSAGES["FIELD_TARGET_OWN"];
			elseif ($field_value =="NEW")  $value=$MESSAGES["FIELD_TARGET_NEW"];
			elseif ($field_value =="DETAIL")  $value=$MESSAGES["FIELD_TARGET_DETAIL"];

			return $value;
		}
	}
?>
