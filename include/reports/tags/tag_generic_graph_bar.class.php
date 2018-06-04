<?php
/**
 * @author Jorge Novoa (jorge.novoa@upcnet.es)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage reports
 *
 * Tags definition class
 *
 */

	include_once INC_DIR . "/reports/tags/tag_generic_graph.class.php";


	class tag_generic_graph_bar extends tag_generic_graph {

		public function get_value() {

			global $USER_ID;

			$this->replace_parameters();

			$grapher= INC_DIR . "/reports/graphs/generic_graph_bar.php";
			if(!file_exists($grapher)) return "** Error: Can't locate generic grapher **";

			include_once $grapher;

			$gr= new generic_graph_bar($this->value, $this->parameters, $this->db);
			return $gr->getImageTag();
		}
	}
