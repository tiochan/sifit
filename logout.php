<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package admin
 *
 * Logout page.
 */


	include_once "include/init.inc.php";

	global $GLOBAL_SCRIPTS;

	$my_function="setTimeout(\"self.location.href='index.html'\", 2000)";
	$GLOBAL_SCRIPTS[]= $my_function;


	end_session();

	html_header("");
//	html_showLogo();
?>
		<br>
		<br>
		<center><h3><?php echo $MESSAGES["APP_LOGOUT_TITLE"]; ?></h3></center>
<?php
	html_footer();
?>
