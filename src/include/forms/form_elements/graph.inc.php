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

/**
 * Creates a graph
 *
 */
class form_graph extends form_element
{
	public $alt;
	public $class;
	public $graph;

	/**
	 * button constructor
	 *
	 * @param string $name, internal button name
	 * @param string $class, the class which manage this kind of image
	 * @param string $alt, the text to display as ALT for image
	 * @return form_graph
	 */
	function form_graph($name, $class, $alt = "")
	{
		parent::form_element($name);
		$this->alt = $alt;
		$this->graph = $class;

		$this->class = new $class();
	}

	function show()
	{
		parent::show();

		if (!$this->visible) return;

		$tag = $this->class->getImageTag();
		echo $tag;
	}
}
