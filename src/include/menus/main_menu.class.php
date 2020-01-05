<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package admin
 *
 * Main menu.
 */

include_once INC_DIR . "/menu.inc.php";

class main_menu extends html_menu_bar {

    public function main_menu() {

        global $MESSAGES, $USER_LEVEL;


        parent::html_menu_bar();

        if(!isset($USER_LEVEL)) return;

        $this->menus["00_init_menu"]= new html_menu($MESSAGES["APP_HOME"], ICONS . "/home.png", HOME . "/");


// APPLICATION ADMINISTRATION

        $this->menus["10_admin_menu"]= new html_menu($MESSAGES["APP_ADMIN"], ICONS . "/kuser.png");
        $admins= Array();

        $admins[]= new html_menu_item($MESSAGES["CHANGE_MY_DATA"], ICONS . "/identity.png",  HOME . "/tools/user.php");

        if($USER_LEVEL <= 3) $admins[]= new html_menu_item($MESSAGES["GROUPS_AND_USERS_MGM_TITLE"], ICONS . "/kuser.png",  HOME . "/tools/groups_and_users.php");

        for($i=0; $i < count($admins); $i++) {
            $this->menus["10_admin_menu"]->add_menu_item($admins[$i]);
        }


// APPLICATION CONFIGURATION

        if($USER_LEVEL <= 3) {

            $configs= Array();

            if($USER_LEVEL == 0) $configs[]= new html_menu_item($MESSAGES["CONFIG_MGM_TITLE"], ICONS . "/configure.png",  HOME . "/tools/config.php");
            if($USER_LEVEL == 0) $configs[]= new html_menu_item($MESSAGES["TASKS_MGM_TITLE"], ICONS . "/clock.png",  HOME . "/tools/task_manager.php");

	        if(count($configs) > 0) $this->menus["40_config_menu"]= new html_menu($MESSAGES["APP_CONF"], ICONS . "/configure.png");
	        for($i=0; $i < count($configs); $i++) {
                $this->menus["40_config_menu"]->add_menu_item($configs[$i]);
            }
        }


// TOOLS MENU

        $this->menus["80_tools"]= new html_menu($MESSAGES["TOOLS"], ICONS . "/package.png");
        $tools_item= Array();

        if($USER_LEVEL == 0) {
            $tools_item[]= new html_menu_item($MESSAGES["DDBB_CURRENT"], ICONS . "/database.png",  HOME . "/tools/db_configured.php");
            $tools_item[]= new html_menu_item($MESSAGES["DDBB_GENERIC"], ICONS . "/database.png",  HOME . "/tools/dbms.php");
            $tools_item[]= new html_menu_item($MESSAGES["LOGS"], ICONS . "/logviewer.png",  HOME . "/tools/logs.php");
	        $tools_item[]= new html_menu_item("System browser", ICONS . "/file.png",  HOME . "/tools/system_browser.php");
        }

        if($USER_LEVEL <= 3) {
            $tools_item[]= new html_menu_item("Mailing", ICONS . "/mail.png",  HOME . "/tools/mailing.php");
        }

        if(defined("BUG_TRACKING") and BUG_TRACKING) $tools_item[]= new html_menu_item($MESSAGES["BUG_MGM_TITLE"], ICONS . "/bug.png",  HOME . "/tools/bugs.php");
        $tools_item[]= new html_menu_item($MESSAGES["REPORT_MGM_TITLE"], ICONS . "/kig_doc.png",  HOME . "/tools/reports.php");

        if(count( $tools_item) ) {
            $this->menus["80_tools"]= new html_menu($MESSAGES["TOOLS"], ICONS . "/package.png");
            foreach ( $tools_item as $item ) {
                $this->menus["80_tools"]->add_menu_item($item);
            }
        }

// DOC MENU
        $this->menus["99_about_menu"]= new html_menu("Doc", ICONS . "/info.png");
        $doc_item= Array();

	    global $tpl_info_page;

	    if($tpl_info_page != "") {
//		    $doc_url = "#' onclick=\"javascript:openMyWindow('" . HOME . "/doc/info/main.php',600,700)\"";
//		    $doc_item[] = new html_menu_item("Doc", ICONS . "/edit.png", $doc_url);

//		    $about_url = "#' onclick=\"javascript:openMyWindow('" . HOME . "/show_info.php?info_page=about.html',600,500)\"";
//		    $doc_item[] = new html_menu_item($MESSAGES["ABOUT"], ICONS . "/info.png", $about_url);

		    $about_url = "#' onclick=\"javascript:openMyWindow('" . HOME . "/show_info.php?info_page=$tpl_info_page',600,500)\"";
		    $doc_item[] = new html_menu_item("Info", ICONS . "/info.png", $about_url);
	    }

        foreach ( $doc_item as $item ) {
            $this->menus["99_about_menu"]->add_menu_item($item);
        }

        ksort($this->menus);
    }
}
