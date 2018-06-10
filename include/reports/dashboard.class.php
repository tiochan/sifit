<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 *
 * @package lib
 * @subpackage reports
 *
 * Dashboard definition class
 *
 */
 ?>
<script language='JavaScript' src='/reports/include/ajax/ajax.js'></script>
<?php

	include_once INC_DIR . "/reports/tags.class.php";
	include_once INC_DIR . "/ajax/include_ajax.inc.php";

	class dashboard {

		public $id_dashboard;
		public $dashboard_name;
		public $id_group;
		public $content;

		protected $report_ok;

		public function dashboard($id_dashboard, $dashboard_name="") {

			global $global_db;
			global $USER_GROUP;

			$this->report_ok= false;

			if($dashboard_name == "") {
				$query="select id_dashboard, dashboard_name, id_group, content from dashboards where id_dashboard='$id_dashboard'";
			} else {
				$query="select id_dashboard, dashboard_name, id_group, content, periodicity from dashboards where dashboard_name='$dashboard_name' and (id_group is null or id_group='$USER_GROUP')";
			}
			$res= $global_db->dbms_query($query);

			if(! $global_db->dbms_check_result($res)) return;
			list($this->id_dashboard, $this->dashboard_name, $this->id_group, $this->content)= $global_db->dbms_fetch_row($res);

			// TODO
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

			$content= $this->parse_dashboard();

			return $content;
		}

		/**
		 * Generate the report
		 *
		 * @return string
		 */
		public function parse_dashboard() {

			$content= $this->content;
			$tags=get_tags($content);

			$tag_counter=1;
			foreach($tags as $tag) {
				{
					$aux= explode("|", $tag);
					$tag_name= $aux[0];
					$timeout= 0;
					$parameters_array= array();
					$parameters_str="";
					$new_tag= "";
					
					if(count($aux) > 1) {
						
						$aux_parameters= explode(";", $aux[1]);
						foreach($aux_parameters as $parameter) {
							list($key, $value)= explode("=", $parameter);
							$parameters_array[$key]=$value;
							$key=strtolower($key);
							if($key=="refresh_time") $timeout= $value;
						}
						
						$parameters_str="&";
						unset($aux[0]);
						$parameters_str.= str_replace(";", "&", $aux[1]);
					}

					if($timeout > 0) {
						$div_id= "dashboard_tag_id_" . $tag_counter;
						$new_tag= "$div_id $tag_counter
							<div id='$div_id'></div>
							<script>
								function set_div_" . $div_id . "_value() {
									var parameters;
									parameters=\"detail_tag_name=". $tag_name . "&show_header=false" . $parameters_str . "\";
									doWorkGET('". SERVER_URL . "/" . HOME . "/tools/report_tag_preview.php', '" . $div_id . "', parameters);
								}

								ajax_set_timeout(\"set_div_" . $div_id . "_value\", " . $timeout . ", 1);
							</script>\n";

					} else {
						$tag_instance= new tag($tag);
						
						foreach($parameters_array as $key => $value) {
							$tag_instance->add_parameter($key,$value);
						}
						$new_tag= $tag_instance->get_value();
					}

					//replace tag in content
					$content=str_replace('{' . $tag . '}', $new_tag, $content);
				}
				$tag_counter++;
			}

			return $content;
		}
	}

	function get_dashboard_value($report_id, $dashboard_name="") {

		$report= new report($report_id, $dashboard_name);
		$return= $report->parse_dashboard();

		return $return;
	}
?>
