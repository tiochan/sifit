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


	class tag_generic_graph_radar extends tag_generic_graph {

		public function get_value() {

			global $USER_ID;

			parent::get_value();

			$grapher= INC_DIR . "/reports/graphs/generic_graph_radar.php";
			if(!file_exists($grapher)) return "** Error: Can't locate generic grapher **";

			include_once $grapher;

			$gr= new generic_graph_radar($this->value, $this->parameters, $this->db);
			return $gr->getImageTag();
		}

		protected function show_help() {
			echo "<p>This tag will create a radar graph, built from the results of a query.";
			echo "Write a SQL sentence knowing that:<br>";
			echo " - The <b>first field will be used as labels</b>,<br>";
			echo " - For each other field, a radar bar will be shown,<br>";
			echo "</p>";
			echo "<p>Example:</p>";
			echo "<p style='font-family: courier; white-space: show' nowrap>";
			echo "SELECT date_format(process_start,'%Y, Week %u') as 'Week', --> This field will be used as labels<br>";
			echo "  (sum(vol) >> 40) as 'TB', --> Generates one bar sequence<br>";
			echo "  avg(duration) as 'Duration' --> Generates another bar sequence<br>";
			echo "FROM backup_results<br>";
			echo "GROUP BY date_format(start,'%Y, Week %u')<br>";
			echo "ORDER BY Week;</p>";
		}
	}
?>