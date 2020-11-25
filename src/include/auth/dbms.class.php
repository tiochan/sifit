<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage auth
 *
 * auth plugin for alternative dbms authentication
 */

include "auth.class.php";

class dbms extends auth_method
{

	function __construct()
	{
		// Your code
	}

	final function authenticate($username, $password, $level)
	{

		$status = false;

		// Your code

		return $status;
	}
}
