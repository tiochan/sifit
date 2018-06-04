<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package admin
 *
 * Log-in page.
 */

	include_once "include/init.inc.php";


	if(file_exists(MY_INC_DIR . "/my_login.php")) {
		require_once MY_INC_DIR . "/my_login.php";
		exit;
	}



	$login_result=false;

	if(isset($_POST["next_page"])) {
		$next_page=stripslashes($_POST["next_page"]);
	} else {
		if(isset($_GET["next_page"])) {
			$next_page=stripslashes($_GET["next_page"]);
		} else {
			$next_page= HOME . "/index.php";
		}
	}

	function showForm($username, $error) {
		global $MESSAGES;
		global $next_page;

		html_showLogo(false);
		?>
		<center><h3><?php echo $MESSAGES["APP_LOGIN_TITLE"]; ?></h3></center>
		<center><font color=red><?php echo $error; ?></font></center>
		<br>
		<br>
		<form name="login" action=<?php echo $_SERVER["PHP_SELF"]; ?> method="post">
			<table border=0 align="center">
				<tr><td><?php echo $MESSAGES["APP_LOGIN_USERNAME"] ?></td><td><input type=text name="username" value="<?php echo $username; ?>"></td></tr>
				<tr><td>
					<?php echo $MESSAGES["APP_LOGIN_PASSWORD"] ?></td><td><input type=password name="password">
					<input type="hidden" name="next_page" value="<?php echo $next_page; ?>">
					<input type="hidden" name="first_time" value="0">
				</td></tr>
				<td><td>&nbsp;</td></tr>
				<tr><td></td><td><input type="submit" value=<?php echo $MESSAGES["APP_ACCEPT"] ?>></td></tr>
			</table>
		<?php
		if(defined("DEMO_VERSION") && DEMO_VERSION) {
		?>
			<table border=0 align="center">
				<tr><td><?php echo $MESSAGES["WANT_REGISTER"]; ?></td></tr>
			</table>
		<?php
		}
		?>
		</form>
		<script language="JavaScript" type="text/javascript">
		   document.forms.login.elements[0].focus();
		</script>
		<?php
	}

	// Is already logged?
	if(isset($_SESSION['logged']) and ($_SESSION['logged']==true)) {
		//echo "user already logged. Redirecting to... $next_page\n";
		header("Location: $next_page");
		exit;
	}

	////////////////////////
	// NOT LOGGED.

	// If first time, just show the login form
	if(!isset($_POST["first_time"])) {
		html_header("");
		showForm("", "");
		html_footer();
		exit;
	}

	// Not first time, Are vars defined?
	if(isset($_POST["username"]) and isset($_POST["password"])) {
		$login_result=authenticate($_POST["username"], $_POST["password"]);
	}

	// Ok, authentication succesful, redirect to previous page if defined, else Home.
	if($login_result) {
		//header("Location: $next_page", false);
		html_showInfo("User logged, redirecting...\n");
		echo "<script>self.location.href='$next_page'</script>";
		exit;
	}

	// Something wrong... try again
	html_header("");
	showForm($_POST["username"], $MESSAGES["AUTH_INVALID_AUTH"]);
	html_footer();
?>
