<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage conf
 *
 * Application configuration management page.
 */




	include_once INC_DIR . "/forms/forms.inc.php";
	include_once INC_DIR . "/forms/containers/sub_form.inc.php";
	include_once INC_DIR . "/forms/form_elements/button.inc.php";
	include_once INC_DIR . "/forms/form_elements/field_box.inc.php";
	include_once INC_DIR . "/forms/field.inc.php";
	include_once INC_DIR . "/forms/field_types/listbox.inc.php";


	$audit_level= new listbox();
	$audit_level->lb[0]= "Only user authentication";
	$audit_level->lb[1]= "Datawindows and system files (if exists) changes";
	$audit_level->lb[5]= "Log all events (includes navigation)";

	$date_format= new listbox();
	$date_format->lb["d-m-y"]= "d-m-y";
	$date_format->lb["m-d-y"]= "m-d-y";
	$date_format->lb["y-m-d"]= "y-m-d";


	class frm_config extends sub_form {
		
		private $come_from_accept;

		public function frm_config() {
			global $audit_level;
			global $date_format;

			parent::sub_form("app_config_subform","",true);
			
			$fields1= array();
			$fields1[]= new field("var_MAINTENANCE","Set application under maintenance?","str_bool", true, false, true, true, isset($_POST["var_MAINTENANCE"]) ? $_POST["var_MAINTENANCE"] : MAINTENANCE);
			$fields1[]= new field("var_DEVELOPMENT","Is a development version?","str_bool", true, false, true, true, isset($_POST["var_DEVELOPMENT"]) ? $_POST["var_DEVELOPMENT"] : DEVELOPMENT);
			$fields1[]= new field("var_DEMO_VERSION","Is a demo version?","str_bool", true, false, true, true, DEMO_VERSION);
			$fields1[]= new field("var_BUG_TRACKING","Enable bug tracking?","str_bool", true, false, true, true, BUG_TRACKING);
			$fields1[]= new field("var_DEBUG","Enable debug messages?","str_bool", true, false, true, true, DEBUG);
			$fields1[]= new field("var_DEBUG_QUERY","Enable query debug messages?","str_bool", true, false, true, true, DEBUG_QUERY);
			$fields1[]= new field("var_AUDIT","Audit application usage?","str_bool", true, false, true, true, AUDIT);
			$fields1[]= new field("var_AUDIT_LVL","Audit level?","listbox", true, false, true, true, AUDIT_LVL, $audit_level);
			$fields1[]= new field("var_CRONO_ENABLED","Enabled chronometer?","str_bool", true, false, true, true, CRONO_ENABLED);
			$fields1[]= new field("var_LANG","Default language","list_lang", true, false, true, true, LANG);
			$fields1[]= new field("var_SHOW_LOGOS","Show corporative logos?","str_bool", true, false, true, true, SHOW_LOGOS);
			$fields1[]= new field("var_DATE_FORMAT","Date fields format ","listbox", true, false, true, true, DATE_FORMAT, $date_format);

			$general_configuration= new field_box("field_box_general_config", "Global application parameters", $fields1);
			$this->add_element($general_configuration);

			$fields2= array();
			$fields2[]= new field("var_APP_VERSION","Application version","fstring", false, false, true, false, APP_VERSION);
			$fields2[]= new field("var_APP_NAME","Instance","fstring", false, false, true, true, APP_NAME);
			$fields2[]= new field("var_HOME","Instance home directory (web based)","fstring", false, false, true, true, HOME);
			$fields2[]= new field("var_SERVER_URL","Server URL","fstring", false, false, true, true, SERVER_URL);
			$fields2[]= new field("var_ADM_EMAIL","Administrator e-mail","fstring", false, false, true, true, ADM_EMAIL);
			$fields2[]= new field("var_APP_LOGO","Application logo (web based reference)","fstring", false, false, true, true, APP_LOGO);
			$fields2[]= new field("var_APP_MINILOGO","Application logo (web based reference)","fstring", false, false, true, true, APP_MINILOGO);

			$general_configuration2= new field_box("field_box_general_config2", "Global application configuration", $fields2);
			$this->add_element($general_configuration2);

			$fields3= array();
			$fields3[]= new field("var_DBType","Database type","list_dbms", false, false, true, true, DBType);
			$fields3[]= new field("var_DBServer","Database server hostname (or IP)","fstring", false, false, true, true, DBServer);
			$fields3[]= new field("var_DBName","Database name","fstring", false, false, true, true, DBName);
			$fields3[]= new field("var_DBUser","Database user name","fstring", false, false, true, true, DBUser);
			$fields3[]= new field("var_DBPass","Database password","fstring", false, false, true, true, DBPass);

			$general_configuration3= new field_box("field_box_general_config3", "Database connection", $fields3);
			
			$this->add_element($general_configuration3);
			
			$this->descents["field_box_general_config"]->first_time=true;
			$this->descents["field_box_general_config2"]->first_time=true;
			$this->descents["field_box_general_config3"]->first_time=true;			
		}
		

		protected function action_accept() {


			$this->descents["field_box_general_config"]->first_time=false;
			$this->descents["field_box_general_config2"]->first_time=false;
			$this->descents["field_box_general_config3"]->first_time=false;
			
			$file= SYSHOME . "/conf/app.conf.php";
			$file2=SYSHOME . "/conf/app.conf.php";

			$reg_patterns= array(
				'/define\(\"%s\",\s*\"(.*)\"\);/', 			// define("CONS","VAL")
				'/define\(\"%s\",[\s*^\"](.*)\"(.*)\"\);/',	// define("CONS",xxx . "VAL");
				'/define\(\"%s\",\s*[^\"](.*)\);/'			// define("CONS",VAL)
			);

// To preserve things:
// 'define("%s",$1"%s")',
			$replacements= array(
				'define("%s","%s");',
				'define("%s","%s");',
				'define("%s",%s);'
			);

			$patterns= array();
			$replaces= array();

			$content= file_get_contents($file);

			foreach($_POST as $key => $value) {
				$pos=strpos($key, "var_");

				if($pos !== false and $pos == 0) {

					$varname=substr($key,4);

					$i=0;
					foreach($reg_patterns as $rp) $patterns[$i++]= sprintf($rp, $varname);
					$i=0;
					foreach($replacements as $r) $replaces[$i++]= sprintf($r, $varname, $value);

					for($i=0; $i < count($reg_patterns); $i++) {
						$count=0;
						$content= preg_replace($patterns[$i], $replaces[$i], $content, 1, $count);
						if(($count)>0) break;
					}

					// $content= preg_replace($patterns, $replaces, $content, 1);
				}
			}
			if(@file_put_contents($file2, $content)===false) {
				html_showError("Couldn't update, perhaps you don't have permissions to write it.");
			} else {
				html_showInfo("Configuration updated");
			}
			return 0;
		}
		
		
		private function check_params() {

			$dbtype= $_POST["var_DBTYpe"];
			$dbserver= $_POST["var_DBServer"];
			$dbuser= $_POST["var_DBUser"];
			$dbpass= $_POST["var_DBPass"];
		}
	}
?>
