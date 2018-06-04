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

	include_once INC_DIR . "/reports/tags/tag_generic_graph.class.php";


	class tag_generic_graph_pie_perc extends tag_generic_graph {

		public function get_value() {

			global $USER_ID;

			parent::get_value();

			$grapher= INC_DIR . "/reports/graphs/generic_graph_pie_perc.php";
			if(!file_exists($grapher)) return "** Error: Can't locate generic grapher **";

			include_once $grapher;

			$gr= new generic_graph_pie_perc($this->value, $this->parameters, $this->db);
			return $gr->getImageTag();
		}

		protected function show_help() {
			echo "<p>This tag will create a pie graph, built from the results of a query with <b>two fields</b>.";
			echo "Write a SQL sentence knowing that:<br>";
			echo " - The <b>first field will be used as labels for legend</b>,<br>";
			echo " - The other field contains the values to be shown into the pie; here <b>shown as '%' </b>of the total).<br>";
			echo "</p>";
			echo "<p>Example:</p>";
			echo "<p style='font-family: courier; white-space: show' nowrap>";
			echo "SELECT CONCAT('Level ',lvl, ': ', (sum(vol) >> 40), ' TB') AS 'Level',<br>" .
				 "(SUM(vol) >> 40) AS 'TB'<br>" .
				 "FROM backup_results<br>" .
				 "WHERE start>= '2010-08-09' AND end<='2010-08-16'<br>" .
				 "GROUP BY lvl</p>";
		}
	}
?>