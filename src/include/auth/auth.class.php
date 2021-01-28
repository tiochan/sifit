<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage auth
 *
 * Abstract authentication class definition.
 */

abstract class auth_method
{
	abstract public function authenticate($username, $password, $level);
}
