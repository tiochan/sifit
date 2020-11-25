<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
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
 define("TAG_REGEXP", '#\{([a-z0-9\,\.\-\_\|\=\;\<\>\'\"\@\#\%\&\(\)\$\/ ][^\{\}]*?)}#is');
 define("TAG_REGEXP", '#\{(.[^\{\}]*)}#is');
 define("TAG_REGEXP", '#\{([^\$\s].[^\{\}]*)}#is');

 For parameters/Vars:
 define("VAR_REGEXP", '#\[\$([a-z0-9\.\-_|=;\$ ]*?)]#is');
 define("VAR_REGEXP1", '#\[\$(.?[^{]*[^}])]#is');
 define("VAR_REGEXP2", '#\{\$(.?[^{]*[^}])}#is');
 */

define("TAG_REGEXP", '#\{\{([^\$\s].[^\{\}]*)\}\}#is');
define("VAR_REGEXP", '#\{\{\$([^\s].[^\{\}]*)\}\}#is');

class tag
{

	public $id_tag;
	public $tag_name;
	public $calc_method;
	public $description;
	public $value;
	public $extrainfo;
	protected $extrainfo_orig;
	public $connection;
	public $is_public = false;
	public $parameters;
	public $parsed_parameters;

	protected $db;

	protected $is_ok;
	protected $tag_ref;

	protected $tag_calls;


	public function tag($tag_name, $tag_calls = null)
	{

		global $global_db;
		global $USER_ID;


		$this->tag_calls = $tag_calls ? $tag_calls : array();
		$this->tag_calls = $tag_calls;
		$this->tag_calls[] = $tag_name;

		$this->parameters = array();
		$this->parsed_parameters = array();

		if (!isset($USER_ID)) die("User id is required but is not defined at tag declaration");

		$this->is_ok = false;

		$tag_parts = explode("|", $tag_name, 2);
		$this->tag_name = $tag_parts[0];

		// Get tag definition
		$query = "SELECT id_tag, calc_method, description, value, extrainfo, connection, is_public FROM report_tags WHERE tag_name ='$this->tag_name'";
		$res = $global_db->dbms_query($query);

		if ($global_db->dbms_check_result($res)) {
			list($this->id_tag, $this->calc_method, $this->description, $this->value, $this->extrainfo_orig, $this->connection, $this->is_public) = $global_db->dbms_fetch_row($res);
			$this->value = stripslashes($this->value);

			$global_db->dbms_free_result($res);
			$this->is_ok = true;
		} else {
			// The tag does not exists.
		}

		if (!$this->is_public) {
			$user = new user($USER_ID);
			if ($user->level != 0) die("Access denied to tag $this->tag_name");
		}
		if (count($tag_parts) > 1) $this->extrainfo_orig .= ";" . $tag_parts[1] . ";";
		$this->extrainfo = $this->extrainfo_orig;
		$this->get_connection();
	}


	public function add_parameter($parameter, $value)
	{

		$this->parameters[$parameter] = $value;
	}


	protected function get_parameter($parameter)
	{

		if (!isset($this->parsed_parameters[$parameter])) return false;
		return $this->parsed_parameters[$parameter];
	}


	/**
	 * Get the real value of TAG
	 *
	 * @return (constant, string, url or operation)
	 */
	public function get_value()
	{

		// TODO: the return value in case of error should be an error code, e.g. "false"
		// This means check the return code on the recursive TAG call.
		if (!$this->is_ok) {
			// Check for live instance for tags: if the tag name itself is the name of the tag type
			$file = INC_DIR . "/reports/tags/tag_" . $this->tag_name  . ".class.php";
			if (!file_exists($file)) die("** Error: TAG \"" . addslashes($this->tag_name) . "\" not found **");
			$this->calc_method = $this->tag_name;
		}

		// FIRST Step: parse parameters on extra info
		$this->parse_extrainfo();
		// SECOND Step: replace parameters / vars set in the forms: "{{$<var_name>}}"
		$this->value = $this->parse_vars($this->value);

		// THIRD Step: in the forms: "{<tag_name>}"
		$this->value = $this->parse_tags($this->value);

		// Create an instance of the tag type
		$tag_class = "tag_" . $this->calc_method;
		$file = INC_DIR . "/reports/tags/" . $tag_class  . ".class.php";
		if (!file_exists($file)) {
			$file = MY_INC_DIR . "/reports/tags/" . $tag_class  . ".class.php";
			if (!file_exists($file)) die("** Error: TAG type not implemented: $tag_class **");
		}

		include_once $file;
		$this->tag_ref = new $tag_class($this->value, $this->extrainfo, $this->db);

		$value = $this->tag_ref->get_value();

		return $value;
	}

	protected function parse_vars($value)
	{

		$vars = get_vars($value, $this->calc_method);

		while ((count($vars) > 0)) {

			$previous_value = $value;

			foreach ($vars as $var) {
				$var_value = $this->get_parameter($var);
				if ($var_value !== false) {
					//					$this->value=str_replace('[$' . $var . ']', $var_value, $value);
					$this->value = str_replace('{{$' . $var . '}}', $var_value, $value);
				} else {
					die("** Warning: Expected parameter $var on tag $this->tag_name, but not found **");
				}
			}

			// If no changes where made, then exit loop
			if ($value == $previous_value) break;

			$vars = get_vars($value, $this->calc_method);
		}

		return $value;
	}

	protected function parse_tags($value)
	{

		$tags = get_tags($value);

		while ((count($tags) > 0)) {

			$previous_value = $value;
			foreach ($tags as $tag) {
				if (strpos($tag, "$") !== false) {	// Expects a variable
					$new_tag = $this->get_parameter(ltrim($tag, '$'));
					if ($new_tag === false) {
						die("** Warning: Expected TAG-Parameter $tag on tag $this->tag_name, but not found **");
					}
				} else {											// Is a normal tag
					if ($this->is_a_loop($tag)) exit;
					$tag_instance = new tag($tag, $this->tag_calls);
					$new_tag = $tag_instance->get_value();
				}

				//replace tag in report
				$value = str_replace('{{' . $tag . '}}', $new_tag, $value);
			}

			// If no changes where made, then exit loop
			if ($value == $previous_value) break;

			$tags = get_tags($value);
		}

		return $value;
	}

	/**
	 * Returns false if there is a looped call.
	 */
	protected function is_a_loop($tag)
	{
		if (in_array($tag, $this->tag_calls)) {
			echo "******************** ERROR ********************<br>\n";
			echo "*** Recursive call detected. Aborting process.<br>\n";
			echo "*** Tag '" . $this->tag_name . "' calls for tag '" . $tag . "' which is already being calculated.<br>\n";
			echo "*** Call tree:<br>\n";

			$old_tn = "";
			foreach ($this->tag_calls as $tn) {
				if ($old_tn != "") echo "*** - '$old_tn' calls '$tn'<br>\n";
				$old_tn = $tn;
			}
			if ($old_tn != "") echo "*** - '$old_tn' calls '" . $tag . "' which is above<br>";
			echo "******************** ERROR ********************<br>\n";
			return true;
		}
		return false;
	}

	protected function get_connection()
	{

		global $global_db;

		if ($this->connection == "") return;

		if ($this->connection == "APP_GENERIC_CONN") {
			$this->db = &$global_db;
		} else {

			$file = SYSHOME . "/include/reports/conn/" . $this->connection;
			if (!file_exists($file)) {
				$file = SYSHOME . "/my_include/reports/conn/" . $this->connection;
				if (!file_exists($file)) return "** Error: SQL Connection not found: $this->connection **";
			}

			include_once $file;

			$conn_name = explode(".", $this->connection);
			$func = $conn_name[0] . "_connect";

			$this->db = $func();
		}

		return;
	}

	protected function parse_extrainfo()
	{

		$this->extrainfo = trim($this->extrainfo_orig);
		// Add the parameters that have been added to the extrainfo property
		// in order to examine all together.
		foreach ($this->parameters as $parameter => $value) {
			$this->extrainfo .= ";$parameter=$value";
		}

		// FIRST Step: explode the extra info into on parameter => value:
		unset($this->parsed_parameters);
		$this->parsed_parameters = array();

		// Extract parameters and values
		$pairs = explode(";", $this->extrainfo);
		foreach ($pairs as $pair) {
			$pair = trim($pair);
			$elements = explode("=", $pair, 2);

			if (!isset($elements[0]) or $elements[0] == "") continue;
			if (strpos($elements[0], "--") === 0) continue;

			trim($elements[0]);
			$value = isset($elements[1]) ? trim($elements[1]) : "";
			$value = $this->parse_vars($value);
			$value = $this->parse_tags($value);
			$this->parsed_parameters[$elements[0]] = $value;
			if (strtolower($elements[0]) == "value") $this->value = $value;
		}
		$this->extrainfo = "";
		foreach ($this->parsed_parameters as $key => $value) {
			$this->extrainfo .= "$key=$value;";
			$this->value = str_replace('{{$' . $key . '}}', $value, $this->value);
		}
	}
}

/**
 * Take from a phrase the words between '{' and '}'
 *
 * @param $string string
 * @return arrays of tags
 */
function &get_tags($string)
{

	preg_match_all(TAG_REGEXP, $string, $tags_tmp, PREG_PATTERN_ORDER);

	$tags = array();

	foreach ($tags_tmp[1] as $tag) {
		if (strpos($string, "{{" . $tag) !== false) $tags[] = $tag;
	}

	return $tags;
}

function &get_vars($string, $calc_method = "any")
{

	$vars = array();

	$ret = preg_match_all(VAR_REGEXP, $string, $vars, PREG_PATTERN_ORDER);
	if (!isset($vars[1])) $vars[1] = array();
	return $vars[1];
}

/**
 * For a tag name given, calculates the value and returns it.
 *
 * @param $tag_name string
 */
function get_tag_value($tag_name)
{

	$tag_instance = new tag($tag_name);

	if (!$tag_instance) return false;
	return $tag_instance->get_value();
}

/**
 * If the string is a TAG ({xxxxxx}) returns the tag name (xxxxxx), else
 * returns false.
 *
 * @param $string string
 */
function is_a_tag($string)
{

	//	if(preg_match('#\{([a-z0-9\-_]*?)\}#is', $string, $tags)) {
	if (preg_match(TAG_REGEXP, $string, $tags)) {
		return $tags[1];
	} else {
		return false;
	}
}
