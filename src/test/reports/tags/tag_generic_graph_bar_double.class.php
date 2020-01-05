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


	class tag_generic_graph_bar_double extends tag_generic_graph {

		public function get_value() {

			global $USER_ID;

			parent::get_value();

			$grapher= INC_DIR . "/reports/graphs/generic_graph_bar_double.php";
			if(!file_exists($grapher)) return "** Error: Can't locate generic grapher **";

			include_once $grapher;

			$gr= new generic_graph_bar_double($this->value, $this->parameters, $this->db);
			return $gr->getImageTag();
		}

		protected function show_help() {
			echo "<p>This tag will create a <b>horizontal bar graph</b> for just two <b>different scales</b>, built from the results of a query." .
				"Write a SQL sentence knowing that:<br>" .
				" - The <b>first field will be used as labels</b>,<br>" .
				" - The second and the third fields will generate two bar sequences with different colors,<br>" .
				" - The scales are show for the second and third fields" .
				"</p>" .
				"<p>Example:</p>" .
				"<p style='font-family: courier; white-space: show' nowrap>" .
				"SELECT concat(server,0xa,fs),<br>" .
				"  AVG(throughput) as KBps,<br>" .
				"  AVG(duration) as secs<br>" .
				"FROM backup_results<br>" .
				"WHERE<br>" .
				"  start>='2010/08/13 10:00:00' AND<br>" .
				"  end<='2011/08/15 08:00:00' AND<br>" .
				"GROUP BY server,fs<br>" .
				"ORDER BY KBps ASC<br>" .
				"LIMIT 50" .
				"</p>";
		}
	}

?>