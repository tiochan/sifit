<?php
/*
	Author: Sebastian Gomez, (tiochan@gmail.com)
	For: Politechnical University of Catalonia (UPC), Spain.

	Global page header.
	Be careful, changing this file will change the global page design.
*/

global $MESSAGES;
global $USER_LEVEL, $USER_LEVEL_NAME, $USER_GROUP, $USER_GROUP_NAME, $USER_ACCOUNT_NAME;
global $tpl_title;
global $tpl_info_page;

	?>	<!-- MAIN TABLE -->
	<div class="table main">
		<?php
		if(strstr($_SERVER["PHP_SELF"],"login.php")===false and strstr($_SERVER["PHP_SELF"],"logout.php")===false) {
            ?>
            <?php
            if (defined("HIDE_HEADER") and HIDE_HEADER) {
            } else {
                ?>
                <div class="row page_header">
                <div>
                <!-- HEADER LOGO -->
                <div class="cell page_header app_logo">
                    <a title="<?php echo $MESSAGES["APP_HOME"]; ?>" href="<?php echo HOME; ?>/index.php">
                        <img class='header_logo' src="<?php echo HOME . APP_MINILOGO; ?>">
                    </a>
                </div>

                <!-- PAGE TITLE -->
                <div class='cell page_header app_name'>
                    <h2 class='title'><?php echo $MESSAGES["APP_NAME"] ?></h2>
                </div>

                <!-- USER INFO -->
                <div class="cell page_header user_info">
                <?php
                if (isset($_SESSION[APP_NAME . '_logged']) and ($_SESSION[APP_NAME . '_logged'] == true) and (strstr($_SERVER["PHP_SELF"], "show_info.php") === false)) {
                    ?>

                    <?php
                    echo "<b>" . $MESSAGES["APP_LOGIN_USERNAME"] . ":</b> " . $_SESSION[APP_NAME . '_username'] .
                        ", <b>" . $MESSAGES["APP_USER_LEVEL"] . ": </b>" . (isset($USER_ACCOUNT_NAME) ? $USER_ACCOUNT_NAME : $USER_LEVEL_NAME) .
                        ", <b>" . $MESSAGES["GROUP"] . ": </b>" . $USER_GROUP_NAME;
                    ?>
                    </div>
                    <div class="cell page_header logout_button">
                        <a href="<?php echo HOME; ?>/logout.php"
                           class="logout"><?php echo $MESSAGES["AUTH_LOGOUT"]; ?></a>
                    </div>
                    </div>
                <?php
                } ?>
            <?php
            } ?>
            </div>
        <?php
        }
        ?>
	<?php

	if(defined("DEVELOPMENT") and DEVELOPMENT) { ?>
		<!-- UNDER CONSTRUCTION BAR -->
		<div class="row on_construction" colspan="3" background="<?php echo ICONS . "/construction.png"; ?>">

		</div>
	<?php
	} ?>

	<?php
	if(strstr($_SERVER["PHP_SELF"],"login.php")===false and
		strstr($_SERVER["PHP_SELF"],"logout.php")===false and
		strstr($_SERVER["PHP_SELF"],"show_info.php")===false and
		!(defined("SHOW_MENU") and SHOW_MENU == false)
	) {
		$file1= MY_INC_DIR . "/menus/my_main_menu.class.php";
		$file2= INC_DIR . "/menus/main_menu.class.php";

		if(file_exists($file1)) {
			$file= $file1;
			$menu_class= "my_main_menu";
		} else {
			$file= $file2;
			$menu_class= "main_menu";
		}
		if($file != "") {?>
			<!-- MENU BAR -->
			<div class="row main_menu" id='main_menu' style='display:none'>
				<?php
				include_once $file;
				$main_menu= new $menu_class();
				$main_menu->show();	?>
			</div>
		<?php
		}
	}

	if($tpl_title!="") {?>
		<!-- PAGE TITLE -->
		<div class='page_title'>
			<h2 class='page_title' align="left"><?php echo $tpl_title; ?></h2>
		</div>
	<?php
	} ?>

		<div class='page_contents'>
		<!-- PAGE CONTENTS TABLE -->
			<div id='main_loading' class='main_loading'>
				<center>
					<img class='main_loading' src='<?php echo HOME ?>/include/images/loading.gif'>
					<p class='main_loading'><?php echo $MESSAGES["LOADING_DATA"]; ?></p>
				</center>
			</div>
			<div id='main_table_content' style='display:none'>

				<!-- PAGE CONTENTS HERE -->
