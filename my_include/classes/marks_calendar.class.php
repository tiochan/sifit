<style>
.calendar_top {
	padding: 30px;
}

th.calendar_top_header {
	font-weight: bold;
	font-size: 24px;
	padding: 15px;
	border:1px dashed #afafaf;
}

tr.calendar_day_of_week {
	margin: 15px;
	background-color: #afafaf;
	height: 30px;
}

td.calendar_day_of_week {
	margin: 15px;
	font-weight: bold;
	text-align: center;
	font-size: 14px;
	width: 50px;
	border:1px solid #444444;
}

td.calendar_day_of_week_total {
	margin: 15px;
	background-color: #aaffaa;
	font-weight: bold;
	text-align: center;
	font-size: 14px;
	width: 50px;
	border:1px solid #444444;
}

tr.calendar_day_label {
	margin: 15px;
	background-color: #efefef;
	padding: 15px;
	height: 60px;
}

td.calendar_day_label {
	font-weight: bold;
	text-align: center;
	font-size: 24px;
	border:1px solid #444444;
}

td.calendar_day_label_today {
	font-weight: bold;
	text-align: center;
	font-size: 24px;
	border:3px solid #4444ff;
}

td.calendar_day_label_weekend {
	background-color: #dfdfdf;
	font-weight: bold;
	text-align: center;
	font-size: 24px;
	border:1px solid #444444;
}

td.calendar_day_label_weekend_today {
	background-color: #dfdfdf;
	font-weight: bold;
	text-align: center;
	font-size: 24px;
	border:3px solid #4444ff;
}

.calendar_day_hours {
	text-align: center;
	font-size: 10px;
}

tr.calendar_footer {
	background-color: #efefef;
	padding: 15px;
	height: 60px;
}

td.calendar_footer {
	font-weight: bold;
	text-align: right;
	font-size: 14px;
}

input.day_action {

	height:12px;
	margin:0px;
	padding:0px;
	border:none;

	inherit:none;
	text-align:center;
	cursor:pointer;
	cursor:hand;

	text-align: center;
	font-size: 10px;
}

.date_selector {
	border: 0px;
}

</style>
<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage marks
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

	include_once INC_DIR . "/forms/form_elements.inc.php";


	class mark_calendar extends form_element {

		protected $dw_marks;
		private $month, $year;


		public function mark_calendar($doc_name, & $dw_marks) {

			parent::form_element($doc_name, $doc_name);
			$this->dw_marks= $dw_marks;

			$current_month= date("m");
			$this->month= get_http_param("_cal_month",$current_month);
			$this->year= get_http_param("_cal_year",date("Y"));
		}


		public function show_hidden() {
			echo "<input type='hidden' name='_cal_month' value='$this->month'>\n";
			echo "<input type='hidden' name='_cal_year' value='$this->year'>\n";

			parent::show_hidden();
		}


		public function show() {

			parent::show();

			global $global_db, $USER_ID, $MESSAGES;


			$month= $this->month;
			$year= $this->year;

			$date_selector= $this->get_month_string($month) . $this->get_year_string($year);


			$days= array();

			// FIRST: SET DATES VARIABLES
			for($i=1; $i <= 7; $i++) {
				$days[]= $MESSAGES["WOL_DAY_" . $i];
			}

			// TODO: Adaptar los valores segun el seleccionador de mes/anyo.
			$first_timestamp= strtotime("first day of " . $year . "-" . $month . "-01");
			$last_timestamp= strtotime("last day of this month");
			$last_timestamp= strtotime("last day of " . $year . "-" . $month . "-01");

			$today= date("Y-m-d");
			$first_day= date("j",$first_timestamp);
			$first_day_of_week= date("N", $first_timestamp);
			$last_day= date("j",$last_timestamp);
			//$month_name= date("F",$last_timestamp);
			$month_number= date("m", $first_timestamp);
			$month_number2= date("n", $first_timestamp);
			$month_name= $MESSAGES["WOL_MONTH_" . $month_number2];
			$year= date("Y", $first_timestamp);


			$sql_first_day= $year . "-" . $month_number . "-01";

			// NEXT: Get marks from dates
			$time_marks= array();
			$time_marks_ids= array();

			$query= "SELECT id, mark_date, minutes from time_marks WHERE user_id='$USER_ID' AND ";
			$query.="mark_date >= '$sql_first_day' AND ";
			$query.="mark_date <= FROM_UNIXTIME('" . $last_timestamp . "')";

			$res= $global_db->dbms_query($query);
			if(!$res) die("ERROR");

			while($row= $global_db->dbms_fetch_row($res)) {
				$time_marks[$row[1]]= $row[2];
				$time_marks_ids[$row[1]]= $row[0];
			}

			// PAINT CALENDAR
			echo "<table class='calendar_top'>\n";
			echo "<th colspan=8><table border=0 width='100%'><tr><th class='calendar_top_header'>$year, $month_name</th><th class='calendar_top_header'>$date_selector</td></tr></table></th>\n";
//			echo "<th colspan=8 class='calendar_top_header'>$year, $month_name</th>\n";

			echo "<tr class='calendar_day_of_week'>\n";
			foreach($days as $day) {
				echo "<td class='calendar_day_of_week'>$day</td>\n";
			}
			echo "<td class='calendar_day_of_week_total'>Total</td>\n";
			echo "</tr>\n";


			echo "<tr class='calendar_day_label'>\n";

			$i=1;

			while($i < $first_day_of_week) {
				echo "<td class='calendar_day_label'>&nbsp;</td>\n";
				$i++;
			}

			$total_month_hours=0;
			$total_week_hours=0;

			for($j=1; $j <= $last_day; $j++, $i++) {

				$day_number= $j < 10 ? "0". $j : "$j";
				$sql_date= $year . "-" . $month_number . "-" . $day_number;

				if($i > 7) {
					echo "<td class='calendar_day_of_week_total'>" . $total_week_hours . "</td>\n";
					echo "</tr>\n";
					echo "<tr class='calendar_day_label'>\n";
					$total_week_hours=0;
					$i=1;
				}

				if($i<=5) {
					if(isset($time_marks[$sql_date])) {
						$minutes= $time_marks[$sql_date];
						$hours= round( $minutes / 60, 2);

						if(CLI_MODE) {
							$day_str= "$hours h";
						} else {
							$day_str= $this->dw_marks->get_day_update("$hours h", $time_marks_ids[$sql_date]);
						}
					} else {
						$minutes= 0;
						$hours= 0;

						if(CLI_MODE) {
							$day_str="0 h";
						} else {
							$day_str= $this->dw_marks->get_day_insert("0 h");
						}
					}
					$day_str= str_replace("class='action'", "class='day_action'", $day_str);


					$total_month_hours+= $hours;
					$total_week_hours+= $hours;

					if($sql_date == $today) {
						echo "<td class='calendar_day_label_today'>$j<br>";
					} else {
						echo "<td class='calendar_day_label'>$j<br>";
					}
					echo "<font class='calendar_day_hours'>$day_str</font>";
					echo "</td>\n";
				} else {
				if($sql_date == $today) {
						echo "<td class='calendar_day_label_weekend_today'>$j<br>";
					} else {
						echo "<td class='calendar_day_label_weekend'>$j<br>";
					}
					echo "<font class='calendar_day_hours'>&nbsp;</font></td>\n";
				}
			}

			while($i<=7) {
				if($i<=5) {
					echo "<td class='calendar_day_label'>&nbsp;</td>\n";
				} else {
					echo "<td class='calendar_day_label_weekend'>&nbsp;</td>\n";
				}
				$i++;
			}

			echo "<td class='calendar_day_of_week_total'>$total_week_hours</td>\n";
			echo "</tr>\n";

			echo "<tr class='calendar_footer'><td class='calendar_footer' colspan=8>Total: $total_month_hours h</td></tr>\n";


			echo "</table>\n";
		}


		private function get_month_string($current_month_number) {

			global $MESSAGES;

			$month_str= array();

			for($i=1; $i <= 12; $i++) {
				$month_str[$i]= $MESSAGES["WOL_SHORT_MONTH_" . $i];
			}

			$ret= "<select class='date_selector' name='cal_month' onchange='document.forms[0]._cal_month.value=this.value;document.forms[0].submit();'>\n";

			for($i=1; $i<=12; $i++) {

				$add= $i == $current_month_number ? "selected" : "";
				$ret.="<option value='$i' $add>" . $month_str[$i] . "</option>\n";
			}

			$ret.="</select>\n";

			return $ret;
		}


		private function get_year_string($current_year) {

			$ret="<select class='date_selector' name='cal_year' onchange='document.forms[0].submit();'>\n";

			for($i = $current_year - 10; $i < ($current_year + 10); $i++) {

				$add= $i == $current_year ? "selected" : "";
				$ret.="<option value='$i' $add>" . $i . "</option>\n";
			}

			$ret.="</select>\n";

			return $ret;
		}
	}

?>