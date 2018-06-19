<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sifit
 * @subpackage reports
 *
 * Tags definition class
 *
 */

	include_once INC_DIR . "/reports/tags/tag_generic_graph.class.php";


	class tag_generic_graph_bar_90 extends tag_generic_graph {

		public function get_value() {

			global $USER_ID;

			$this->replace_parameters();

			$grapher= INC_DIR . "/reports/graphs/generic_graph_bar_90.php";
			if(!file_exists($grapher)) return "** Error: Can't locate generic grapher **";

			include_once $grapher;

			$gr= new generic_graph_bar_90($this->value, $this->parameters, $this->db);
			return $gr->getImageTag();
		}
	}

