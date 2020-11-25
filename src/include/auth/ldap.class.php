<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage auth
 *
 * auth plugin for LDAP authentication
 */

include_once "auth.class.php";


class ldap extends auth_method
{

	function __construct()
	{
		!defined("ldapServer") and die("LDAP method is not yet configured at configuration file.");
	}

	final function authenticate($username, $password, $level)
	{

		$ok = false;

		// Check password via LDAP
		$ds = ldap_connect(ldapServer) or die("ERROR: Can't connect to LDAP server\n");			// Connect to ldap server

		ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);

		$bn = "cn=$username," . ldapUserBaseDn;
		$ldapbind = @ldap_bind($ds, $bn, $password);

		if ($ldapbind) {
			$searchResult = ldap_search($ds, ldapUserBaseDn, 'cn=' . $username);
			$info = ldap_get_entries($ds, $searchResult);
			if ($info["count"] > 0) {
				$ok = true;
			}
		}
		return $ok;
	}
}
