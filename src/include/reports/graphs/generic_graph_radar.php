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

include_once INC_DIR . "/reports/graphs/generic_graph.php";
include_once INC_DIR . "/jpgraph/src/jpgraph_radar.php";

class generic_graph_radar extends generic_graph
{

	public function render($filename = "")
	{

		global $jpgraph_colors;

		$this->width = 600;
		$this->height = 300;

		$this->left = 40;
		$this->right = 130;
		$this->top = 40;
		$this->bottom = 100;

		// Some data to plot
		if (!$this->get_data($this->labels, $this->values, $this->plot_names, $filename)) {
			$this->labels = array();
			$this->values = array();
			$this->plot_names = array();

			$this->showMessage("No data", $filename);
			return 0;
		}
		$this->get_overriden_values();

		// Create the graph and the plot
		$this->graph = new RadarGraph($this->width, $this->height, "auto");
		$this->graph->img->SetMargin($this->left, $this->right, $this->top, $this->bottom);
		$this->graph->SetFrameBevel(0, false);

		/*			$theme_class= new SoftyTheme();
			$this->graph->SetTheme($theme_class);
*/
		if ($this->title != "") {
			$this->graph->title->Set($this->title);
			$this->graph->title->SetFont(FF_FONT2, FS_BOLD);
		}
		if ($this->subtitle != "") $this->graph->subtitle->Set($this->subtitle);

		// Create the titles for the axis
		$this->graph->SetTitles($this->labels);

		// Add grid lines
		$this->graph->grid->Show();
		$this->graph->grid->SetColor('darkred');
		$this->graph->grid->SetLineStyle('dotted');

		$lplots = array();

		$num_plots = count($this->plot_names) + 1; // The first plot is empty

		for ($i = 1; $i < $num_plots; $i++) {

			$j = $i - 1;

			$lplots[$j] = new RadarPlot($this->values[$i]);
			$lplots[$j]->SetColor($jpgraph_colors[$j]);
			if ($this->show_legend) $lplots[$j]->SetLegend($this->plot_names[$i]);

			$lplots[$j]->SetFillColor($jpgraph_colors[$j] . "@0.9");
			$this->graph->Add($lplots[$j]);
		}

		$this->graph->Stroke($filename);

		unset($this->graph);
	}
}
