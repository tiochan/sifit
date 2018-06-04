<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage reports
 *
 * Alerts evolution on the last year (12 months).
 * Echo a png string, creating a graph that indicates how many alerts
 * have appeared on each month.
 */

	if(!defined("QUIET")) define("QUIET",true);		// Avoid init echos and html includes

	include_once INC_DIR . "/reports/graphs/report_graph.class.php";


	abstract class generic_query_graph extends report_graph {

		protected $query;

		public function generic_query_graph($value, $extrainfo, &$db=null) {

			parent::__construct(false,$extrainfo, $db);

			$this->query= $value;
		}

		protected function get_data(&$labels, &$values, &$plot_names, $filename="") {

			///////////////////////////////////////////////////////////////////////////
			// Get data
			$res= $this->db->dbms_query($this->query);

			if($this->db->dbms_check_result($res)) {

				$num_fields= $this->db->dbms_num_fields($res);

				$values= array();
				$plot_names= array();

				for($i=1; $i < $num_fields; $i++) {
					$values[$i]= array();
					$plot_names[$i]= $this->db->dbms_field_name($res,$i);
				}

				$labels= Array();

				while($row= $this->db->dbms_fetch_row($res)) {

					$labels[]= $row[0];

					for($i=1; $i < $num_fields; $i++) {
						$values[$i][]= $row[$i];
					}
				}

				$this->db->dbms_free_result($res);

			} else {

				if($this->db->dbms_error()!="") echo "SQL Error: " . $this->db->dbms_error();
				return 0;
			}

			return 1;
		}
	}
?>