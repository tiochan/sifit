<?php
/**
 * @authors Jorge Novoa (jorge.novoa@upcnet.es), Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage reports
 *
 * Tags definition class
 *
 */


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

			if(!$this->is_ok) return "<font color='red'>** TAG $this->tag_name NOT FOUND **<font>";


			/**
			 * Search for vars.
			 *
			 * Those are set into the form: "[$<var_name>]", and they will be
			 * searched as a parameter.
			 */
			$vars=get_vars($this->value);

			foreach($vars[1] as $var) {

				$var_value= $this->get_parameter($var[0]);

				if($var_value !== false) $this->value=str_replace('[$' . $var[0] . ']', $var_value, $this->value);
			}


			$tags=get_tags($this->value);

			foreach($tags[1] as $tag) {

				if(strpos($tag[0],"$") !== false ) {	// Expects a variable

					$new_tag= $this->get_parameter(ltrim($tag[0], '$'));
					if($new_tag === false) {
						echo "*** Warning: Expected parameter $tag[0] on tag $this->tag_name, but not found ***<br>";
					}

				} else {											// Is a normal tag

					if(in_array($tag[0], $this->tag_calls)) {
						echo "******************** ERROR ********************<br>";
						echo "*** Recursive call detected. Aborting process.<br>";
						echo "*** Tag '" . $this->tag_name . "' calls for tag '" . $tag[0] . "' which is already being calculated.<br>";
						echo "*** Call tree:<br>";

						$old_tn="";
						foreach($this->tag_calls as $tn) {
							if($old_tn != "") echo "*** - '$old_tn' calls '$tn'<br>";
							$old_tn=$tn;
						}
						if($old_tn != "") echo "*** - '$old_tn' calls '" . $tag[0] . "' which is above<br>";
						exit;
					}
					$tag_instance= new tag($tag[0],$this->tag_calls);
					$new_tag= $tag_instance->get_value();
				}

				//replace tag in report
				$this->value=str_replace('{' . $tag[0] . '}', $new_tag, $this->value);
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

				$elements=explode("=",$pair);

				if(!isset($elements[0]) or $elements[0] == "") continue;

				trim($elements[0]);
				$value= isset($elements[1]) ? trim($elements[1]) : "";

				$tags=get_tags($value);

				foreach($tags[1] as $tag) {
					if(strpos($tag[0],"$") === false ) {
						$tag_value= get_tag_value($tag[0]);
						$value=str_replace('{' . $tag[0] . '}', $tag_value, $value);
					}
				}

				$this->parameters[$elements[0]]= $value;
			}

			///////////////////////////////////////////////
			// Then, parse those which are parameters

			// Extract parameters and values
			foreach($pairs as $pair) {
				$pair= trim($pair);
				$elements=explode("=",$pair);

				if(!isset($elements[0]) or $elements[0] == "") continue;

				trim($elements[0]);
				$value= isset($elements[1]) ? trim($elements[1]) : "";

				$tags=get_tags($value);

				foreach($tags[1] as $tag) {

					if(strpos($tag[0],"$") !== false ) {	// Expects a variable
						$tag_value= $this->get_parameter(ltrim($tag[0], '$'));
						if($tag_value === false) {
							echo "*** Warning: Expected parameter $tag[0] on tag property $elements[0], but not found ***<br>";
						}

						$value=str_replace('{' . $tag[0] . '}', $tag_value, $value);
						$this->parameters[$elements[0]]= $value;
					}
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

		preg_match_all('#\{([a-z0-9\-_|=;\$]*?)}#is', $string, $tags, PREG_OFFSET_CAPTURE);
		return $tags;
	}

	function & get_vars($string) {
		preg_match_all('#\[\$([a-z0-9\-_|=;\$]*?)\]#is', $string, $vars, PREG_OFFSET_CAPTURE);
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