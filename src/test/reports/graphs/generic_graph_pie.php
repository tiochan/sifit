<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage reports
 *
 * Generic pie plot graph.
 *
 * Graphs can be launched via CLI (command line) to generate automatically
 * the reports.
 *
 * In those cases the user is passed as argument on the URL
 *
 */

	include_once INC_DIR . "/reports/graphs/generic_query_graph.class.php";

	class generic_graph_pie extends generic_query_graph {

		protected $percent= false;


		public function generic_graph_pie($value,$extra_info,&$db) {

			parent::generic_query_graph($value,$extra_info,$db);

			$this->show_legend=true;
		}

		public function render($filename="") {

			global $MESSAGES;

			if(!$this->get_data($this->labels, $this->values, $this->plot_names, $filename)) {
				$this->labels=array();
				$this->values=array();
				$this->plot_names=array();

				$this->showMessage("No data",$filename);
				return 0;
			}

			$this->width= 600;
			$this->height= 200;

			$this->left= 10;
			$this->right= 10;
			$this->top= 10;
			$this->bottom= 10;

			$this->get_overriden_values();

			///////////////////////////////////////////////////////////////////////////
			// Create graph
			$this->graph= new PieGraph($this->width, $this->height, "auto");
			$this->graph->SetMarginColor('white');
			$this->graph->img->SetMargin($this->left,$this->right,$this->top,$this->bottom);
			$this->graph->SetFrameBevel(0,false);

			$theme_class= new SoftyTheme();
			$this->graph->SetTheme($theme_class);

			if($this->title != "") {
				$this->graph->title->Set($this->title);
				$this->graph->title->SetFont(FF_FONT2,FS_BOLD);
			}
			if($this->subtitle != "") $this->graph->subtitle->Set($this->subtitle);


			$lplots= array();

			$num_plots= count($this->plot_names) + 1; // The first plot is empty


			for($i=1; $i < $num_plots; $i++) {

				$j= $i - 1;

				$lplots[$j]=new PiePlot3D($this->values[$i]);
				if($this->show_legend) $lplots[$j]->SetLegends($this->labels);
				$lplots[$j]->SetCenter(0.30,0.5);
				$lplots[$j]->SetSize(0.5);

				if(!$this->percent) {
					$lplots[$j]->SetValueType(PIE_VALUE_ABS);
					$lplots[$j]->value->SetFormat('%d');
				}

				$this->graph->Add($lplots[$j]);
			}

			$this->graph->Stroke($filename);
			unset($this->graph);
		}
	}
?>