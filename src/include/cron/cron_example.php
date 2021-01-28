<?php
/*
	Author: Sebastian Gomez, (tiochan@gmail.com)
	For: Politechnical University of Catalonia (UPC), Spain.

	Cron script example
	You own scripts must be placed at my_include/cron
*/

define("CLI_REQUIRED", true);
define("QUIET", true);

$dir = dirname($_SERVER["PHP_SELF"]);
define("SYSHOME", $dir . "/../..");

include_once SYSHOME . "/include/init.inc.php";

$return_code = 0;			// 0: ok, [1,2, ...] : error

echo "\n\nProcess started at " . date("Y-m-d h:i:s") . "\n";
echo "----------------------------------------------------\n\n";

/*
	 * DO WHATEVER YOU NEED TO DO
	 *
	 * $return_code can be set to the exit status (0 is Ok).
	 *
	 */

/*
	 * Perhaps you want to send a report about execution:
	 *
	 * 	include_once SYSHOME . "/include/mail.inc.php";
	 *
	 * 	$subject= "<any subject>";
	 *  $content= "The content to send"
	 * 	// Now send mail to admins.
	 * 	send_group_mail(0, $subject,$content, "html");
	 */

echo "\n\n----------------------------------------------------\n";
echo "Process finished at " . date("Y-m-d h:i:s") . "\n";
exit($return_code);
