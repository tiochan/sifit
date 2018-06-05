<?php
/*
	Author: Sebastian Gomez, (tiochan@gmail.com)
	For: Politechnical University of Catalonia (UPC), Spain.

	Global page header.
	Be careful, changing this file will change the global page design.

	Also the javascript includes must persist.
*/

global $MESSAGES;
global $GLOBAL_SCRIPTS;
global $GLOBAL_HEADERS;
global $GLOBAL_STYLES;
global $GLOBAL_RECONFIGURE_PAGE;
global $MAINTENANCE_ALLOWED_HOSTS;

?>
<!doctype html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo $MESSAGES["APP_NAME"]; ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<meta name="description" content="<?php echo HTML_HEADER_DESCRIPTION; ?>" />
		<meta name="keywords" content="<?php echo HTML_HEADER_KEYWORD; ?>" />
		<link rel='stylesheet' type='text/css' href='<?php echo HOME ; ?>/include/styles/styles.css' />
<?php if(file_exists(MY_INC_DIR . "/styles/styles.css")) { ?>
		<link rel='stylesheet' type='text/css' href='<?php echo HOME ; ?>/my_include/styles/styles.css' />
<?php } ?>
<?php
		if(isset($GLOBAL_STYLES) and is_array($GLOBAL_STYLES)) {
			foreach($GLOBAL_STYLES as $style) {
?>
		<link rel='stylesheet' type='text/css' href='<?php echo HOME . "/" . $style ; ?>' />
<?php
			}
		}
?>
		<script language='JavaScript' src='<?php echo HOME; ?>/include/styles/functions.js'></script>
		<script type="text/javascript">
			function reconfigure_page() {
				if(document.getElementById('main_loading')) main_loading.style.display='none';
				if(document.getElementById('main_menu')) main_menu.style.display='block';
				if(document.getElementById('main_table_content')) main_table_content.style.display='block';
<?php
				if(isset($GLOBAL_RECONFIGURE_PAGE)) {
					foreach($GLOBAL_RECONFIGURE_PAGE as $SCRIPT) {
						echo $SCRIPT . "\n";
					}
				}
		?>
			}
		</script>
	</head>
	<body onload='javascript:reconfigure_page()'>
<?php
	// If this block is displayed but MAINTENANCE is enabled, then remote IP must be one of the authorized to
	// access the application.

	if(defined("MAINTENANCE") && MAINTENANCE) {
		if(defined("QUIET") and (QUIET === true)) {
		} else {
			$ips_allowed= implode(", ", $MAINTENANCE_ALLOWED_HOSTS);
?>
		<div style='display: block; border: 1px red dashed'>
			<div style='display: block; border-bottom: 1px coral solid;  margin: 10px'>
				<div style='display: table-cell; padding: 5px; vertical-align: middle;'>
					<img src='<?php  echo HOME; ?>/my_include/images/cloud_maintenance.png'>
				</div>
				<div style='display: table-cell; padding: 5px; vertical-align: middle;'>
					<b><font style='font-size: 16px; font-style: italic;'>MAINTENANCE</font></b><br>
					<b>Allowed hosts:</b> <?php  echo $ips_allowed; ?>
				</div>
			</div>
<?php
		}
	}
?>
