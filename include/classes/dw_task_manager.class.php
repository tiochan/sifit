<?php
/**
 * @author Jorge Novoa (jorge.novoa@upcnet.es)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage processes to launch
 *
 */

/*

	Table definition

+-------------+--------------+------+-----+---------+----------------+
| Field       | Type         | Null | Key | Default | Extra          |
+-------------+--------------+------+-----+---------+----------------+
| id_ptl      | mediumint(9) | NO   | PRI | NULL    | auto_increment |
| task_name   | varchar(60)  | NO   |     | NULL    |                |
| script      | varchar(60)  | NO   |     | NULL    |                |
| parameters  | text         | YES  |     | NULL    |                |
| description | varchar(255) | YES  |     | NULL    |                |
| periodicity | varchar(25)  | YES  |     | NULL    |                |
| hour        | varchar(5)   | YES  |     | NULL    |                |
| send_report | tinyint(4)   | NO   |     | 0       |                |
+-------------+--------------+------+-----+---------+----------------+
*/

	include_once "../include/init.inc.php";
	include_once INC_DIR . "/forms/field_types/listbox.inc.php";
	include_once INC_DIR . "/forms/field_types/date_time_listbox.inc.php";
	include_once INC_DIR . "/forms/field_ext.inc.php";
	include_once INC_DIR . "/forms/form_elements/datawindow_ext.inc.php";
	include_once INC_DIR . "/classes/lb_periodicity.class.php";



	class dw_task_manager extends datawindow_ext {

		public function dw_task_manager(&$optional_db=null) {

			global $USER_LEVEL, $global_db, $MESSAGES, $USER_LEVEL;

			$db= is_null($optional_db) ? $global_db : $optional_db;

			$null_reference=null;

			// Datawindow Query
			$qry= new datawindow_query();

			$periodicity= new list_periodicity();

			$scripts= new list_dir_extended("/include/cron", "php");
			$scripts->add_dir("/my_include/cron","php");
			$scripts->add_dir("/plugins/cron","php");

			// Fields
			$fields= array();
			$fields[]= new field_ext("tasks.id_ptl","","auto",false,true,0,false);
			$fields[]= new field_ext("tasks.task_name",$MESSAGES["TASKS_FIELD_NAME"],"fstring",true,false,1,true,$null_reference);
			$fields[]= new field_ext("tasks.script",$MESSAGES["TASKS_FIELD_SCRIPT"],"listbox",true,false,2,true,$null_reference,$scripts);
			$fields[]= new field_ext("tasks.parameters","Params.","text",false,false,3,true,$null_reference);
			$fields[]= new field_ext("tasks.description",$MESSAGES["TASKS_FIELD_DESCRIPTION"],"text",false,false,4,true,$null_reference);
			$fields[]= new field_ext("tasks.periodicity",$MESSAGES["TASKS_FIELD_PERIODICITY"],"listbox",true,false,5,true,$null_reference,$periodicity);
			$fields[]= new field_ext("tasks.hour",$MESSAGES["TASKS_FIELD_HOUR"],"list_time",false,false,6,true);
			$fields[]= new field_ext("tasks.send_report",$MESSAGES["TASKS_FIELD_SEND_REPORT"],"fbool",false,false,7,true);

			// Creation of table and add it to query
			$can_insert= ($USER_LEVEL == 0);
			$can_update= ($USER_LEVEL == 0);
			$can_delete= ($USER_LEVEL == 0);
			$table_rs= new datawindow_table("tasks", $fields, 0, $can_insert, $can_update, $can_delete);
			$qry->add_table($table_rs);

			$qry->add_order_by("tasks.task_name");

			parent::datawindow_ext($qry);
		}
		
		public function pre_insert(&$values) {
		
			switch($values["tasks.periodicity"]) {
				case "never":
				case "daily":
				case "working_daily":
				case "weekly":
				case "monthly":
					break;
					
				case "hourly":
				case "half_hourly":
					
					$values["tasks.hour"]="";
					break;
					
				default:
					html_showError("Invalid periodicity");
					return 0;
			}
			
			return 1;
		}
		
		public function pre_update($row_id, $old_values, &$new_values) {
		
			switch($new_values["tasks.periodicity"]) {
				case "never":
				case "daily":
				case "working_daily":
				case "weekly":
				case "monthly":
					break;
					
				case "hourly":
				case "half_hourly":
					
					$new_values["tasks.hour"]="";
					break;
					
				default:
					html_showError("Invalid periodicity");
					return 0;
			}
			
			return 1;
		}


		public function post_show_row($values) {
			echo $this->create_row_action("Execute","execute_task",$values["row_id"],ICONS . "/run.png");
		}

		protected function action_execute_task($row_id) {

			global $global_db;

			include_once INC_DIR . "/classes/tasks.class.php";

			$query="select id_ptl, task_name, script, description, periodicity, hour, send_report from tasks where id_ptl = '$row_id'";
			$res= $global_db->dbms_query($query);

			if(! $global_db->dbms_check_result($res)) {
				html_showError("Error: task not found by id: $row_id<br>");
				return 0;
			}
			
			$start_date= date ("Y-m-d h:i:s");

			list($id_ptl, $task_name, $script, $description, $periodicity, $hour, $send_report)= $global_db->dbms_fetch_array($res);
			$global_db->dbms_free_result($res);

			$process= new process($id_ptl, $task_name, $script, $description, $periodicity,$hour, false, true);
			$return_code= $process->launch($ret_str);
			
			$end_date= date ("Y-m-d h:i:s");

			if($return_code == 0) {
				html_showSuccess("Task executed successfully.<br>");
			} else {
				html_showError("Error executing task.<br>");
			}

			html_showInfo("Process<br>" .
				"----------------------------------------------------<br>" .
				"-<b> Started at </b>$start_date<br>" .
				"-<b> Ended at: </b>$end_date<br>" .
				"-<b> Execution time: </b>" . $process->execution_time . " secs.<br>" .
				"-<b> Return code: </b>$return_code.<br>" .
				"----------------------------------------------------<br>" . 
				"<b>Execution detail</b><br>" .
				"----------------------------------------------------<br>" .
				$ret_str .
				"<br>");

			return 0;
		}
	}
?>
