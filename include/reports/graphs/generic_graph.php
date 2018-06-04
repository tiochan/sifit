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

	include_once INC_DIR . "/reports/graphs/report_graph.class.php";

	class generic_graph extends report_graph {

		protected $data;

		public function generic_graph($value, $extrainfo, &$db=null) {

			parent::__construct(false,$extrainfo, $db);

			$this->data= $value;
		}

		public function render($filename="") {

			$this->width= 600;
			$this->height= 300;

			$this->left=40;
			$this->right=130;
			$this->top=40;
			$this->bottom=100;

			$this->create_graph($this->graph_type,$filename);
			$this->graph->Stroke($filename);

			unset($this->graph);
		}

		protected function get_data(&$labels, &$values, &$plot_names, $filename="") {

			///////////////////////////////////////////////////////////////////////////
			// Get data
			$values= array();
			$plot_names= array();
			$labels= Array();

			$set_col_names= isset($this->parameters["COL_NAMES"]) ? trim($this->parameters["COL_NAMES"],'"') : "";

			if($set_col_names != "") {
				$aux= array('');
				$col_names= array_merge($aux, explode(",", $set_col_names));

				for($i=1; $i < count($col_names); $i++) {
					$col_names[$i]= trim($col_names[$i]);
					$values[$i]= array();
					$plot_names[$i]= $col_names[$i];
				}
			}

			if(strpos($this->data, "<GRAPH_SERIE>")!== false) $this->combine_data();

			$lines= explode("\n", $this->data);

			$cont=0;
			foreach($lines as $line) {

				$line= trim($line);
				if($line == "") continue;

				$pieces= explode(",", $line);

				if(!isset($pieces[0]) or ($pieces[0]== null)) continue;

				// Use the first line as header, if not set from parameters
				if($cont == 0 and $set_col_names == "") {
					for($i=1; $i < count($pieces); $i++) {
						$values[$i]= array();
						$plot_names[$i]= $pieces[$i];
					}
				} else {
					$labels[]= $pieces[0];
					for($i=1; $i < count($pieces); $i++) {
						$values[$i][]= $pieces[$i];
					}
				}

				$cont++;
			}

			return ($cont > 0);
		}
	}
