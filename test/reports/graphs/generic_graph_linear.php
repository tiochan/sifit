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


	class generic_graph_linear extends generic_query_graph {

		public function render($filename="") {

			$this->width= 600;
			$this->height= 300;

			$this->left= 40;
			$this->right= 10;
			$this->top= 10;
			$this->bottom= 40;

			$this->create_graph("LinePlot",$filename);
			$this->graph->Stroke($filename);

			unset($this->graph);
		}
	}
?>