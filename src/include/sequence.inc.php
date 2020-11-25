<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage sequence
 *
 * Is not simply to determine if the page was served, if the user can open more than one page against
 * our application.
 *
 * To determine if the page was loaded before if:
 *   1. The form object add a hidden field <APP_NAME>_sequence, that contains the current load global_sequence number.
 *   2. This script is called from the init.inc.php
 *   3. The sequence is stored as a session var
 *   4. An associative array <APP_NAME>_sequence_array is stored as a session var.
 * How to know if this load is a re-load?
 * - Each time that this script is loaded, the session var global_sequence_number is incremented.
 * - If the post var <APP_NAME>_sequence is defined, the position <APP_NAME>_sequence of the session array
 *   <APP_NAME>_sequence_array is set to value 'executed'.
 * - To determine if this page was served, simply check for the value of the position of the array.
 *
 * Requires:
 * - init.inc.php to be loaded
 * - Requires session to be started
 */

class navigation_sequence
{

	private $current_sequence;
	private $form_sequence;
	private $reloaded;

	public function navigation_sequence()
	{

		// Get session sequence from session var, if defined, else, creates it.
		$this->init_session_sequence();

		// Get current form sequence number, comming from last page.
		$this->init_form_sequence();

		$this->init_is_reload();

		// Set form sequence as served.
		$this->store_current_sequence();

		// Increment the session sequence.
		$this->increment_session_sequence();
	}

	/**
	 * Return the current sequence number
	 *
	 * @return integer
	 */
	public function get_current_sequence()
	{
		return $this->current_sequence;
	}

	/**
	 * Return the sequence number of the form (if set)
	 *
	 * @return integer
	 */
	public function get_form_sequence()
	{
		return $this->form_sequence;
	}

	/**
	 * Tells if current page is a reload from a past served page.
	 *
	 * @return boolean
	 */
	public function is_reload()
	{

		return $this->reloaded;
	}

	public function debug()
	{
		echo "<hr>";
		echo "CURRENT_SEQUENCE: " . $this->get_current_sequence() . "<br>";
		echo "FORM SEQUENCE: " . $this->get_form_sequence() . "<br>";
		print_object($_SESSION[APP_NAME . '_sequence_array']);

		if ($this->is_reload()) {
			echo "This page is a RELOAD.<br>";
		} else {
			echo "This page is new.<br>";
		}
		echo "<hr>";
	}

	/**
	 * Returns the session sequence. If it is not initialized, creates it and set it to default value.
	 *
	 */
	private function init_session_sequence()
	{
		global $_SESSION;

		if (!isset($_SESSION[APP_NAME . '_sequence'])) {
			$_SESSION[APP_NAME . '_sequence'] = 1;
			if (isset($_SESSION[APP_NAME . '_sequence_array'])) unset($_SESSION[APP_NAME . '_sequence_array']);
			$_SESSION[APP_NAME . '_sequence_array'] = array();
		}

		$this->current_sequence = $_SESSION[APP_NAME . '_sequence'];
	}

	private function init_is_reload()
	{
		$this->reloaded = isset($_SESSION[APP_NAME . '_sequence_array'][$this->form_sequence]);
	}

	/**
	 * If current form comes from a submit, and the sequence is defined, get it from post var.
	 *
	 * @return integer
	 */
	private function init_form_sequence()
	{
		global $_POST;

		$this->form_sequence = isset($_POST[APP_NAME . '_sequence']) ? ($_POST[APP_NAME . '_sequence']) : 0;
	}

	/**
	 * Increments the session sequence.
	 *
	 */
	private function increment_session_sequence()
	{
		global $_SESSION;

		$_SESSION[APP_NAME . '_sequence'] = $_SESSION[APP_NAME . '_sequence'] + 1;
	}

	private function store_current_sequence()
	{

		if (!$this->form_sequence) return;

		$event = isset($_POST['event']) ? ($_POST['event']) : '';
		$element = isset($_POST['element']) ? ($_POST['element']) : '';

		$sep = ($event != "" and $element != "") ? "." : "";

		$_SESSION[APP_NAME . '_sequence_array'][$this->form_sequence] = "served: " . $element . $sep . $event;
	}
}
