<?php
/**
 * @authors Jorge Novoa, Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage reports
 *
 * Tags definition class
 *
 */

/**
 * Set of regular expressions being tested:
 http://www.php.net/manual/es/reference.pcre.pattern.modifiers.php
 http://www.igdonline.com/blog/rapido-y-completo-expresiones-regulares-en-php/
 
 For TAGs:
 define("TAG_REGEXP", '#\{([a-z0-9\,\.\-\_\|\=\;\<\>\'\"\@\#\%\&\(\)\$\/ ]*?)}#is');
 define("TAG_REGEXP", '#\{([a-z0-9\,\.\-\_\|\=\;\<\>\'\"\@\#\%\&\(\)\$\/ ][^\{\}]*?)}#is
 define("TAG_REGEXP", '#\{(.[^\{\}]*)}#is');
 define("TAG_REGEXP", '#\{([^\$\s].[^\{\}]*)}#is');

 For parameters/Vars:
 define("VAR_REGEXP", '#\[\$([a-z0-9\.\-_|=;\$ ]*?)]#is');
 define("VAR_REGEXP1", '#\[\$(.?[^{]*[^}])]#is');
 define("VAR_REGEXP2", '#\{\$(.?[^{]*[^}])}#is');
*/

define("TAG_REGEXP",  '#\{([^\$\s].[^\{\}]*)}#is');
define("VAR_REGEXP1", '#\[\$([^\s].[^\{\}]*)\]#is');
define("VAR_REGEXP2", '#\{\$([^\s].[^\{\}]*)\}#is');


	class tag {

		public $id_tag;
		public $tag_name;
		public $calc_method;
		public $description;
		public $value;
		public $extrainfo;
		public $connection;
		public $is_public;
		public $parameters;

		protected $db;

		protected $is_ok;
		protected $tag_ref;

		protected $tag_calls;


		public function tag($tag_name, $tag_calls=null) {

			global $global_db;
			global $USER_ID;


			$this->tag_calls= $tag_calls ? $tag_calls : array();
			$this->tag_calls= $tag_calls;
			$this->tag_calls[]=$tag_name;

			$this->parameters= array();

			if(!isset($USER_ID)) die("User id is required but is not defined at tag declaration");

			$this->is_ok= false;

			$tag_parts=explode("|",$tag_name);
			$tag_name= $tag_parts[0];


			// Get tag definition
			$query="SELECT id_tag, tag_name, calc_method, description, value, extrainfo, connection, is_public FROM report_tags WHERE tag_name ='$tag_name'";
			$res= $global_db->dbms_query($query);

			if(! $global_db->dbms_check_result($res)) return;

			list($this->id_tag, $this->tag_name, $this->calc_method, $this->description, $this->value, $this->extrainfo, $this->connection, $this->is_public)= $global_db->dbms_fetch_row($res);
			$this->value=stripslashes($this->value);

			if(count($tag_parts) > 1) {
				$this->extrainfo.= ";" . $tag_parts[1] . ";";
			}
			$global_db->dbms_free_result($res);

			if(!$this->is_public) {
				$user= new user($USER_ID);
				if($user->level != 0) die("Access denied to tag $this->tag_name");
			}

			$this->get_connection();
			$this->parse_extrainfo();
			$this->is_ok= true;
		}

		public function add_parameter($parameter,$value) {

			$this->parameters[$parameter]= $value;
			$this->extrainfo.=";$parameter=$value";
		}

		protected function get_parameter($parameter) {
			if(!isset($this->parameters[$parameter])) return false;
			return $this->parameters[$parameter];
		}

		/**
		 * Get the real value of TAG
		 *
		 * @return (constant, string, url or operation)
		 */
		public function get_value() {

			// TODO: the return value in case of error should be an error code, e.g. "false"
			// This means check the return code on the recursive TAG call.
			if(!$this->is_ok) return "<font color='red'>** TAG \"" . addslashes($this->tag_name) . "\" NOT FOUND **</font>";

			/**
			 * FIRST STEP: replace parameters / vars
			 *
			 * Those are set into the forms: "{$<var_name>}", "[$<var_name>]", and they will be
			 * searched as a parameter.
			 */
			$vars=get_vars($this->value, $this->calc_method);
echo "<h1>" . $this->tag_name . "</h1>";
echo "<hr>VARS<br>";
print_r($vars);
echo "<hr>PARMS<br>";
print_r($this->parameters);
			while((count($vars) > 0)) {

				$previous_value= $this->value;

				foreach($vars as $var) {
					$var_value= $this->get_parameter($var);
					if($var_value !== false) {
						$this->value=str_replace('[$' . $var . ']', $var_value, $this->value);
					} else {
						echo "*** Warning: Expected parameter $var on tag $this->tag_name, but not found ***<br>";
					}
				}

				// If no changes where made, then exit loop
				if($this->value == $previous_value) break;

				$vars=get_vars($this->value, $this->calc_method);
			}

			$tags=get_tags($this->value);

			while((count($tags) > 0)) {

				$previous_value= $this->value;

				foreach($tags as $tag) {

					if(strpos($tag,"$") !== false ) {	// Expects a variable
						$new_tag= $this->get_parameter(ltrim($tag, '$'));
						if($new_tag === false) {
							echo "*** Warning: Expected TAG-Parameter $tag on tag $this->tag_name, but not found ***<br>";
						}

					} else {											// Is a normal tag

						if(in_array($tag, $this->tag_calls)) {
							echo "******************** ERROR ********************<br>";
							echo "*** Recursive call detected. Aborting process.<br>";
							echo "*** Tag '" . $this->tag_name . "' calls for tag '" . $tag . "' which is already being calculated.<br>";
							echo "*** Call tree:<br>";

							$old_tn="";
							foreach($this->tag_calls as $tn) {
								if($old_tn != "") echo "*** - '$old_tn' calls '$tn'<br>";
								$old_tn=$tn;
							}
							if($old_tn != "") echo "*** - '$old_tn' calls '" . $tag . "' which is above<br>";
							exit;
						}

						$tag_instance= new tag($tag,$this->tag_calls);
						$new_tag= $tag_instance->get_value();
					}

					//replace tag in report
					$this->value=str_replace('{' . $tag . '}', $new_tag, $this->value);
				}

				// If no changes where made, then exit loop
				if($this->value == $previous_value) {
					echo "NO CHANGES ON TAGS -> BREAK<br>";
					break;
				}

				$tags=get_tags($this->value);
			}

			$tag_name= "tag_" . $this->calc_method;

			$file= INC_DIR . "/reports/tags/" . $tag_name  . ".class.php";
			if(!file_exists($file)) {
				$file= MY_INC_DIR . "/reports/tags/" . $tag_name  . ".class.php";
				if(!file_exists($file)) return "** Error: TAG type not implemented: $tag_name **";
			}

			include_once $file;

			foreach($this->parameters as $parameter => $value) {
				$this->extrainfo.=";$parameter=$value";
			}

			$this->tag_ref= new $tag_name($this->value, $this->extrainfo, $this->db);

			$value= $this->tag_ref->get_value();

			return $value;
		}

		protected function get_connection() {

			global $global_db;

			if($this->connection == "") return;

			if($this->connection == "APP_GENERIC_CONN") {
				$this->db= &$global_db;
			} else {

				$file= SYSHOME . "/include/reports/conn/" . $this->connection;
				if(!file_exists($file)) {
					$file= SYSHOME . "/my_include/reports/conn/" . $this->connection;
					if(!file_exists($file)) return "** Error: SQL Connection not found: $this->connection **";
				}

				include_once $file;

				$conn_name= explode(".",$this->connection);
				$func= $conn_name[0] . "_connect";

				$this->db= $func();
			}

			return;
		}

		protected function parse_extrainfo() {

			$this->extrainfo=trim($this->extrainfo);
			$pairs=explode(";",$this->extrainfo);

			///////////////////////////////////////////////
			// First, parse those which are not parameters

			// Extract parameters and values
			foreach($pairs as $pair) {
				$pair= trim($pair);

				$elements=explode("=",$pair,2);

				if(!isset($elements[0]) or $elements[0] == "") continue;
				if(strpos($elements[0], "--") === 0) continue;

				trim($elements[0]);
				$value= isset($elements[1]) ? trim($elements[1]) : "";

				$tags=get_tags($value);

				foreach($tags as $tag) {
					if(strpos($tag,"$") !== false ) continue;		// Is a parameter

					$tag_value= get_tag_value($tag);
					$value=str_replace('{' . $tag . '}', $tag_value, $value);
				}

				$this->parameters[$elements[0]]= $value;
			}

			///////////////////////////////////////////////
			// Then, parse those which are parameters

			// Extract parameters and values
			foreach($pairs as $pair) {
				$pair= trim($pair);
				$elements=explode("=",$pair,2);

				if(!isset($elements[0]) or $elements[0] == "") continue;
				if(strpos($elements[0], "--") === 0) continue;

				trim($elements[0]);
				$value= isset($elements[1]) ? trim($elements[1]) : "";

				$vars=get_vars($value, $this->calc_method);

				foreach($vars as $var) {

					$var_value= $this->get_parameter($var);
					if($var_value === false) {
						echo "<font color='red'>*** Warning: Expected parameter $tag[0] on tag property $elements[0], but not found ***</font><br>";
					}

					$value=str_replace('{$' . $var . '}', $var_value, $value);
					$value=str_replace('[$' . $var . ']', $var_value, $value);
					$this->parameters[$elements[0]]= $value;
				}
			}
		}
	}

	/**
	 * Take from a phrase the words between '{' and '}'
	 *
	 * @param $string string
	 * @return arrays of tags
	 */
	function & get_tags($string) {

		preg_match_all(TAG_REGEXP, $string, $tags_tmp, PREG_PATTERN_ORDER);

		$tags= array();

		foreach($tags_tmp[1] as $tag) {
			if(strpos($string, "{" . $tag) !== false) $tags[]= $tag;
		}

		return $tags;
	}

	function & get_vars($string, $calc_method="any") {

		$vars=array();

		if($calc_method!="php_code") {
			// Search VARS in the form "[$..."
			$ret1= preg_match_all(VAR_REGEXP1, $string, $vars1, PREG_PATTERN_ORDER);
/*			foreach($vars1[1] as $var) {
				if(strpos($string, '[$' . $var) !== false) $vars[]= $var;
			}*/
		}

		// Search VARS in the form "{$..."
		$ret2= preg_match_all(VAR_REGEXP2, $string, $vars2, PREG_PATTERN_ORDER);
/*		foreach($vars2[1] as $var) {
			if(strpos($string, '{$' . $var) !== false) $vars[]= $var;
		}
*/
		$vars= array_merge($vars1[1], $vars2[1]);

		return $vars;
	}

	/**
	 * For a tag name given, calculates the value and returns it.
	 *
	 * @param $tag_name string
	 */
	function get_tag_value($tag_name) {

		$tag_instance= new tag($tag_name);

		if(!$tag_instance) return false;
		return $tag_instance->get_value();
	}

	/**
	 * If the string is a TAG ({xxxxxx}) returns the tag name (xxxxxx), else
	 * returns false.
	 *
	 * @param $string string
	 */
	function is_a_tag($string) {

		if(preg_match('#\{([a-z0-9\-_]*?)\}#is', $string, $tags)) {
			return $tags[1];
		} else {
			return false;
		}
	}
?>
