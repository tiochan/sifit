<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage init
 *
 * Environment init
 *
 * This is the main script that every other must include at the start.
 * This script also performs some tasks dependint on vars and constants defined
 * before it is included.
 *
 * will create some important constants like:
 *
 *   - var $global_db, (unless NO_DDBB const is defined and set to true):
 *     will create a connection to the default database defined at
 *     app.conf.php. This var is the object instance of dbms_class.
 *
 *   - var $global_nav_sequence, and $global_nav_control,if you need to use
 *     the SESSION sequence and form sequence into your scripts.
 *     (like shadow.inc.php, or forms.inc.php)
 *
 *   - const SYSHOME, which contains the absolute path to the root directory
 *     of the application.
 *
 *   - const CLI_MODE, if true, means that the call is made via command
 *     line.
 *
 *   - const INC_DIR, TPL_DIR, MY_INC_DIR, MY_TPL_DIR, containing the
 *     absolute path to each of the directories: include, include/templates,
 *     my_include, my_include/templates.
 *
 *   - const IMAGES, ICONS, MY_IMAGES, MY_ICONS, , containing the absolute
 *     path to each of the directories: include/images, include/images/icons
 *     , my_include/images, my_include/images/icons.
 *
 *   - User information variables (if auth is required and user is logged):
 *     $USER_NAME, $USER_REAL_NAME, $USER_LEVEL, $USER_LEVEL_NAME, $USER_GROUP,
 *     $USER_GROUP_NAME, $USER_ID, $USER_LANG, $USER_EMAIL
 *
 *
 * Depending on the content or definition of some variables and constants
 * will change the execution flow as follows:
 *
 *   - MAINTENANCE: defined via app.conf.php. If set to true, then will stop
 *     the rest of the execution.
 *
 *   - const CLI_REQUIRED: if set, will check that the call is made via
 *     command line.
 *
 *   - var $AUTH_REQUIRED: if set, then will check if user is logged or not.
 *     if not logged will redirect to the login page.
 *
 *   - var $AUTH_LVL: if set, will check if user level grants the page
 *     needed level
 *
 *   - var $LOCATION_REQUIRED: if set, will check that call is set from the
 *     defined IP on this var.
 *
 *   - const QUIET: if set, will not echo anything. Useful for scripts
 *
 *   - NO_DDBB: if set to true, will not create a connection to the default
 *     database defined at app.conf.php.
 *
 */

date_default_timezone_set("Europe/Madrid");


////////////////////////////////////////////////////////////////////////////
// DEFINITION OF BASE CONSTANTS AND GLOBAL VARS
//

// Is called from command line interpreter?
$CLI_MODE = (php_sapi_name() == "cli");
define("CLI_MODE", $CLI_MODE);

if (defined("CLI_REQUIRED") and CLI_REQUIRED and !CLI_MODE) die("Must be executed using a command line interpreter.");

// Global paths definition
if (!defined("SYSHOME")) define("SYSHOME", dirname(dirname(__FILE__)));

define("INC_DIR", SYSHOME . "/include/");			// Include directory
define("MY_INC_DIR", SYSHOME . "/my_include/");		// Application Include directory
define("TPL_DIR", INC_DIR . "templates/");			// Templates directory
define("MY_TPL_DIR", MY_INC_DIR . "templates/");	// Application Templates directory

// System configuration
include_once SYSHOME . "/conf/app.conf.php";

if (file_exists(MY_INC_DIR . "/conf/app.conf.php")) include_once MY_INC_DIR . "/conf/app.conf.php";

setlocale(LC_ALL, APP_DEFAULT_LOCALE);
/*
	// Locales and monetary initialization
	setlocale(LC_ALL, APP_LC_LOCALE);

	$locales=localeconv();
	define("MON_DECIMAL_SEP",$locales["mon_decimal_point"]);
	define("MON_THOUSAND_SEP", $locales["mon_thousands_sep"]);
	define("MON_SYMBOL", $locales["currency_symbol"]);
*/
define("IMAGES", HOME . "/include/images");	// Images directory, Web reference
define("ICONS", IMAGES . "/icons");			// Icons directory, Web reference
define("MY_IMAGES", HOME . "/my_include/images");	// Images directory, Web reference
define("MY_ICONS", MY_IMAGES . "/icons");			// Icons directory, Web reference

include_once SYSHOME . "/include/functions.php";	// Generic functions
if (file_exists(SYSHOME . "/my_include/functions.php")) include_once SYSHOME . "/my_include/functions.php";	// Generic user functions
global $MESSAGES;
include_once SYSHOME . "/include/lang/lang.inc.php"; // Language configuration

//	if(defined("MAINTENANCE") && MAINTENANCE && $_SERVER['REMOTE_ADDR']!="127.0.0.1" && $_SERVER['REMOTE_ADDR']!=$_SERVER['SERVER_ADDR'] && $_SERVER['REMOTE_ADDR']!="147.83.198.148") {
global $MAINTENANCE_ALLOWED_HOSTS;
if (defined("MAINTENANCE") && MAINTENANCE && (in_array($_SERVER['REMOTE_ADDR'], $MAINTENANCE_ALLOWED_HOSTS) === false)) die($MESSAGES["MAINTENANCE_MESSAGE"]);

// Database interface
if (!(defined("NO_DDBB") and NO_DDBB)) {
	require_once SYSHOME . "/include/dbms/" . DBType . ".class.php";
	$dbtype = DBType . "_class";
	$global_db = new $dbtype() or die("ERROR: couldn't create DBMS Object");
	$global_db->dbms_connect(DBServer, DBUser, DBPass, false) or die("ERROR: couldn't connect to database server");
	$global_db->dbms_select_db(DBName) or die("ERROR: couldn't connect to database " . DBName);
}


////////////////////////////////////////////////////////////////////////////
// ADDITIONAL PACKAGES
//

if (!CLI_MODE and defined("CRONO_ENABLED") and CRONO_ENABLED) require_once SYSHOME . "/include/crono.inc.php";
require_once SYSHOME . "/include/html.inc.php";			// HTML functions
include_once SYSHOME . "/include/log.inc.php";			// Audit package

if (!CLI_MODE) {
	// Authentication
	if (isset($LOCATION_REQUIRED) and ($_SERVER['REMOTE_ADDR'] != $LOCATION_REQUIRED)) {
		html_showNoaccess();
		log_write("AUTH", "Access denied to remote address", 0);
		exit;
	}

	// Authentication
	if (file_exists(MY_INC_DIR . "/auth.inc.php")) require_once MY_INC_DIR . "/auth.inc.php";
	else require_once INC_DIR . "/auth.inc.php";
	session_start();

	if (get_http_param("hide_header")) {
		$_SESSION["hide_header"] = true;
		define("HIDE_HEADER", true);
	} elseif (isset($_SESSION["hide_header"])) {
		define("HIDE_HEADER", true);
	}

	include_once SYSHOME . "/include/sequence.inc.php";
	include_once SYSHOME . "/include/navigation.inc.php";
	global $global_nav_sequence, $global_nav_control;
	$global_nav_sequence = new navigation_sequence();
	$global_nav_control = new navigation();

	// Recover session variables
	if (is_user_logged()) {
		$USER_REAL_NAME = $_SESSION[APP_NAME . '_realname'];
		$USER_NAME = $_SESSION[APP_NAME . '_username'];
		$USER_LEVEL = $_SESSION[APP_NAME . '_level'];
		$USER_LEVEL_NAME = $MESSAGES["SKILL_" . $USER_LEVEL];
		$USER_GROUP = $_SESSION[APP_NAME . '_group'];
		$USER_GROUP_NAME = $_SESSION[APP_NAME . '_group_name'];
		$USER_ID = $_SESSION[APP_NAME . '_id_user'];
		$USER_LANG = $_SESSION[APP_NAME . '_def_lang'];
		$USER_EMAIL = isset($_SESSION[APP_NAME . '_user_email']) ? $_SESSION[APP_NAME . '_user_email'] : "";

		// Load default language for this user
		load_language($USER_LANG);

		if (file_exists(MY_INC_DIR . "/classes/user.class.php")) {
			include_once MY_INC_DIR . "/classes/user.class.php";
		} else {
			include_once INC_DIR . "/classes/user.class.php";
		}

		$CURRENT_USER = new user($USER_ID);

		// Check for access restrictions
		if (isset($AUTH_LVL) and $AUTH_LVL < $USER_LEVEL) {
			register_shutdown_function("end_function");
			html_showHeader();
			html_showNoaccess();
			exit;
		}
	} else {
		if (isset($AUTH_REQUIRED) and $AUTH_REQUIRED) {
			header("Location: " . HOME . "/login.php?next_page=" . $_SERVER['PHP_SELF']);
			exit;
		}
		unset($USER_NAME);
		unset($USER_LEVEL);
		unset($USER_GROUP);

		$USER_LEVEL = 10000;
	}

	if (strpos($_SERVER['PHP_SELF'], "logs.php") === false) {
		log_write("NAVIGATION", "User loaded " . $_SERVER['PHP_SELF'], 5);
	}

	if (DEBUG == true) error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
	else error_reporting(0);

	if (defined("QUIET") and (QUIET === true)) {
	} else {
		register_shutdown_function("end_function");
		html_showHeader();
	}
}

if (file_exists(SYSHOME . "/my_include/my_init.inc.php")) include_once SYSHOME . "/my_include/my_init.inc.php";

function end_function()
{
	html_showScripts();
	html_showAdds();
	echo "\n\n</body>\n</html>";
}

function db_connect(&$db)
{
	$dbtype = DBType . "_class";
	$db = new $dbtype() or die("ERROR: couldn't create DBMS Object");
	$db->dbms_connect(DBServer, DBUser, DBPass, false) or die("ERROR: couldn't connect to database server");
	$db->dbms_select_db(DBName) or die("ERROR: couldn't connect to database " . DBName);

	return $db;
}

// $global_nav_sequence->debug();
