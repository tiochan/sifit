<?php
/**
 * @author Jorge Novoa (jorge.novoa@upcnet.es)
 * @reedited by Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage cron
 *
 * Script to launch cron processes
 */

	include_once INC_DIR . "/crono.inc.php";

	class process {

		public $id_ptl;
		public $task_name;
		public $script;
		public $description;
		public $periodicity;
		public $hour;
		public $send_report;
		public $minute;
		public $force;

		protected $to_execute;
		protected $current_hour;
		protected $current_minute;
		protected $current_dow;		// Day of week
		protected $current_dom;		// Day of month

		public $exit_status;
		public $execution_time;

		//construct
		public function process($id_ptl, $task_name, $script, $description, $periodicity, $hour, $send_report, $force= false) {

			$this->id_ptl= $id_ptl;
			$this->task_name= $task_name;
			$this->script= $script;
			$this->description= $description;
			$this->periodicity= $periodicity;
			$this->send_report= $send_report;


			if($hour != "") {
				list($this->hour, $this->minute)= explode(":",$hour);
			}
			$this->force= $force;

			$current_time= date("H:i:d:N");
			list($this->current_hour,$this->current_minute,$this->current_dow, $this->current_dom)= explode(":",$current_time);

			$this->execution_time="-";

			$periodicity= $this->periodicity;

			if($this->force) {
				$this->to_execute= true;
			} else {
				$this->to_execute= method_exists($this,$periodicity) ? $this->$periodicity() : false;
			}
		}

		public function will_execute() {
			return $this->to_execute;
		}

		public function launch(& $result) {

			$result= "<TITLE_3>Task launched: " . $this->task_name . "</TITLE_3><HLINE>\n";
			$result.="<BOLD>[ Periodicity: </BOLD>" . $this->periodicity . " ]<br><br>\n";

			if($this->to_execute) {

				$time_start = getmicrotime();

				$res_temp="";
				$return_code= $this->execute($res_temp);


				$time_end = getmicrotime();
				$this->execution_time= round($time_end - $time_start, 3);

				$result.= $res_temp;

			} else {
				$return_code=99;
			}

			$result.="<FONT COLOR='gray'>Task finished with exit status: $return_code</FONT><br>\n";

			return $return_code;
		}

		//processes to launch daily
		protected function hourly() {
			return ($this->current_minute == 0);
		}

		protected function half_hourly() {
			return ($this->current_minute == 0) or ($this->current_minute == 30);
		}

		protected function daily() {
			return ($this->hour == $this->current_hour) and ($this->current_minute == $this->minute);
		}

		protected function working_daily() {
			return (date('N')<=5) and $this->daily();
		}

		//processes to launch weekly
		protected function weekly() {
			return (date('w')==1) and $this->daily();
		}

		//processes to launch monthly
		protected function monthly() {
			return (date('j')==1) and $this->daily();
		}

		//processes to launch "never"
		protected function never() {
			return false;
		}

		protected function execute(& $result) {

			$result="";

			$php="/usr/bin/php";
			$script= SYSHOME . "/" . $this->script;
			$command="$php -f $script 2>&1";

			exec($command,$output,$ret);

			if(is_array($output)) {
				$result.= implode("<br>\n", $output);
			} else {
				$result.=$output;
			}

			$result.= "<br><br>";

			return $ret;
		}
	}


	class processes_to_launch {

		protected $process_list;

		public function processes_to_launch($force=false) {

			global $global_db;

			$this->process_list= array();

			$query="select id_ptl, task_name, script, description, periodicity, hour, send_report from tasks order by task_name";
			$res= $global_db->dbms_query($query);

			if(! $global_db->dbms_check_result($res)) return;

			//launch for all processes
			while($row=$global_db->dbms_fetch_array($res)) {

				list($id_ptl, $task_name, $script, $description, $periodicity, $hour, $send_report)= $row;

				$this->process_list[] = new process($id_ptl, $task_name, $script, $description, $periodicity, $hour, $send_report, $force);
			}

			$global_db->dbms_free_result($res);
		}

		public function launch() {

			$send_report=false;

			$result= "";
			$return_code="";

			$stat="<TABLE BORDER='1'>" .
					"<TH>Task</TH>" .
					"<TH>Periodicity</TH>" .
					"<TH>Finished</TH>" .
					"<TH>Execution time</TH>";

			foreach($this->process_list as $process) {
				echo "Executing task: '$process->task_name'\n";

				$res_temp="";
				$return_code= $process->launch($res_temp);
				echo " - return code: $return_code\n";

				$stat.="<TR><TD><BOLD>" . $process->task_name . "</BOLD></TD>";
				$stat.="<TD><BOLD>" . $process->periodicity . "</BOLD></TD>";

				switch($return_code) {
					case 0:
						$stat.="<TD><BOLD><FONT COLOR='GREEN'> OK </FONT></BOLD></TD>";
						$send_report= ($send_report or $process->send_report);
						break;

					case 99:
						$stat.="<TD><BOLD><FONT COLOR='#CBCB00'>NOT EXECUTED</FONT></BOLD></TD>";
						break;

					default:
						$stat.="<TD><BOLD><FONT COLOR='RED'> ERROR </FONT></BOLD></TD>";
						$send_report= ($send_report or $process->send_report);
				}

				$stat.="<TD align='right'>" . $process->execution_time . "</TD></TR>";

				if($process->send_report and $return_code != 99) $result.= $res_temp;
			}

			$stat.="</TABLE>";

			if($send_report) {
				return $stat . "<LINE_BREAK>" . $result;
			} else {
				return "";
			}
		}
	}
?>