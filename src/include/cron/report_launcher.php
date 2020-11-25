<?php

/**
 * @author Jorge Novoa (jorge.novoa@upcnet.es)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package discoverer
 * @subpackage cron
 *
 * Script to detect launchs
 */


define("CLI_REQUIRED", true);

$dir = dirname($_SERVER["PHP_SELF"]);
define("SYSHOME", $dir . "/../..");


include_once SYSHOME . "/include/init.inc.php";
include_once INC_DIR . "/reports/reports.class.php";


//$report= new reports_to_launch();
$subscriptions = new subscriptions_to_launch();
$subscriptions->launch();
