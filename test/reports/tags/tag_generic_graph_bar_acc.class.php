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


	class tag_generic_graph_bar_acc extends tag_generic_graph {

		public function get_value() {

			global $USER_ID;

			parent::get_value();

			$grapher= INC_DIR . "/reports/graphs/generic_graph_bar_acc.php";
			if(!file_exists($grapher)) return "** Error: Can't locate generic grapher **";

			include_once $grapher;

			$gr= new generic_graph_bar_acc($this->value, $this->parameters, $this->db);
			return $gr->getImageTag();
		}

/*		protected function show_help() {
			echo "
<p>
This tag will create a bar graph, built from the results of a query.
Write a SQL sentence knowing that:<br>
 - The <b>first field will be used as labels</b>,<br>
 - For each other field, a bar will be shown,<br>
 - All sequences are shown over the same graph, each sequence with one different color.<br>
</p>
<p>Example:</p>
<p style='font-family: courier; white-space: show' nowrap>
SELECT date_format(process_start,'%Y, Week %u') as 'Week', --> This field will be used as labels<br>
  (sum(vol) >> 40) as 'TB', --> Generates one bar sequence<br>
  avg(duration) as 'Duration' --> Generates another bar sequence<br>
FROM backup_results<br>
GROUP BY date_format(start,'%Y, Week %u')<br>
ORDER BY Week;
</p>";
		}
*/
	}
?>
