<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package cloud
 * @subpackage forms
 *
 * Field box for date selection
 */

	include_once INC_DIR . "/forms/field.inc.php";
	include_once INC_DIR . "/forms/form_elements/field_box.inc.php";
	include_once INC_DIR . "/forms/field_types/listbox.inc.php";

	include_once INC_DIR . "/reports/tags.class.php";



	class plain_date_selector {

		protected $list_period;

		public $period;
		public $start_date, $end_date;
		public $start_date_time, $end_date_time;
		public $start_timestamp, $end_timestamp;

		public function plain_date_selector($period) {

			$this->set_period($period);
		}

		public function set_period($period) {

			$date_format=get_tag_value("CONS_DATE_FORMAT_PHP");

			$this->period= get_http_param("period");

			switch($this->period) {
				case "0":			// Past month
					$this->start_date= date($date_format,strtotime("first day of this month -1 month"));
					$this->end_date= date($date_format,strtotime("last day of this month -1 month"));
					break;
				case "1":			// Instant
					$now= date($date_format,strtotime("today"));
					$this->start_date= $now;
					$this->end_date= $now;
					break;
				case "2":			// Current month
					$this->start_date= date($date_format,strtotime("first day of this month"));
					if(date('j')==1) {
						$this->end_date= date($date_format);
					} else {
						$this->end_date= date($date_format,strtotime("today"));
					}
					break;
				case "3":			// Select period
					$this->start_date= get_http_param("start_date");
					$this->end_date= get_http_param("end_date");
					break;
				default:
					$this->start_date= date($date_format,strtotime("first day of this month"));
					$this->end_date= date($date_format,strtotime("today"));
					break;
			}

			$this->str_format=get_tag_value("CONS_DATE_TIME_FORMAT");
			$this->str_format2=get_tag_value("CONS_DATE_TIME_FORMAT_PHP");
			$this->sql_date_time_format=get_tag_value("CONS_DATE_TIME_FORMAT_SQL");
			$this->short_date_format=get_tag_value("CONS_DATE_FORMAT_PHP");

			$now=date($this->str_format2);

			$this->start_date_time= $this->start_date . " 00:00:00";
			$this->end_date_time= $this->end_date . " 23:59:59";


			$tmp= strptime($this->start_date_time,$this->str_format);
			if($tmp === false) {
				html_showError("Invalid start date $this->start_date_time");
				return;
			}
			$this->start_timestamp= mktime($tmp["tm_hour"],$tmp["tm_min"],$tmp["tm_sec"],$tmp["tm_mon"] + 1,$tmp["tm_mday"],$tmp["tm_year"] + 1900);

			$tmp= strptime($this->end_date_time,$this->str_format);
			if($tmp === false) {
				html_showError("Invalid end date $this->end_date_time");
				return;
			}
			$this->end_timestamp= mktime($tmp["tm_hour"],$tmp["tm_min"],$tmp["tm_sec"],$tmp["tm_mon"] + 1,$tmp["tm_mday"],$tmp["tm_year"] + 1900);
		}
	}


	class fb_date_selector extends field_box {

		protected $list_period;

		public $period;
		public $start_date, $end_date;
		public $start_date_time, $end_date_time;
		public $start_timestamp, $end_timestamp;


		public function fb_date_selector($name) {

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

		public function show_hidden() {
			echo "<input type='hidden' name='period' value='" . get_http_param("period") . "'>\n";
			echo "<input type='hidden' name='start_date' value='" . get_http_param("start_date") . "'>\n";
			echo "<input type='hidden' name='end_date' value='" . get_http_param("end_date") . "'>\n";

			parent::show_hidden();
		}

		private function get_dates() {

			$date_format=get_tag_value("CONS_DATE_FORMAT_PHP");

			$this->period= get_http_param("period");

			switch($this->period) {
				case "0":			// Past month
					$this->start_date= date($date_format,strtotime("first day of this month -1 month"));
					$this->end_date= date($date_format,strtotime("last day of this month -1 month"));
					break;
				case "1":			// Instant
					$now= date($date_format,strtotime("today"));
					$this->start_date= $now;
					$this->end_date= $now;
					break;
				case "2":			// Current month
					$this->start_date= date($date_format,strtotime("first day of this month"));
					if(date('j')==1) {
						$this->end_date= date($date_format);
					} else {
						$this->end_date= date($date_format,strtotime("today"));
					}
					break;
				case "3":			// Select period
					$this->start_date= get_http_param("start_date");
					$this->end_date= get_http_param("end_date");
					break;
				default:
					$this->start_date= date($date_format,strtotime("first day of this month"));
					$this->end_date= date($date_format,strtotime("today"));
					break;
			}

			$this->fields[1]->set_default_value($this->start_date);
			$this->fields[2]->set_default_value($this->end_date);



			$this->str_format=get_tag_value("CONS_DATE_TIME_FORMAT");
			$this->str_format2=get_tag_value("CONS_DATE_TIME_FORMAT_PHP");
			$this->sql_date_time_format=get_tag_value("CONS_DATE_TIME_FORMAT_SQL");
			$this->short_date_format=get_tag_value("CONS_DATE_FORMAT_PHP");

			$now=date($this->str_format2);

			$this->start_date_time= $this->start_date . " 00:00:00";
			$this->end_date_time= $this->end_date . " 23:59:59";


			$tmp= strptime($this->start_date_time,$this->str_format);
			if($tmp === false) {
				html_showError("Invalid start date $this->start_date_time");
				return;
			}
			$this->start_timestamp= mktime($tmp["tm_hour"],$tmp["tm_min"],$tmp["tm_sec"],$tmp["tm_mon"] + 1,$tmp["tm_mday"],$tmp["tm_year"] + 1900);

			$tmp= strptime($this->end_date_time,$this->str_format);
			if($tmp === false) {
				html_showError("Invalid end date $this->end_date_time");
				return;
			}
			$this->end_timestamp= mktime($tmp["tm_hour"],$tmp["tm_min"],$tmp["tm_sec"],$tmp["tm_mon"] + 1,$tmp["tm_mday"],$tmp["tm_year"] + 1900);
		}

		protected function action_accept() {

			if($this->start_date == null or $this->end_date == null) {
				html_showError("indicar dates");
			}

			return 0;
		}
	}
?>