<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage auth
 *
 * Authentication functions.
 *
 */ ($GLOBALS["config_included"] == "yes") or die("ERROR: Config file not loaded\n");

// http_authenticate: deprecated.
// Actually using the login.php script to get username/password.
// Its here for historical reasons.
function http_authenticate($msg)
{
	global $MESSAGES;

	Header("WWW-authenticate: basic realm=\"SIGVI\"");
	// Header("HTTP/1.0   401 Unauthorized");

	html_header($msg);
	echo "<p>" . $MESSAGES["AUTH_USER_REQUIRED"] . "</p>";
	html_footer();
	exit;
}

/**
 * Look for this user as internal user in our sigvi.user table.
 */
function is_user_registered($username, $password, &$id_user, &$name, &$external, &$level, &$group, &$group_name, &$def_lang, &$email)
{

	global $global_db;

	$ok = false;

	$md5_password = md5($password);
	$query = "
			select u.id_user, u.name, u.external, u.level, u.id_group, u.lang, g.name, u.email
			from users u, groups g
			where username='$username' and
				u.id_group = g.id_group and
				(
					( external = 0 and password='$md5_password' ) or
					( external = 1 )
				)
			";

	$res = $global_db->dbms_query($query);

	if ($global_db->dbms_check_result($res)) {
		$row = $global_db->dbms_fetch_row($res);
		$id_user = $row[0];
		$name = $row[1];
		$external = $row[2];
		$level = $row[3];
		$group = $row[4];
		$def_lang = $row[5];
		$group_name = $row[6];
		$email = $row[7];

		$global_db->dbms_free_result($res);
		$ok = true;
	}

	return $ok;
}

// MAIN
function authenticate($username, $password)
{

	global $global_db;

	$ok = false;

	if (is_user_registered($username, $password, $id_user, $name, $external, $level, $group, $group_name, $def_lang, $email)) {

		if ($external == 1) {

			$auth_file = SYSHOME . "/include/auth/" . authMethod . ".class.php";
			if (!file_exists($auth_file)) {
				$auth_file = SYSHOME . "/my_include/auth/" . authMethod . ".class.php";
			}
			(include_once $auth_file) or die("Authentication method " . authMethod . " is not defined");

			$className = authMethod;
			$auth = new $className();
			$ok = $auth->authenticate($username, $password, $level);
		} else {
			$ok = true;
		}

		if ($ok) {
			$_SESSION[APP_NAME . '_realname'] = $name;
			$_SESSION[APP_NAME . '_username'] = $username;
			$_SESSION[APP_NAME . '_level'] = $level;
			$_SESSION[APP_NAME . '_group'] = $group;
			$_SESSION[APP_NAME . '_group_name'] = $group_name;
			$_SESSION[APP_NAME . '_id_user'] = $id_user;
			$_SESSION[APP_NAME . '_def_lang'] = $def_lang;
			$_SESSION[APP_NAME . '_logged'] = true;
			$_SESSION[APP_NAME . '_remote_addr'] = $_SERVER['REMOTE_ADDR'];
			$_SESSION[APP_NAME . '_user_email'] = $email;
		} else {
			// Action?
		}
	}

	if ($ok) {
		log_write("AUTH", "User logged in: $username", 0);
	} else {
		log_write("AUTH", "User authentication failed (username: $username)", 0);
	}

	return $ok;
}

function is_user_logged()
{
	return (isset($_SESSION[APP_NAME . "_logged"]) and ($_SESSION[APP_NAME . "_logged"] == true) and ($_SESSION[APP_NAME . '_remote_addr'] == $_SERVER['REMOTE_ADDR']));
}

function end_session()
{
	log_write("AUTH", "User logged out", 0);
	unset($_SESSION[APP_NAME . '_logged']);
	unset($_SESSION[APP_NAME . '_username']);
	unset($_SESSION[APP_NAME . '_level']);
	unset($_SESSION[APP_NAME . '_group']);
	unset($_SESSION[APP_NAME . '_id_user']);
	unset($_SESSION[APP_NAME . '_remote_addr']);
	// Use it only if this application is on a dedicated server (or virtual host)
	// Else you can destroy all the data associated with this user on other applications.
	session_destroy();
}
