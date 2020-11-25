<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage navigation
 *
 *
 * Requires:
 * - init.inc.php to be loaded
 * - Requires session to be started
 */

class url_info
{
	public $URL;
	public $POST;
	// public $GET;

	public function url_info()
	{
		$this->URL = $_SERVER['REQUEST_URI'];
		$this->POST = $_POST;
		// $this->GET= $_GET;
	}
}

/**
 * navigation is the class which controls the status of each page loaded.
 *
 */
class navigation
{
	public $nav;

	public function navigation()
	{

		// Restore navigation information
		if (isset($_SESSION[APP_NAME . "nav_store"])) {
			$this->nav = unserialize($_SESSION[APP_NAME . "nav_store"]);
		} else {
			$this->nav = array();
		}

		if (basename(($_SERVER['SCRIPT_FILENAME'])) == "show_page.php") {
			$this->nav[] = new url_info();
		}
		include_once "functions.php";

		$this->store();

		$event = isset($_POST["event"]) ? $_POST["event"] : "";
		if ($event == "form_cancel") {
			$this->back(1);		// This function gives implicitly an exit.
			return;
		}
	}

	public function back($pos)
	{

		/*
				$total_states is the total number of states

				$total_states -0	Actual state
			  	$total_states -1  	Previous state, wich creates the state
			  	$total_states -2  	First state where we can go to..
			  	$total_states -3  	second state where we can go to
			  	...

			  	If $pos is referencing 1, will be redirected to 1 (+2) = 3rd position.
			 */

		$pos += 2;

		$new_pos = count($this->nav) - $pos;
		if ($new_pos < 0) die("navigation::back: Error redirecting to an unknown situation.");

		$url = $this->nav[$new_pos]->URL;
		$data = $this->nav[$new_pos]->POST;

		/*
			// Now, all navigation data up to this position must be deleted:
			for($i= count($this->nav); $i >= $pos; $i--) {
				unset($this->nav[$i]);
			}
			*/
		$this->store();
		$this->post_it($url, $data);
	}

	private function store()
	{
		//unset($_SESSION[APP_NAME . "nav_store"]);
		$_SESSION[APP_NAME . "nav_store"] = serialize($this->nav);
	}

	private function post_it($url, $data)
	{
		ob_end_clean();

		echo "
			<html>
			<body onload='document.forms.form_base.submit()'>
			<form name='form_base' action='$url' method='post'>
			";

		foreach ($data as $key => $value) {
			if ($key == "event") $value = "";
			if ($key == "element") $value = "";
			echo "<input type='hidden' name='$key' value='$value'>\n";
		}

		echo "
			</form>

			</body>
			</html>
			";
		exit;			// Exit the program..
	}
}
