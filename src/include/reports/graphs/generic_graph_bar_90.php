<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage reports
 *
 * Generic bar plot graph, vertical.
 *
 * Graphs can be launched via CLI (command line) to generate automatically
 * the reports.
 *
 * In those cases the user is passed as argument on the URL
 *
 */

include_once INC_DIR . "/reports/graphs/generic_graph.php";


class generic_graph_bar_90 extends generic_graph
{

	public function render($filename = "")
	{

		$this->vertical = true;

		$this->width = 600;
		$this->height = 300;

		$this->left = 130;
		$this->right = 30;
		$this->top = 60;
		$this->bottom = 30;

		$this->create_graph("BarPlot", $filename);
		$this->graph->Stroke($filename);

		unset($this->graph);
	}
}
