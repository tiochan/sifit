<?php

/**
 * @author Anna Ribas (ana.ribas@upcnet.es)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage auth
 *
 * auth plugin for LDAP authentication
 */

/**
 * LDAP FIELDS:
 *
 * cn ( [0] => name.surname )
 * uid ( [0] => name.surname )
 * sn ( [0] => Name Surname1 Surname2 )
 * givenname ( [0] => Name )
 * aliesnom ( [0] => Name )
 * sn1 ( [0] => Surname1 )
 * sn2 ( [0] => Surname2 )
 * dnipassport ( [0] => 12345678A )
 * organization ( [0] => UPC, [1] => GRUPUPC ... )
 * suborganization ( [0] => UPC, [1] => UPCnet ... )
 * typology ( [0] => PAS, [1] => EST, [2] => PersExtern... )
 * unitcode ( [0] => 744, [1] => 300, [2] => 940... )
 * segmentation ( [0] =>  UPC-UPC-PAS-744, [1] => UPC-UPC-EST-300... )
 * unit ( [0] =>  ET, [1] => EETAC... )
 * cnb ( [0] =>  name surname1 surname2 )
 * idgauss ( [0] =>  01234567 )
 * birthdate ( [0] =>  01/01/1900 )
 * mail ( [0] =>  name.surname@mailer.com )
 * employeenumber ( [0] =>  12345678A )
 * ...
 *
 */

function ldap_get_all_users_info($cn_filter)
{
	$users = array();

	// Search user department via LDAP
	$ds = ldap_connect(ldapServer) or die("ERROR: Can't connect to LDAP server\n");			// Connect to ldap server

	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);

	$bn = ldapBindUser . "," . ldapUserBaseDn;
	$ldapbind = @ldap_bind($ds, $bn, ldapBindPasswd);

	if ($ldapbind) {

		// Search department's people
		$attr = array("sn", "cn", "unit");
		$filter = "(&(cn=*" . $cn_filter . "*))";

		$searchResult = @ldap_search($ds, ldapUserBaseDn, $filter, $attr);
		ldap_sort($ds, $searchResult, "cn");
		$info = ldap_get_entries($ds, $searchResult);

		//Now, to display the results we want:
		foreach ($info as $user) {

			$users[] = $user;
		}
	}

	return $users;
}

function ldap_search_user_by_filter($username, $filter)
{

	$user = ldap_search_users_by_group($filter);
	return in_array($username, $user);
}

function ldap_search_users($cn_filter)
{

	$users = array();

	// Search user department via LDAP
	$ds = ldap_connect(ldapServer) or die("ERROR: Can't connect to LDAP server\n");			// Connect to ldap server

	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);

	$bn = ldapBindUser . "," . ldapUserBaseDn;
	$ldapbind = @ldap_bind($ds, $bn, ldapBindPasswd);

	if ($ldapbind) {

		// Search department's people
		$attr = array("sn", "cn", "unit");
		$filter = "(&(cn=*" . $cn_filter . "*))";

		$searchResult = @ldap_search($ds, ldapUserBaseDn, $filter, $attr);
		ldap_sort($ds, $searchResult, "cn");
		$info = ldap_get_entries($ds, $searchResult);

		//Now, to display the results we want:
		foreach ($info as $user) {

			$cn = $user["cn"][0];
			$users[$cn] = array();

			$users[$cn]["cn"] = $cn;
			$users[$cn]["sn"] = $user["sn"][0];
			$users[$cn]["unit"] = $user["unit"][0];
		}
	}

	return $users;
}

function ldap_search_user_value($username, $attribute)
{

	// Search LDAP user
	$ds = ldap_connect(ldapServer) or die("ERROR: Can't connect to LDAP server\n");			// Connect to ldap server

	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);

	$bn = ldapBindUser . "," . ldapUserBaseDn;
	$ldapbind = @ldap_bind($ds, $bn, ldapBindPasswd);

	if ($ldapbind) {
		// Search person
		$attr = array($attribute);
		$filter = "(cn=$username)";
		$searchResult = ldap_search($ds, ldapUserBaseDn, $filter, $attr);
		$info = ldap_first_entry($ds, $searchResult);

		if ($info) {
			$value = ldap_get_values($ds, $info, $attribute);
			return $value[0];
		}
	}

	return NULL;
}

function ldap_search_users_by_group($filter)
{

	$users = array();

	// Search user department via LDAP
	$ds = ldap_connect(ldapServer) or die("ERROR: Can't connect to LDAP server\n");			// Connect to ldap server

	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);

	$bn = ldapBindUser . "," . ldapUserBaseDn;
	$ldapbind = @ldap_bind($ds, $bn, ldapBindPasswd);


	if ($ldapbind) {

		// Search group's members
		$attr = array("member");
		$searchResult = ldap_search($ds, ldapGroupBaseDn, $filter, $attr);
		$info = ldap_get_entries($ds, $searchResult);

		//Now, to display the results we want:
		$k = 0;
		for ($i = 0; $i < $info["count"]; $i++) {
			for ($j = 0; $j < $info[$i]["member"]["count"]; $j++) {
				$users[$k] = $info[$i]["member"][$j];
				$users[$k] = str_replace("cn=", "", $users[$k]);
				$users[$k] = str_replace(",ou=users,dc=upc,dc=edu", "", $users[$k]);
				$k++;
			}
		}
		sort($users);
	}

	return $users;
}

function ldap_search_users_by_department($username, $typology = 'PAS')
{

	global $USER_DOMAIN_NAME;

	$users = array();

	// Search user department via LDAP
	$ds = ldap_connect(ldapServer) or die("ERROR: Can't connect to LDAP server\n");			// Connect to ldap server

	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);

	$bn = ldapBindUser . "," . ldapUserBaseDn;
	$ldapbind = @ldap_bind($ds, $bn, ldapBindPasswd);

	if ($ldapbind) {

		// Search department's people
		//$attr = array("cn");
		$attr = array("*");
		$filter = "(&(unit=$USER_DOMAIN_NAME)(typology=$typology))";

		$searchResult = ldap_search($ds, ldapUserBaseDn, $filter, $attr);
		ldap_sort($ds, $searchResult, "cn");
		$info = ldap_get_entries($ds, $searchResult);
		//Now, to display the results we want:
		for ($i = 0; $i < $info["count"]; $i++) {
			$users[$i] = $info[$i]["cn"][0];
		}
	}

	// print_object($users);
	return $users;
}
