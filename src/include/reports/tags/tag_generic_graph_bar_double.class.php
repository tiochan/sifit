<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 *
 * @package sifit
 * @subpackage reports
 *
 * Tags definition class
 *
 */

include_once INC_DIR . "/reports/tags/tag_generic_graph.class.php";


class tag_generic_graph_bar_double extends tag_generic_graph
{

	public function get_value()
	{

		global $USER_ID;

		$this->replace_parameters();

		$grapher = INC_DIR . "/reports/graphs/generic_graph_bar_double.php";
		if (!file_exists($grapher)) return "** Error: Can't locate generic grapher **";

		include_once $grapher;

		$gr = new generic_graph_bar_double($this->value, $this->parameters, $this->db);
		return $gr->getImageTag();
	}
}
