<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package conf
 * @subpackage forms
 *
 */


/*******************************************
 *	Icons Configuration
 *******************************************/

define("FE_SHOW_ICONS",true);	// If true will use icons instead symbols.
define("FE_RELOAD_ICON", ICONS . "/reload.png");
define("FE_INSERT_ICON", ICONS . "/add.png");
//define("FE_EXPORT_ICON", ICONS . "/fileexport.png");
define("FE_EXPORT_ICON", ICONS . "/Icon_csv.gif");
define("FE_EXPORT_ICON_PDF", ICONS . "/Icon_pdf.gif");
define("FE_EDIT_ICON", ICONS . "/edit.png");
define("FE_DELETE_ICON", ICONS . "/delete.png");
define("FE_FIRST_ICON", ICONS . "/start.png");
define("FE_BACK_ICON", ICONS . "/back.png");
define("FE_NEXT_ICON", ICONS . "/next.png");
define("FE_LAST_ICON", ICONS . "/end.png");
define("FE_UP_ICON", ICONS . "/up_1.png");
define("FE_UP_SELECTED_ICON", ICONS . "/up.png");
define("FE_DOWN_ICON", ICONS . "/down_1.png");
define("FE_DOWN_SELECTED_ICON", ICONS . "/down.png");
define("FE_LOOKUP_ICON", ICONS . "/find.png");
define("FE_DATEPICK_ICON", ICONS . "/cal.png");
define("FE_DEFAULT_ACTION_ICON", ICONS . "/run.png");
define("FE_SAVE_ICON", ICONS . "/document-save.png");
define("FE_OK_ICON", ICONS . "/button.png");
define("FE_CANCEL_ICON", ICONS . "/stop.png");
define("FE_PREVIEW_ICON", ICONS . "/file.png");

define("MAX_LINES_PER_PAGE", 25);
// Where is going to be oppened the detail?
define("DETAIL_TARGET","SELF");		// [ NEW | SELF ]
// CSV exports
define("CSV_SEPARATOR","|");		// The separator which will be used to separate fields into the CSV file
