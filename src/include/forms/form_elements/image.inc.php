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
 * Creates a button
 *
 */
class form_image extends form_element
{
	public $alt;
	public $src;

	/**
	 * button constructor
	 *
	 * @param string $name, internal button name
	 * @param string $text, the text to display
	 * @return button
	 */
	function form_image($name, $src, $alt = "")
	{
		parent::form_element($name);
		$this->alt = $alt;
		$this->src = $src;
	}

	function show()
	{
		parent::show();

		if (!$this->visible) return;

		echo "<center><img alt='" . $this->alt . "' src='" . $this->src . "'/></center>";
	}

	function event($event_type)
	{
		return $this->clicked();
	}

	// Abstract
	function clicked()
	{
		return 0;
	}
}
