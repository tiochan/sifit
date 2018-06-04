<?php
/**
 * @author Jorge Novoa (jorge.novoa@upcnet.es)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage reports
 *
 * Report definition class
 *
 */

	include_once INC_DIR . "/reports/tags.class.php";
	include_once INC_DIR . "/mail.inc.php";


	class report {

		public $id_report;
		public $report_name;
		public $id_group;
		public $content;
		public $periodicity;

		protected $report_ok;

		public function report($id_report, $report_name="") {

			global $global_db;


			$this->report_ok= false;

			if($report_name == "") {
				$query="select id_report, report_name, id_group, content, periodicity from reports where id_report='$id_report'";
			} else {
				$query="select id_report, report_name, id_group, content, periodicity from reports where report_name='$report_name'";
			}
			$res= $global_db->dbms_query($query);

			if(! $global_db->dbms_check_result($res)) return;
			list($this->id_report, $this->report_name, $this->id_group, $this->content, $this->periodicity)= $global_db->dbms_fetch_row($res);

			$this->content= stripslashes($this->content);
			$global_db->dbms_free_result($res);


			$this->report_ok= true;
		}

		public function launch() {

			global $USER_GROUP;
			global $USER_GROUP_NAME;
			global $USER_LEVEL;
			global $USER_LEVEL_NAME;
			global $USER_ID;
			global $USER_NAME;
			global $USER_REAL_NAME;
			global $global_db;

/**
 * TODO
 *
 * Substituir todas las variables globales por un objeto de la clase user.
 *
 */
			$periodicity= $this->periodicity;

			if(!method_exists($this,$periodicity)) {
				html_showError("periodicity not defined " . $this->periodicity . " on report " . $this->report_name . "\n");
				return false;
			}

			echo"";
			if($this->$periodicity()) {

				if(file_exists(MY_INC_DIR . "/classes/user.class.php")) {
					include_once MY_INC_DIR . "/classes/user.class.php";
				} else {
					include_once INC_DIR . "/classes/user.class.php";
				}

				$usr= new user($USER_ID);

				$username="";

				$USER_NAME= $usr->username;
				$USER_REAL_NAME= $usr->name;
				$USER_GROUP=$usr->id_group;
				$USER_GROUP_NAME=$usr->group_name;
				$USER_LEVEL=$usr->level;
				$USER_LEVEL_NAME=$usr->level_name;

				echo "<br>- Generating the report <u>'" . $this->report_name . "'</u> for User <u>" . $USER_NAME . "</u>. ";

				$content= $this->parse_report();

				return $content;
			} else {
				return "";
			}
		}

		//reports to launch daily
		protected function daily() {
			return true;
		}

		protected function working_daily() {
			return (date('w') <= 5);
		}

		//reports to launch weekly
		protected function weekly() {

			return (date('w')==1);
		}

		//reports to launch monthly
		protected function monthly() {

			return (date('j')==1);
		}

		//reports to launch "never"
		protected function never() {
			return false;
		}

		/**
		 * Generate the report
		 *
		 * @return string
		 */
		public function parse_report() {

			$content= $this->content;

			$tags=get_tags($content);

			foreach($tags[1] as $tag) {
				{
					$tag_instance= new tag($tag[0]);

					$new_tag= $tag_instance->get_value();

					//replace tag in report
					$content=str_replace('{' . $tag[0] . '}', $new_tag, $content);
				}
			}

			return $content;
		}
	}



	class subscriptions_to_launch {

		protected $subscription_list;

		// Get all subscription
		public function subscriptions_to_launch() {

			global $global_db;
			global $USER_ID;

			$this->subscription_list= array();

			$query="select r.id_user, r.id_report from report_subscription r, users u where r.id_user = u.id_user and u.send_notifications=1";
			$res= $global_db->dbms_query($query);

			if(! $global_db->dbms_check_result($res)) return;

			while($row=$global_db->dbms_fetch_array($res)) {

				list($id_user, $id_report)= $row;
				$this->subscription_list[] = new subscription($id_user, $id_report);
			}

			$global_db->dbms_free_result($res);
		}

		public function launch() {

			foreach($this->subscription_list as $subscription) {

				$subscription->launch();
			}
		}
	}


	class subscription {

		protected $id_user;
		protected $id_report;
		protected $report;


		public function subscription($id_user, $id_report) {

			$this->id_user=$id_user;
			$this->id_report=$id_report;

			$this->report = new report($id_report);
		}

		public function launch() {

			global $USER_ID;

			$USER_ID=$this->id_user;

			$content= $this->report->launch();
			if($content != "") {
				$subject= ucfirst(stripslashes($this->report->report_name)) . " [" . ucfirst($this->report->periodicity) . " report]";
				send_user_mail($USER_ID, $subject, $content, "html");
			} else {
				echo "- Empty report ($this->id_report)<br>";
			}
		}
	}

	function get_report_value($report_id, $report_name="") {

		$report= new report($report_id, $report_name);
		$return= $report->parse_report();

		return $return;
	}
?>