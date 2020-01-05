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
	include_once INC_DIR . "/reports/graphs/generic_graph_bar.php";


	class generic_graph_bar_acc extends generic_graph_bar {

		public function render($filename="") {

			$this->accumulated= true;

			parent::render($filename);
		}
	}
?>