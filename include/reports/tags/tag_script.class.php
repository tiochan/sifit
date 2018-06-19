<?php
/**
 * @author Jorge Novoa (jorge.novoa@upcnet.es)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sifit
 * @subpackage reports
 *
 * Tags definition class
 *
 */

	include_once INC_DIR . "/reports/tags/tag_element.class.php";
	include_once INC_DIR . "/forms/field_types/listbox.inc.php";


	class tag_script extends tag_element {
		protected $show_connection= false;

		public function get_value() {

			$this->replace_parameters();

			$filename= INC_DIR . "/reports/scripts/" . $this->value . ".php";
			if(!file_exists($filename)) {
				$filename= MY_INC_DIR . "/reports/scripts/" . $this->value . ".php";
				if(!file_exists($filename)) {
					return "** Error: can't locate script file " . $filename . " **";
				}
			}

			include_once $filename;

			$function = $this->value;
			$result= $function($this->parameters);

			return $result;
		}

		static public function check_value($value) {

			$filename= INC_DIR . "/reports/scripts/" . $value . ".php";
			if(!file_exists($filename)) {
				$filename= MY_INC_DIR . "/reports/scripts/" . $value . ".php";
				if(!file_exists($filename)) {
					html_showError("** Error: can't locate script file " . $filename . " **");
					return 0;
				}
			}

			include_once $filename;
			if(!function_exists($value)) {
				html_showError("** Error: script " . $filename . " does not contains the function $value **");
				return 0;
			}

			return 1;
		}

		protected function change_field_properties(&$field) {
			$field->reference= new scripts_list();
			$field->alias="Script";
		}
	}

	class scripts_list extends listbox {

		public function scripts_list() {

			parent::listbox();
			$this->lb[""]="";

			$files= read_dir(INC_DIR . "/reports/scripts",".php");

			foreach($files as $file) {
				$scripts_name= substr($file,0,strpos($file,"."));
				$this->lb[$scripts_name]= $file;
			}

			$files= read_dir(MY_INC_DIR . "/reports/scripts",".php");

			foreach($files as $file) {
				$scripts_name= substr($file,0,strpos($file,"."));
				$this->lb[$scripts_name]= $file;
			}

			asort($this->lb);
		}
	}
