<?php
/*
	Author: Sebastian Gomez, (tiochan@gmail.com)
	For: Politechnical University of Catalonia (UPC), Spain.

	Global page header.
	Be careful, changing this file will change the global page design.
*/
global $MESSAGES;

?>
				</div>
			</div>

			<!-- FOOTER -->
			<div class='table page_footer'>
				<div class='row page_footer'>
					<div class='cell page_footer'>

<?php		if(defined("SHOW_FOOTER") and SHOW_FOOTER) { ?>
<?php			echo APP_VERSION ?>, UPCnet.&nbsp;
<?php			if(defined("CRONO_ENABLED") and CRONO_ENABLED and
					(strstr($_SERVER["PHP_SELF"],"login.php")===false) and
					(strstr($_SERVER["PHP_SELF"],"logout.php")===false) and
					(strstr($_SERVER["PHP_SELF"],"show_info.php")===false)) {
?>
<?php
					$time=crono_stop();
					printf($MESSAGES["CRONO_END_MESSAGE"], $time);
				}
?>
					</div>
				</div>
<?php

				if(defined("SHOW_LOGOS") and SHOW_LOGOS) { ?>
				<div class='row page_footer'>
						<div id="content" class="content"><a href="http://www.upcnet.es" target="_blank"><img class="logo" src="<?php echo HOME; ?>/include/images/logo1.png" border="0" alt="UPCnet"></a></div>
						<div id="content" class="content" align="right"><a href="http://www.upc.edu" target="_blank"><img height='55' src="<?php echo HOME; ?>/include/images/logo2.png" border="0" alt="UPC"></a></div>
				</div>
<?php		}
		} ?>
			</div>