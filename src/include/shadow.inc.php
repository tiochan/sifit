<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage shadow
 *
 * Shadow functions.
 *
 * Will be used to send to forms a shadowed IDs for rows IDs.
 * It uses the $_SESSION user session space to store the relation between the
 * shadowed IDs and real row IDs.
 *
 * Also, uses the sequence number to create new stores allowing more than one
 * window using the same form.
 *
 */

class shadow
{

	private $shadow_name;
	private $previous_shadow_name;
	private $shadow_initialized = false;

	private $previous_shadow;

	public function shadow($name)
	{

		global $global_nav_sequence;

		// Current shadow name
		// It is the given name plus the current sequence number:
		$nav_sequence = $global_nav_sequence->get_current_sequence();
		$this->shadow_name = $nav_sequence . "_" . $name;

		// Previous shadow name
		// It is the given name plus the sequence number given by POST
		$previous_nav_sequence = $global_nav_sequence->get_form_sequence();
		$this->previous_shadow_name = $previous_nav_sequence . "_" . $name;

		if (key_exists($this->previous_shadow_name, $_SESSION)) {
			// Store previous shadowed array, and delete it.
			$this->previous_shadow = $_SESSION[$this->previous_shadow_name];
			unset($_SESSION[$this->previous_shadow_name]);
		} else {

			// Doesn't exists previous shadow store
			$this->previous_shadow = array();
			$this->previous_shadow_name = "";
		}

		$this->shadow_initialized = key_exists($this->shadow_name, $_SESSION);
	}

	public function debug()
	{
		echo "shadow name: " . $this->shadow_name . "<br>";
		echo "previous shadow name: " . $this->previous_shadow_name . "<br>";
		echo "previous shadow array: <br>";
		print_object($this->previous_shadow);
	}

	private function init_shadow()
	{
		if (!key_exists($this->shadow_name, $_SESSION)) {
			$_SESSION[$this->shadow_name] = array();
		}
		$this->shadow_initialized = true;
	}

	/**
	 * Shadow row id functions
	 *
	 * get_shadow_id will return a new id for a row_id based on calcs.
	 *
	 * @param mixed $id
	 * @return string
	 */
	public function get_shadow_id($id)
	{
		$shadow_id = rand(10000, 20000) . "_" . rand(20000, 30000) . "_" . rand(30000, 40000) . $id;
		return $shadow_id;
	}

	/**
	 * Shadow row id functions
	 *
	 * add_shadowed_id will add a new association between an original row_id for a
	 * shaded row_id for a specific action.
	 *
	 * @param mixed $shadow
	 * @param string $action
	 */
	public function add_shadowed_id($row_id, $shadow_id, $action = "_default_")
	{

		if (!$this->shadow_initialized) $this->init_shadow();

		if (!key_exists($action, $_SESSION[$this->shadow_name])) {
			$_SESSION[$this->shadow_name][$action] = array();
		}

		$_SESSION[$this->shadow_name][$action][$shadow_id] = $row_id;
	}

	/**
	 * Shadow row id functions
	 *
	 * get_shadowed_id will return the original row_id associated for a
	 * previuos returned shaded row_id for a specific action.
	 *
	 * @param mixed $shadow
	 * @param string $action
	 * @return mixed
	 */
	public function get_shadowed_id($shadow, $action = "_default_")
	{

		if (!key_exists($action, $this->previous_shadow)) return false;
		if (!key_exists($shadow, $this->previous_shadow[$action])) return false;

		return $this->previous_shadow[$action][$shadow];
	}

	/**
	 * Reset shadow arrays for current class instance
	 *
	 */
	public function reset_shadow()
	{
		if (key_exists($this->shadow_name, $_SESSION)) {
			foreach ($_SESSION[$this->shadow_name] as $key => $value) {
				unset($_SESSION[$this->shadow_name][$key]);
			}
		}
	}
}
