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

include_once INC_DIR . "/reports/graphs/generic_graph_pie.php";


class generic_graph_pie_perc extends generic_graph_pie
{

	public function generic_graph_pie_perc($value, $extra_info, &$db)
	{

		$this->percent = true;
		parent::generic_graph_pie($value, $extra_info, $db);
	}
}
