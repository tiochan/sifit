<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage reports
 *
 * Generic vertical bar plot graph with two Y axis (2 different scales).
 * 
 * Graphs can be launched via CLI (command line) to generate automatically
 * the reports.
 *
 * In those cases the user is passed as argument on the URL
 *
 */

	include_once INC_DIR . "/reports/graphs/generic_graph_bar_double.php";


	class generic_graph_bar_double_90 extends generic_graph_bar_double {

		public function render($filename="") {

			$this->vertical= true;
			$this->double= true;
			
			$this->width= 600;
			$this->height= 500;
						
			$this->top = 60;
			$this->bottom = 30;
			$this->left = 200;
			$this->right = 30;
			
			$this->create_graph("BarPlot",$filename);
			$this->graph->Stroke($filename);
			unset($this->graph);
		}
	}
?>