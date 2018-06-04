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

		protected $show_no_data=false;
		protected $csv=false;
		protected $show_field_names=false;

		public function get_value() {

			global $USER_GROUP;
			global $USER_ID;

			$this->replace_parameters();
			//get the value of the query
			$query= stripslashes($this->value);
			$res= $this->db->dbms_query($query);

			$this->show_no_data= (isset($this->parameters["SHOW_NO_DATA"]) and (strtolower($this->parameters["SHOW_NO_DATA"])=="true"));
			$this->csv= (isset($this->parameters["CSV"]) and (strtolower($this->parameters["CSV"])=="true"));
			$this->show_field_names= (isset($this->parameters["SHOW_FIELD_NAMES"]) and (strtolower($this->parameters["SHOW_FIELD_NAMES"])=="true"));

			if((is_int($res) or is_bool($res))and $res == 1 and $this->db->dbms_error() == "") {

				if($this->show_no_data) return "-- No data --";
				return;
			}

			if(! $this->db->dbms_check_result($res) and $this->db->dbms_error() != "") return "** Error: " . $this->db->dbms_error() . " **";


			$num_rows=$this->db->dbms_num_rows($res);

			if($num_rows == 0) {
				if(!$this->show_no_data) return;
				else return "-- No data --";
			}

			if($num_rows > 1 or $this->db->dbms_num_fields($res) > 1) {

				include_once INC_DIR . "/forms/form_basic.inc.php";

				if($this->csv) {
					$valor_value= convertResultToCSV($this->db, $res, $this->show_field_names);
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
			echo "The result of the query will be shown as a HTML table or CSV (see extra parameters).";
		}


		protected function show_extra_help() {
?>
			<p>
				<b>Extra parameters</b><br>
				<br>
				You can also add some extra parameters into the <i>'additional information field'</i>, separated by ';'.<br>
				There are some examples of extra parameters:<br>
				<br>
				<table class='data_box_rows'>
					<tr>
						<th class='data_box_rows'>Parameter</th>
						<th class='data_box_rows'>Default</th>
						<th class='data_box_rows'>Description</th>
					</tr>
					<tr>
						<td class='data_box_cell'>CSV=[true|false]</td>
						<td class='data_box_cell'>false</td>
						<td class='data_box_cell'>If you need values to be returned as CSV string, instead HTML table.</td>
					</tr>
					<tr>
						<td class='data_box_cell'>SHOW_NO_DATA=[true|false]</td>
						<td class='data_box_cell'>false</td>
						<td class='data_box_cell'>If true and the query does not retuns any data, a string with this situation is shown.</td>
					</tr>
					<tr>
						<td class='data_box_cell'>SHOW_FIELD_NAMES=[true|false]</td>
						<td class='data_box_cell'>false</td>
						<td class='data_box_cell'>For CSV output, if true then the first row is set with the field names..</td>
					</tr>
				</table>
			</p>
<?php
		}
	}
?>
