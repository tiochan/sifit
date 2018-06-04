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


	class tag_query extends tag_element {

		public function get_value() {

			global $USER_GROUP;
			global $USER_ID;

			parent::get_value();

			//get the value of the query
			$query= stripslashes($this->value);
			$res= $this->db->dbms_query($query);

			if((is_int($res) or is_bool($res))and $res == 1 and $this->db->dbms_error() == "") {

				if(isset($this->parameters["SHOW_NO_DATA"]) and !$this->parameters["SHOW_NO_DATA"]) return;
				else return "-- No data returned --";
			}

			if(! $this->db->dbms_check_result($res) and $this->db->dbms_error() != "") return "** Error: " . $this->db->dbms_error() . " **";


			$num_rows=$this->db->dbms_num_rows($res);

			if($num_rows == 0) {
				if(isset($this->parameters["SHOW_NO_DATA"]) and !$this->parameters["SHOW_NO_DATA"]) return;
				else return "-- No data --";
			}

			if($num_rows > 1 or $this->db->dbms_num_fields($res) > 1) {

				include_once INC_DIR . "/forms/form_basic.inc.php";

				if(isset($this->parameters["CSV"]) and $this->parameters["CSV"]) {
					$valor_value= convertResultToCSV($this->db, $res, false);
				} else {
					$valor_value= convertResultToString($this->db, $res);
				}

			} else {
				list($valor_value)= $this->db->dbms_fetch_row($res);
			}

			$this->db->dbms_free_result($res);

			return $valor_value;
		}

		protected function change_field_properties(&$field) {
			$field->alias="Query";
		}

		protected function show_help() {
			echo "The result of the query will be shown as a table.";
		}
	}
?>
