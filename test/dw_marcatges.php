<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage wol
 *
 * Datawindow class for Wake On Lan management.
 */


/*
	Table definition

	mysql> describe time_marks;
	+-----------+--------------+------+-----+---------+----------------+
	| Field     | Type         | Null | Key | Default | Extra          |
	+-----------+--------------+------+-----+---------+----------------+
	| id        | mediumint(9) | NO   | PRI | NULL    | auto_increment |
	| user_id   | mediumint(9) | NO   | MUL | NULL    |                |
	| mark_date | date         | NO   |     | NULL    |                |
	| marks     | varchar(255) | YES  |     | NULL    |                |
	| minutes   | int(11)      | NO   |     | 0       |                |
	+-----------+--------------+------+-----+---------+----------------+
	5 rows in set (0.00 sec)

*/

	include_once INC_DIR . "/forms/forms.inc.php";
	include_once INC_DIR . "/forms/field_types/listbox.inc.php";
	include_once INC_DIR . "/forms/form_elements/datawindow_ext.inc.php";
	include_once INC_DIR . "/forms/form_elements/search_box_ext.inc.php";

	include_once INC_DIR . "/forms/form_elements/field_box.inc.php";
	include_once INC_DIR . "/forms/field_types/listbox.inc.php";

	include_once INC_DIR . "/reports/tags.class.php";


	class fb_data_selector extends field_box {

		protected $list_period;

		public $period;
		public $start_date;
		public $end_date;


		public function fb_data_selector($name) {

			global $MESSAGES;
			global $USER_LEVEL;

			$fields=array();

			$this->list_period= new listbox();

			$this->list_period->lb["0"]= $MESSAGES["PAST_MONTH"];
			$this->list_period->lb["1"]= $MESSAGES["INSTANT"];
			$this->list_period->lb["2"]= $MESSAGES["CURRENT_MONTH"];
			$this->list_period->lb["3"]= $MESSAGES["SELECT_PERIOD"];


			$fields[]= new field("period", $MESSAGES["PERIOD"], "listbox", false, false, true, true, 2, $this->list_period);
			$fields[]= new field("start_date", $MESSAGES["START_DATE"], "date", false, false, true, true);
			$fields[]= new field("end_date", $MESSAGES["END_DATE"], "date", false, false, true, true);

			parent::field_box($name, $MESSAGES["PERIOD_SELECTOR"], $fields, true, 10);
			$this->show_cancel_button=false;

			$this->get_dates();
		}

		private function get_dates() {

			$date_format=get_tag_value("CONS_DATE_FORMAT_PHP");
echo "STR TO TIME: " . strtotime("first day of this month");
			$this->period= get_http_param("period");

			switch($this->period) {
				case "0":			// Past month

					$this->start_date= date($date_format,strtotime("first day of this month -1 month"));
					$this->end_date= date($date_format,strtotime("last day of this month -1 month"));
					break;
				case "1":			// Instant
					$now= date($date_format);
					$this->start_date= $now;
					$this->end_date= $now;
					break;
				case "2":			// Current month
					$this->start_date= date($date_format,strtotime("first day of this month"));
echo "HOLA: $date_format,  $this->start_date";
					if(date('j')==1) {
						$this->end_date= date($date_format);
					} else {
						$this->end_date= date($date_format);
					}
					break;
				case "3":			// Select period
					$this->start_date= get_http_param("start_date");
					$this->end_date= get_http_param("end_date");
					break;
				default:
					$this->start_date= date($date_format,strtotime("first day of this month"));
					$this->end_date= date($date_format,strtotime("yesterday"));
					break;
			}

			$this->fields[1]->set_default_value($this->start_date);
			$this->fields[2]->set_default_value($this->end_date);

/*
			echo "START DATE: $this->start_date, END DATE $this->end_date<br>";
			echo "DATE FORMAT: $date_format<br>";
			echo "PAST MONTH " . date($date_format,strtotime("first day of this month -1 month")) . "<br>";
 */
		}

		protected function action_accept() {

			if($this->start_date == null or $this->end_date == null) {
				html_showError("indicar dates");
			}

			return 0;
		}
	}


	class field_ext_ip_mac extends fstring {

		function field_ext_ip_mac($default_value="") {

			global $GLOBAL_HEADERS;

			parent::fstring($default_value);

			if(!isset($GLOBAL_HEADERS["jquery_dialog"])) {
				$GLOBAL_HEADERS["jquery_dialog"]='
					<link rel="stylesheet" href="' . HOME . '/include/jquery/themes/base/jquery.ui.all.css">
					<script src="' . HOME . '/include/jquery/jquery-1.6.2.js"></script>
					<script src="' . HOME . '/include/jquery/ui/jquery.ui.core.js"></script>
					<script src="' . HOME . '/include/jquery/ui/jquery.ui.widget.js"></script>
					<script src="' . HOME . '/include/jquery/ui/jquery.ui.button.js"></script>
					<script src="' . HOME . '/include/jquery/ui/jquery.ui.dialog.js"></script>
					<script src="' . HOME . '/include/jquery/ui/jquery.ui.draggable.js"></script>
					<script src="' . HOME . '/include/jquery/ui/jquery.ui.position.js"></script>
					';
			}
		}


		public function show($field_name, $readonly) {

			global $IP_MAC_HELP;

			$msg="";
			if(isset($_SERVER["REMOTE_ADDR"])) {
				$msg= "La IP des de la que accediu &eacute;s <b>" . $_SERVER["REMOTE_ADDR"] . "</b><br>";
			}

			$msg.= "Per obtenir la IP de la vostra m&agrave;quina heu d'executar 'sudo ifconfig'";

			global $MESSAGES;
			global $GLOBAL_HEADERS;

			$GLOBAL_HEADERS[$field_name . "jquery"]="
					<script>
						$(function() {
							$( \"#dialog_" . $field_name . "\" ).dialog( {
								autoOpen: false,
								show: \"slide\",
								buttons: { \"Ok\": function() { $(this).dialog(\"close\"); }}
							} );

							$( \"#opener_" . $field_name ."\" ).click(function() {
								$( \"#dialog_" . $field_name . "\" ).dialog( 'option', 'position', 'center' );
								$( \"#dialog_" . $field_name . "\" ).dialog( 'option', 'width', '550px' );
								$( \"#dialog_" . $field_name . "\" ).dialog( \"open\" );
								return false;
							});
						});
					</script>";
			echo "<div id=\"dialog_" . $field_name . "\" title=\"help\"><p>". $IP_MAC_HELP ."</p></div>";
			$add= $readonly ? "class='readonly' readonly='1'":"";
			$def= $this->default_value != null ? "value='$this->default_value'": "";
			echo "<div align='left'>";
			parent::show($field_name, $readonly);
			echo "<button id=\"opener_$field_name\">Com puc obtenir la meva IP o la MAC?</button>";
			echo "</div>";
		}
	}

	class dw_marcatges_ext extends datawindow_ext {

		public function dw_marcatges_ext(&$optional_db=null) {

			global $MESSAGES, $USER_GROUP, $USER_ID, $USER_LEVEL, $global_db;


			// Datawindow Query
			$qry= new datawindow_query();

			// Fields
			$fields_mark= Array();

			$mark_restriction= "user_id='$USER_ID'";
			$user_reference= new foreign_key($global_db,"users","id_user","username", $global_db->dbms_query_append($mark_restriction, "deleted='0'"));

			// WOL TABLE //////////////////////////////////////////////////////////
			$fields_mark[0]= new field_ext("time_marks.id","","auto",false,true,0,false);
			$fields_mark[1]= new field_ext("time_marks.user_id",$MESSAGES["USER_FIELD_USERNAME"],"foreign_key", true, false, 0, ($USER_LEVEL <= 3), null, $user_reference);
			$fields_mark[2]= new field_ext("time_marks.mark_date","Data","date",true,false,2,true);
			$fields_mark[3]= new field_ext("time_marks.marks","Marcatges","fstring",true,false,3,true);
			$fields_mark[4]= new field_ext("time_marks.minutes","Minuts","integer",false,false,4,true,0);

			$fields_mark[1]->hide_on_update=true;

			// Creation of table and add it to query
			$can_insert= true;
			$can_update= true;
			$can_delete= true;

			$table_marks= new datawindow_table("time_marks", $fields_mark, 0, $can_insert, $can_update, $can_delete);
			$table_marks->logical_delete= false;

			$qry->add_table($table_marks);


			$sb= new search_box_ext(array($fields_mark[2]),"search_wol",$MESSAGES["SEARCH"],1,false);


			// CALL CONSTRUCTOR ///////////////////////////////////////////////////
			parent::datawindow_ext($qry);

			$this->add_search_box($sb);
		}
/*
		protected function pre_show_row(&$values, &$can_update, &$can_delete) {

			global $USER_GROUP, $USER_ID, $USER_LEVEL;

			if($USER_LEVEL <=3) return 1;

			if($values["users.id_user"]!=$USER_ID) {
				$can_update=false;
				$can_delete=false;
				return false;
			}

			return 1;
		}

		public function post_show_row($values) {

			global $USER_GROUP, $USER_ID, $USER_LEVEL;

			if($USER_LEVEL < 3 or ($values["users.id_user"]==$USER_ID)) {
				echo $this->create_row_action("Power-On","force_wol_on",$values["row_id"], MY_ICONS . "/start.png");
				echo $this->create_row_action("Hibernate","force_wol_hibernate",$values["row_id"],MY_ICONS . "/hibernate.png");
				echo $this->create_row_action("Power-Off","force_wol_off",$values["row_id"],MY_ICONS . "/shutdown.png");
//				echo "<img src='" . HOME . "/plugins/ping.php?ip=" . $values["wol.ip"] . "'>";
				echo "<iframe frameborder='0' src='" . HOME . "/plugins/ws/ping.php?ip=" . $values["wol.ip"] . "' width='16px' height='16px' marginheight='0' marginwidth='0'></iframe>";
			}
		}

		protected function action_force_wol_on($row_id) {

			global $global_db;

			include_once MY_INC_DIR . "/classes/wakeonlan.class.php";

			wakeonlan($row_id);
		}

		protected function action_force_wol_hibernate($row_id) {

			global $global_db;

			include_once MY_INC_DIR . "/classes/wakeonlan.class.php";

			hibernateonlan($row_id);
		}

		protected function action_force_wol_off($row_id) {

			global $global_db;

			include_once MY_INC_DIR . "/classes/wakeonlan.class.php";

			shutdownonlan($row_id);
		}

		function pre_insert(&$values) {
			global $MESSAGES, $USER_LEVEL, $USER_ID;

			if($USER_LEVEL > 0) $values["wol.id_user"]= $USER_ID;

			if(($values["wol.enabled"] == 1) and ($values["wol.mac"] == "")){
				html_showError("Cal indicar la MAC del PC que voleu iniciar");
				return 0;
			}

			return 1;
		}

		function pre_update($row_id, $old_values, &$new_values) {
			global $MESSAGES, $USER_LEVEL, $USER_ID;

			if($USER_LEVEL > 0) $values["wol.id_user"]= $USER_ID;

			if(($new_values["wol.enabled"] == 1) and ($new_values["wol.mac"] == "")){
				html_showError("Cal indicar la MAC del PC que voleu iniciar");
				return 0;
			}

			return 1;
		}

		function pre_delete($row_id, $old_values) {
			global $MESSAGES;

			return 1;
		}
		*/
	}
?>
