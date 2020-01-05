<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package conf
 * 	Main application configuration.
 *  You can edit this file manually or using the tools/config.php utility
 */


/*******************************************************************************
 *	Instance location configuration
 *******************************************************************************
 EDIT EDIT EDIT EDIT EDIT EDIT EDIT EDIT EDIT EDIT EDIT EDIT EDIT EDIT EDIT EDIT
 *******************************************************************************
 * Change it to configure:
 *
 * - (*) HOME: By default is set to /sigvi, put your web server direction.
 *         If the application is set at root dir of your web server, don't set
 *         it to "/", just put "" instead.
 *
 * - (*) SERVER_URL: Just the URL that you have to put into your web navigator.
 *         This parameter is used to build the links to your SIGVI instance into
 *         emails and reports.
 *
 * - (*) ADM_EMAIL: Is the system administrator email address, to notify of
 *         results from system processes.
 *
 * - APP_NAME: If you have more than one instance of SIGVI into the same server
 *
 ******************************************************************************/

define("HOME","/sigvi");                                                // EDIT
define("SERVER_URL","http://localhost");                                // EDIT
define("ADM_EMAIL","sigvi <root@localhost>");                           // EDIT
define("APP_NAME","SIGVI");

define("APP_DESCRIPTION","SIGVI R2 Enterprise");
define("APP_VERSION","SIGVI R2 Enterprise 1.9.20 RC4");


/*******************************************************************************
 *	DATABASE Configuration
 *******************************************************************************
 * - DBType [ mysql | mysqli | postgres | oracle ... ]
 *         The type of database to use. See /include/dbms directory for
 *         supported dbms.
 *
 ******************************************************************************/

define("DBType","mysqli");
define("DBServer","localhost");
define("DBName","sigvi");
define("DBUser","sigvi");
define("DBPass","sigvi05");


/*******************************************************************************
 * General Configuration
 *******************************************************************************
 *
 * Set as your needings:
 *
 * - MAINTENANCE [true|false]: Disable application. Nobody would use it until is
 *         set to true.
 *         Its default value is false.
 *
 * - DEVELOPMENT [true|false]: If you set it to true, a "under construction" bar
 *         is shown on each page.
 *         Its default value is false.
 *
 * - DEMO_VERSION [true|false]: If set to true, free registration will be
 *         available from login window.
 *         Its default value is false.
 *
 * - BUG_TRACKING [true|false]: Usually for demo versions. If set to true, will
 *         activate the bug management option and menus. Also you can use this
 *         feature for your own business.
 *         Its default value is false.
 *
 * - DEBUG [true|false]: For development purposes. Will enable all messages from
 *         PHP preprocessor.
 *         Its default value is false.
 *
 * - DEBUG_QUERY [true|false]: Also for development purposes. High debugging
 *         option to examine query performance. Enable only if necessary.
 *         Its default value is false.
 *
 * - AUDIT [true|false]: Enable users interaction log?
 *         Its default value is true.
 *
 * - AUDIT_LEVEL [0..5]: If AUDIT is set to true, you can define the audit
 *         level. Each level means:
 *
 *         - 0 - only user autentication
 *         - 1 - + datawindows and system files (if exists) changes
 *         - 2 - + dbms query
 *         - [2-4] - free log levels
 *         - 5 - Log all events (includes navigation)
 *         Its default value is 0.
 *
 * - DATE_FORMAT [ d-m-y | m-d-y | y-m-d ]: Set your date format.
 *          Its default value is "y-m-d".
 *
 ******************************************************************************/

define("MAINTENANCE",false);
define("DEVELOPMENT",false);
define("DEMO_VERSION",false);
define("BUG_TRACKING",true);
define("DEBUG",false);
define("DEBUG_QUERY", false);
define("AUDIT",true);
define("AUDIT_LVL",0);
define("DATE_FORMAT","y-m-d");

/*******************************************************************************
 *	A little bit about appearance
 *******************************************************************************
 * Change it to configure:
 *
 * - CRONO_ENABLED [true|false]: If true, the generation time will be shown on
 *         the page footer.
 *
 * - LANG: Default language: [ cat | en | es | fr ], see include/lang subdir.
 *
 * - APP_THEME: name of the default theme. It will be searched under
 *         my_include/themes and include/themes (in this order). Users can
 *         customize it on their own config.
 *
 * - SHOW_LOGOS [true|false]: Corporative logos Will be shown on the page
 *         footer.
 *
 * - APP_LOGO and APP_MINI_LOGO: are the SIGVI logos. Those must not be changed.
 *
 * -
 *
 ******************************************************************************/

define("CRONO_ENABLED",true);
define("LANG","en");
define("SHOW_LOGOS",false);
define("APP_THEME","default");
define("APP_LOGO","/my_include/images/logoApp.png");
define("APP_MINILOGO","/my_include/images/logoAppMini.png");


/*******************************************************************************
 *	User authentication
 *******************************************************************************
 *
 * This part is OPTIONAL.
 *
 * If you want to use your own validation systems like ldap, you must define it
 * here.
 *
 * When you create a user which is defined as "external" you must set which type
 * of autentication will be used to check their password.
 *
 * If this validation has not a scrip (placed on directory include/auth) you
 * will have to create your own under this directory.
 *
 * If you define all users as "not external", then their password will be
 * checked with their own at the users table (as usual).
 ******************************************************************************/
define("authMethod","dbms");	// [ ldap | dbms | <define your own> ]
define("ldapServer","ldaps://your.ldap.server");
define("ldapBaseDn","ou=users,dc=your_dc");

// Don't change this:
$GLOBALS["config_included"]="yes";		// Don't delete this sencence !!!!!!!
?>