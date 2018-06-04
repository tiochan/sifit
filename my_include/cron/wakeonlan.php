<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage alert validation process
 *
 * Search for alerts on status "doubt", pending for validations
 * Those which pass more days than max, will be automatically auto-validated.
 *
 */

	$AUTH_REQUIRED=true;
	$AUTH_LVL=0;			// SIGVI ADMINS ONLY
	$LOCATION_REQUIRED="127.0.0.1"; // Localhost

	/**
	 * This script can be called from command line or via Web.
	 *
	 * - Comman line (cli), from cron tasks or directly (from shell)
	 * - Web, forcing the load process.
	 *
	 */

	if(php_sapi_name() == "cli") {
		define("SYSHOME",dirname($_SERVER["PHP_SELF"]) . "/../../");
		include_once SYSHOME . "/include/init.inc.php";
	} else {
		include_once "../../include/init.inc.php";
	}

	if(CLI_MODE) define("QUIET",true);

	include_once INC_DIR . "/output.inc.php";
	include_once MY_INC_DIR . "/classes/wakeonlan.class.php";

	if(!CLI_MODE) html_header($MESSAGES["APP_NAME"]);

	wakeonlan();

	if(!CLI_MODE) {
		echo $output;
		html_footer();
	}
?>