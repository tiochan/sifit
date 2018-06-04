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

*/

	include_once INC_DIR . "/forms/forms.inc.php";
	include_once INC_DIR . "/forms/field_types/listbox.inc.php";
	include_once INC_DIR . "/forms/form_elements/datawindow_ext.inc.php";
	include_once INC_DIR . "/forms/form_elements/search_box_ext.inc.php";



	class dw_marks extends datawindow_ext {

		protected $date_selector;
		protected $total_minutes;

		public function dw_marks(&$date_selector, &$optional_db=null) {

			global $MESSAGES, $USER_GROUP, $USER_ID, $USER_LEVEL, $global_db;


			$this->date_selector= $date_selector;

			// Datawindow Query
			$qry= new datawindow_query();

			// Fields
			$fields= Array();

			$user_restriction= "time_marks.user_id='$USER_ID'";


			// TIME_MARKS TABLE //////////////////////////////////////////////////////////
			$fields[0]= new field_ext("time_marks.id","","auto",false,true,0,false);
			$fields[1]= new field_ext("time_marks.user_id","","string", true, false, 0, true);
			$fields[2]= new field_ext("time_marks.mark_date","Date","short_date",true,false,2,true);
			$fields[3]= new field_ext("time_marks.marks","Marks","string",false,false,3,true);
			$fields[4]= new field_ext("time_marks.minutes","Minutes","string",true,false,4,true);
			$fields[5]= new field_ext("dummy","Time","dummy",true, false, 5, false);

			$fields[3]->size=50;
			$fields[1]->hide_on_insert=true;
			$fields[1]->hide_on_update=true;
			$fields[2]->hide_on_update=true;
			$fields[4]->hide_on_insert=true;
			$fields[4]->hide_on_update=true;
			$fields[5]->hide_on_insert=true;
			$fields[5]->hide_on_update=true;


			// Creation of table and add it to query
			$can_insert= true;
			$can_update= true;
			$can_delete= true;

			$table= new datawindow_table("time_marks", $fields, 0, $can_insert, $can_update, $can_delete);
			$table->logical_delete= false;

			$table->add_restriction(1, "='$USER_ID'");

			$table->add_custom_restriction("time_marks.mark_date >= FROM_UNIXTIME('" . $this->date_selector->start_timestamp . "')");
			$table->add_custom_restriction("time_marks.mark_date <= FROM_UNIXTIME('" . $this->date_selector->end_timestamp . "')");


			// Add to query object
			$qry->add_table($table);
			$qry->add_order_by_field($table, 2);

			parent::datawindow_ext($qry);

//			$sb= new search_box_ext(array($fields[2]),"search_time_marks",$MESSAGES["SEARCH"],1,true);
//			$this->add_search_box($sb);

			$this->export_allowed=true;
		}

		protected function show_toolbar_footer($nav_str) {

			$total_hours= floor($this->total_minutes / 60);
			$total_minutes= $this->total_minutes % 60;
			echo "<tr><td><b>Total: $total_hours h $total_minutes mins</b></td></tr>";
		}

		protected function pre_show_row(&$values, &$can_update, &$can_delete) {

			global $USER_GROUP, $USER_ID, $USER_LEVEL;


			$minutes= intval($values["time_marks.minutes"]);
			$hour= floor($minutes / 60);
			$mins= $minutes % 60;

			$this->total_minutes+= $minutes;

			$values["dummy"]= $hour . " h " . $mins . " min";

			return 1;
		}

		public function get_day_update($text, $l_row_id) {

			return $this->create_row_action($text, "start_update", $l_row_id);

		}

		public function get_day_insert($text) {

			return $this->create_row_action($text, "start_insert", -1);
		}


/*		public function post_show_row($values) {

			global $USER_GROUP, $USER_ID, $USER_LEVEL;

			if($USER_LEVEL < 3 or ($values["users.id_user"]==$USER_ID)) {
				echo $this->create_row_action("Power-On","force_wol_on",$values["row_id"], MY_ICONS . "/start.png");
				echo $this->create_row_action("Hibernate","force_wol_hibernate",$values["row_id"],MY_ICONS . "/hibernate.png");
				echo $this->create_row_action("Power-Off","force_wol_off",$values["row_id"],MY_ICONS . "/shutdown.png");
//				echo "<img src='" . HOME . "/plugins/ping.php?ip=" . $values["wol.ip"] . "'>";
				echo "<iframe frameborder='0' src='" . HOME . "/plugins/ws/ping.php?ip=" . $values["wol.ip"] . "' width='16px' height='16px' marginheight='0' marginwidth='0'></iframe>";
			}

		}
*/

		function pre_insert(&$values) {
			global $MESSAGES, $USER_LEVEL, $USER_ID;

			$values["time_marks.user_id"]=$USER_ID;
			$values["time_marks.minutes"]=0;

			return 1;
		}

/*		function pre_update($row_id, $old_values, &$new_values) {

			global $MESSAGES, $USER_LEVEL, $USER_ID;


			return 1;
		}
*/
/*		function pre_delete($row_id, $old_values) {
			global $MESSAGES;

			return 1;
		}
*/
	}
?>
