<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage forms
 *
 */

include_once SYSHOME . "/include/forms/form_elements.inc.php";
include_once INC_DIR . "/reports/tags.class.php";


/**
 * Creates a graph
 *
 */
class form_report_tag extends form_element
{

	public $tag_instance;

	/**
	 * button constructor
	 *
	 * @param string $name, internal button name
	 * @param string $class, the class which manage this kind of image
	 * @param string $alt, the text to display as ALT for image
	 * @return form_graph
	 */
	function form_report_tag($tag_name)
	{
		parent::form_element($tag_name);

		$this->tag_instance = new tag($tag_name);
	}

	function show()
	{

		parent::show();

		if (!$this->visible) return;

		echo $this->tag_instance->get_value();
	}
}
