<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage reports
 *
 * Generic bar plot graph with two Y axis (2 different scales).
 *
 * Graphs can be launched via CLI (command line) to generate automatically
 * the reports.
 *
 * In those cases the user is passed as argument on the URL
 *
 */

	include_once INC_DIR . "/reports/graphs/generic_query_graph.class.php";


	class generic_graph_bar_double extends generic_query_graph {

		public function render($filename="") {

			$this->double= true;

			$this->width= 600;
			$this->height= 300;

			$this->left=60;
			$this->right=120;
			$this->top=30;
			$this->bottom=30;

			$this->create_graph("BarPlot",$filename);
			$this->graph->Stroke($filename);

			unset($this->graph);
		}
	}
?>