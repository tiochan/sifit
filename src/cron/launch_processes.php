<?php
/**
 * @author Jorge Novoa (jorge.novoa@upcnet.es)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage cron
 *
 * Script to detect launchs
 */


	define("CLI_REQUIRED",true);

	$dir= dirname($_SERVER["PHP_SELF"]);
	define("SYSHOME", $dir . "/..");


	include_once SYSHOME . "/include/init.inc.php";
	include_once SYSHOME . "/include/mail.inc.php";

	include_once INC_DIR . "/classes/tasks.class.php";

	$tsk= new processes_to_launch();
	$results= $tsk->launch();

	if($results != "") {
		send_admins_mail("Summary of Task Manager","Summary of Task Manager",$results, "html");
	}

?>
