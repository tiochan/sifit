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


	class tag_generic_graph_linear extends tag_generic_graph {

		public function get_value() {

			global $USER_ID;

			parent::get_value();

			$grapher= INC_DIR . "/reports/graphs/generic_graph_linear.php";
			if(!file_exists($grapher)) return "** Error: Can't locate generic grapher **";

			include_once $grapher;

			$gr= new generic_graph_linear($this->value, $this->parameters, $this->db);
			return $gr->getImageTag();
		}

		protected function show_help() {
			echo "<p>This tag will create a linear graph, built from the results of a query.";
			echo "Write a SQL sentence knowing that:<br>";
			echo " - The <b>first field will be used as labels</b>,<br>";
			echo " - For each other field, a line sequence will be shown,<br>";
			echo " - All sequences are shown over the same graph, each sequence with one different color.<br>";
			echo "</p>";
			echo "<p>Example:</p>";
			echo "<p style='font-family: courier; white-space: show' nowrap>";
			echo "SELECT date_format(start,'%Y, Week %u') as 'week',<br>" .
				" (SUM(vol) >> 40) as 'TB',<br>" .
				" AVG(duration) as 'Duration'<br>" .
				"FROM backup_results<br>" .
				"GROUP BY date_format(start,'%Y, Week %u')<br>" .
				"ORDER BY week;</p>";
		}
	}
?>