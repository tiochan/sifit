<?php
/**
 * @author Jorge Novoa (jorge.novoa@upcnet.es)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage reports
 *
 * Tags definition class
 *
 */

	include_once INC_DIR . "/reports/tags/tag_element.class.php";
	include_once INC_DIR . "/forms/field_types/listbox.inc.php";


	class tag_graph extends tag_element {

		protected $show_connection= false;


		public function get_value() {

			global $USER_ID;

			parent::get_value();

			$grapher= INC_DIR . "/reports/graphs/" . $this->value . ".graph.php";
			if(!file_exists($grapher)) {
				$grapher= MY_INC_DIR . "/reports/graphs/" . $this->value . ".graph.php";
				if(!file_exists($grapher)) return "** Error: Can't locate grapher " . $this->value . " **";
			}

			include_once $grapher;
			$gr= new $this->value(false,$this->parameters);

			return $gr->getImageTag();
		}

		static public function check_value($value) {

			$filename= INC_DIR . "/reports/graphs/" . $value . ".graph.php";
			if(!file_exists($filename)) {
				$filename= MY_INC_DIR . "/reports/graphs/" . $value . ".graph.php";
				if(!file_exists($filename)) {
					html_showError("Can't find graph file: $filename");
					return 0;
				}
			}

			return 1;
		}

		protected function change_field_properties(&$field) {
			$field->reference= new graph_tag();
			$field->alias="Graph";
		}
	}

	class graph_tag extends listbox {

		public function graph_tag() {

			parent::listbox();

			$this->lb[""]="";

			$files= read_dir(INC_DIR . "/reports/graphs",".graph.php");

			foreach($files as $file) {
				$graph_name= substr($file,0,strpos($file,"."));
				$this->lb[$graph_name]= $file;
			}

			$files= read_dir(MY_INC_DIR . "/reports/graphs",".graph.php");

			foreach($files as $file) {
				$graph_name= substr($file,0,strpos($file,"."));
				$this->lb[$graph_name]= $file;
			}

			asort($this->lb);
		}
	}

?>
