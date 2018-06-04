<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage reports
 *
 * Generic bar plot graph.
 *
 * Graphs can be launched via CLI (command line) to generate automatically
 * the reports.
 *
 * In those cases the user is passed as argument on the URL
 *
 */

	include_once INC_DIR . "/reports/graphs/generic_query_graph.class.php";


	class generic_graph_bar extends generic_query_graph {

		public function render($filename="") {

			$this->width= 600;
			$this->height= 300;

			$this->left=40;
			$this->right=130;
			$this->top=40;
			$this->bottom=100;

			$this->create_graph("BarPlot",$filename);
			$this->graph->Stroke($filename);

			unset($this->graph);
		}
	}
?>