<?php
/**
 * @author Jorge Novoa (jorge.novoa@upcnet.es)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage processes periodicity
 *
 */

	include_once INC_DIR . "/forms/field_types/listbox.inc.php";

	class list_periodicity extends listbox {

		public function list_periodicity() {

			global $MESSAGES;

			parent::listbox();

			$this->lb["never"]= $MESSAGES["TASKS_PERIODICITY_NEVER"];
			$this->lb["daily"]= $MESSAGES["TASKS_PERIODICITY_DAILY"];
			$this->lb["working_daily"]= $MESSAGES["TASKS_PERIODICITY_WORKING_DAILY"];
			$this->lb["weekly"]= $MESSAGES["TASKS_PERIODICITY_WEEKLY"];
			$this->lb["monthly"]= $MESSAGES["TASKS_PERIODICITY_MONTHLY"];
			$this->lb["hourly"]= $MESSAGES["TASKS_PERIODICITY_HOURLY"];
			$this->lb["half_hourly"]= $MESSAGES["TASKS_PERIODICITY_HALF_HOURLY"];
		}
	}

	class report_periodicity extends listbox {

		public function report_periodicity() {

			global $MESSAGES;

			parent::listbox();

			$this->lb["never"]= $MESSAGES["TASKS_PERIODICITY_NEVER"];
			$this->lb["daily"]= $MESSAGES["TASKS_PERIODICITY_DAILY"];
			$this->lb["working_daily"]= $MESSAGES["TASKS_PERIODICITY_WORKING_DAILY"];
			$this->lb["weekly"]= $MESSAGES["TASKS_PERIODICITY_WEEKLY"];
			$this->lb["monthly"]= $MESSAGES["TASKS_PERIODICITY_MONTHLY"];
		}

	}
?>