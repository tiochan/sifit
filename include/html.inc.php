<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage html
 *
 * Required:
 * - init.inc.php loaded.
 * - lang.php loaded.
 */

	include_once INC_DIR . "/tpl.inc.php";

	/**
	 * $GLOBAL_SCRIPTS
	 * This var contains all the javascript functions that will be created on the script section of the page sent.
	 */
	global $GLOBAL_SCRIPTS;
	global $GLOBAL_HEADERS;
	global $GLOBAL_STYLES;
	global $GLOBAL_RECONFIGURE_PAGE;

	if(!isset($GLOBAL_SCRIPTS))	$GLOBAL_SCRIPTS= Array();
	if(!isset($GLOBAL_HEADERS))	$GLOBAL_HEADERS= Array();
	if(!isset($GLOBAL_STYLES))	$GLOBAL_STYLES= Array();
	if(!isset($GLOBAL_RECONFIGURE_PAGE)) $GLOBAL_RECONFIGURE_PAGE= Array();


	function html_showHeader() {

		if(defined("CLI_MODE") and CLI_MODE) return;

		tpl_include("/header.tpl.php");
	}

	function html_showSimpleBody() {

		if(defined("CLI_MODE") and CLI_MODE) return;

		tpl_include("simplebody.tpl.php");
	}

	function html_showBody($title, $info_page="") {

//		tpl_include("body.tpl.php");

		html_showMainFrameHeader($title, $info_page);
	}

	function html_showMainFrameHeader($title, $info_page="") {
		global $tpl_title;
		global $tpl_info_page;

		global $OVERRIDE_TPL;

		$tpl_title= $title;
		$tpl_info_page= $info_page;

		if(defined("CLI_MODE") and CLI_MODE) return;

		if(isset($OVERRIDE_TPL) and ($OVERRIDE_TPL != "")) {
			tpl_include($OVERRIDE_TPL);
		} else {
			tpl_include("mainframeheader.tpl.php");
		}
	}

	function html_header($title, $info_page="") {

		if(defined("CLI_MODE") and CLI_MODE) return;

		if(isset($GLOBALS["header_sent"])) {
			return;
		} else {
			$GLOBALS["header_sent"]=1;
		}

		if(defined("CRONO_ENABLED") and CRONO_ENABLED) crono_start();

		//html_showHeader();  Now called from init.inc.php

		if(isset($GLOBALS["is_master"]) or isset($GLOBALS["is_detail"])) {
			html_showSimpleBody();
		} else {
			html_showBody($title, $info_page);
		}
	}

	function html_showAdds() {
		global $GLOBAL_HEADERS;

		if(!count($GLOBAL_HEADERS)) return;

		foreach($GLOBAL_HEADERS as $header) echo $header . "\n";
	}

	function html_showScripts() {
		global $GLOBAL_SCRIPTS;

		if(count($GLOBAL_SCRIPTS) == 0) return;

		echo "\n<script language='javascript'>\n";
		foreach($GLOBAL_SCRIPTS as $SCRIPT) echo $SCRIPT . "\n";
		echo "</script>\n";
	}

	function html_footer() {

		if(defined("CLI_MODE") and CLI_MODE) return;


		if(isset($GLOBALS["footer_sent"])) {
			return;
		} else {
			$GLOBALS["footer_sent"]=1;
		}

		if(isset($GLOBALS["is_master"]) or isset($GLOBALS["is_detail"])) {
			html_showSimpleFooter();
		} else {
			html_showFooter();
		}
	}

	function html_showSimpleFooter() {

		if(defined("CLI_MODE") and CLI_MODE) return;

//		echo "</body></html>";
	}

	function html_showMainFrameFooter() {

		if(defined("CLI_MODE") and CLI_MODE) return;

		tpl_include("mainframefooter.tpl.php");
	}

	function html_showFooter() {

		if(defined("CLI_MODE") and CLI_MODE) return;

		html_showMainFrameFooter();
		tpl_include("footer.tpl.php");

	}

	function html_showLogo($center=true) {
		global $MESSAGES;

		if(defined("CLI_MODE") and CLI_MODE) return;

		if($center) {
		?>
			<div align="center"><img src="<?php echo HOME . APP_LOGO; ?>" alt="<?php echo $MESSAGES["APP_NAME"]; ?>" class=loginlogo></div>
		<?php
		} else {
		?>
			<div align="center"><img src="<?php echo HOME . APP_LOGO; ?>" alt="<?php echo $MESSAGES["APP_NAME"]; ?>" class=loginlogo></div>
		<?php
		}
	}

	function html_showMiniLogo() {
		global $MESSAGES;

		if(defined("CLI_MODE") and CLI_MODE) return;

	?>
		<div align=left><img src="<?php echo HOME . APP_MINILOGO; ?>" alt="<?php echo $MESSAGES["APP_NAME"]; ?>"></div>
	<?php
	}

	function html_showNoaccess() {
		global $MESSAGES;

		if(defined("CLI_MODE") and CLI_MODE) return;

		html_header("");
		//html_showLogo();
	?>
		<script language="javascript">setTimeout("self.location.href='<?php echo HOME; ?>/index.html'", 2000)</script>
		<br>
		<br>
		<center><h3><?php echo $MESSAGES["AUTH_NOT_GRANTED"]; ?></h3></center>
	<?php

		html_footer();
	}

	function html_redirect($url) {
		if(defined("CLI_MODE") and CLI_MODE) return;
		if(defined("QUIET") and QUIET) return;
		?>
		<script language="javascript">self.location.href="<?php echo $url; ?>"</script>
		<?php
	}

	function html_showError($msg, $close_window= false) {

		if(defined("QUIET") and QUIET) return;

		if(defined("CLI_MODE") and CLI_MODE) {
			echo "$msg";
		} else {
			$img= ICONS . "/stop.png";
			echo "<table class='message' style='background-color: #ffeeee;'><tr class='message'><td class='message'><img class='message' src='$img'></td><td><b>Error</b><br>$msg</td></tr></table>";
		}

		if($close_window) {
?>			<script language="javascript">
				setTimeout('window.close()', 2000);
			</script>
<?php
		}
	}

	function html_showSuccess($msg) {

		if(defined("QUIET") and QUIET) return;

		if(defined("CLI_MODE") and CLI_MODE) {
			echo "$msg";
		} else {
			$img= ICONS . "/button.png";
			echo "<table class='message' style='background-color: #eeffee;'><tr class='message'><td class='message'><img class='message' src='$img'></td><td>$msg</td></tr></table>";
		}
	}

	function html_showSuccessAndClose($msg, $timeout=5000) {

		if(defined("QUIET") and QUIET) return;

		if(defined("CLI_MODE") and CLI_MODE) {
			echo "$msg";
		} else {
			$div_id= "warn_" . rand(1000,2000);
			echo "<div id='$div_id' style='display:block'>";
			$img= ICONS . "/button.png";
			echo "<table class='message' style='background-color: #eeffee;'><tr class='message'><td class='message'><img class='message' src='$img'></td><td>$msg</td></tr></table>";
			echo "</div>";
?>
			<script language="javascript">
				window.setTimeout("hide_div('<?php  echo $div_id; ?>')", <?php  echo $timeout; ?>);
			</script>
<?php
		}
	}

	function html_showInfo($msg) {

		if(defined("QUIET") and QUIET) return;

		if(defined("CLI_MODE") and CLI_MODE) {
			echo "$msg";
		} else {
			$img= ICONS . "/info.png";
			echo "<table class='message' style='background-color: #eeeeee;'><tr class='message'><td class='message'><img class='message' src='$img'></td><td>$msg</td></tr></table>";
		}
	}

	function html_showInfoAndClose($msg, $timeout=5000) {

		if(defined("QUIET") and QUIET) return;

		if(defined("CLI_MODE") and CLI_MODE) {
			echo "$msg";
		} else {
			$div_id= "warn_" . rand(1000,2000);
			echo "<div id='$div_id' style='display:block'>";
			$img= ICONS . "/info.png";
			echo "<table class='message' style='background-color: #eeeeee;'><tr class='message'><td class='message'><img class='message' src='$img'></td><td>$msg</td></tr></table>";
			echo "</div>";
?>
			<script language="javascript">
				window.setTimeout("hide_div('<?php  echo $div_id; ?>')", <?php  echo $timeout; ?>);
			</script>
<?php
		}
	}

	function html_showDetail($msg) {

		if(defined("QUIET") and QUIET) return;

		if(defined("CLI_MODE") and CLI_MODE) {
			echo "$msg";
		} else {
			$img= ICONS . "/info.png";
			echo "<table class='trim_message' style='background-color: #eeeeee;'><tr><td><img class='message' src='$img'></td><td>$msg</td></tr></table>";
		}
	}

	function html_showDetailAndClose($msg, $timeout=5000) {

		if(defined("QUIET") and QUIET) return;

		if(defined("CLI_MODE") and CLI_MODE) {
			echo "$msg";
		} else {
			$div_id= "warn_" . rand(1000,2000);
			echo "<div id='$div_id' style='display:block'>";
			$img= ICONS . "/info.png";
			echo "<table class='trim_message' style='background-color: #eeeeee;'><tr><td><img class='message' src='$img'></td><td>$msg</td></tr></table>";
			echo "</div>";
?>
			<script language="javascript">
				window.setTimeout("hide_div('<?php  echo $div_id; ?>')", <?php  echo $timeout; ?>);
			</script>
<?php
		}
	}

	function html_showWarning($msg) {

		if(defined("QUIET") and QUIET) return;

		if(defined("CLI_MODE") and CLI_MODE) {
			echo "$msg";
		} else {
			$img= ICONS . "/messagebox_warning.png";
			echo "<table class='message' style='background-color: #ffffdd;'><tr class='message'><td class='message'><img class='message' src='$img'></td><td>$msg</td></tr></table>";
		}
	}

	function html_showWarningAndClose($msg, $timeout=5000) {

		if(defined("QUIET") and QUIET) return;

		if(defined("CLI_MODE") and CLI_MODE) {
			echo "$msg";
		} else {
			$div_id= "warn_" . rand(1000,2000);
			echo "<div id='$div_id' style='display:block'>";
			$img= ICONS . "/messagebox_warning.png";
			echo "<table class='message' style='background-color: #ffffdd;'><tr class='message'><td class='message'><img class='message' src='$img'></td><td>$msg</td></tr></table>";
			echo "</div>";
?>
			<script language="javascript">
				window.setTimeout("hide_div('<?php  echo $div_id; ?>')", <?php  echo $timeout; ?>);
			</script>
<?php
		}
	}

	function html_debug_info($msg) {
		if($_SERVER['REMOTE_ADDR'] == "147.83.198.148") {
			echo "<font color=yellow><i>$msg</i></font>";
		}
	}

	function html_showReload() {

		global $MESSAGES;

		echo "<center><input type=button onclick='window.location.href=\"" . $_SERVER["PHP_SELF"] . "\"' value='" . $MESSAGES["RELOAD"] . "'></center>";
	}

	function build_html_page($PAGE_TITLE, $SUBJECT, $PAGE_CONTENT, $TEMPLATE="default.tpl.php") {
		global $_SERVER;

		$html_page= tpl_get($TEMPLATE);


		$html_page=str_replace("{PAGE_TITLE}", $PAGE_TITLE, $html_page);
		$html_page=str_replace("{SUBJECT}", $SUBJECT, $html_page);
		$html_page=str_replace("{PAGE_CONTENT}", $PAGE_CONTENT, $html_page);

		return $html_page;
	}

	/**
	 * Set the value to the field
	 *
	 * @param string $form_name
	 * @param string $field_name
	 * @param string $value
	 */
	function html_set_field_value($form_name, $field_name, $value) {
		$search = array("\x00", "\x0a", "\x0d", "\x1a", "\x09");
		$replace = array('\0', '\n', '\r', '\Z' , '\t');

		$val= str_replace($search, $replace, @addslashes($value));
		echo "<script>set_field_value('$form_name', '$field_name', '$val');</script>\n";
	}

	/**
	 * This function is used to paste an image into HTML code.
	 * Will return a tag, in function of the environment status.
	 *
	 * If the function is launched under CLI (Command Line Interpreter), then
	 * the generated TAG will be used to paste into an email (inline images
	 * with mime boundary). If is called under HTTP Server (Not CLI), then will
	 * return an <img> tag with source embedded.
	 *
	 * @param string $filename
	 * @param boolean $force_attachment, set to 1 to attach the image in-line
	 * @return unknown
	 */
	function paste_image($filename, $force_attachment=false) {

		global $GLOBALS;

		include_once INC_DIR . "/mail.inc.php";

		$picture= file_get_contents($filename);
		$base64 = chunk_split(base64_encode($picture));

		// If is being executed from command line, the will be generated to
		// be shown as image inline for email
		if(CLI_MODE or $force_attachment) {
			$file_base= basename($filename);
			$rnd= rand(10000,20000);
			$rand_name= $filename . $rnd;

			$tag="<img alt='" . APP_NAME . "' title='" . APP_NAME . "' src='cid:part1.$rnd'>";

			$GLOBALS["attach"][]= <<< IMAGE
Content-Type: image/png;
 name="$file_base"
Content-Disposition: inline;
 filename="$file_base"
Content-ID: <part1.$rnd>
Content-Transfer-Encoding: base64

$base64


IMAGE;

		} else {
			$tag = "<img alt='" . APP_NAME . "' title='" . APP_NAME . "' " .
				   "src=\"data:image/png;base64,$base64\" />";
		}

		return $tag;
	}

?>
