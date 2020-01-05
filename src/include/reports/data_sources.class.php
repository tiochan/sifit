<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage reports
 *
 * Report data sources class.
 */


	class data_source {

		public $id_source;

		protected $is_ok;


		public function data_source($id_source) {

			global $global_db;
			global $USER_ID;

			if(!isset($USER_ID)) die("User id is required but is not defined at tag declaration");

			$this->is_ok= false;
/*
			// Get tag definition
			$query="SELECT id_tag, tag_name, calc_method, description, value, extrainfo, is_public FROM report_tags WHERE tag_name ='$tag_name'";
			$res= $global_db->dbms_query($query);

			if(! $global_db->dbms_check_result($res)) return;

			list($this->id_tag, $this->tag_name, $this->calc_method, $this->description, $this->value, $this->extrainfo, $this->is_public)= $global_db->dbms_fetch_row($res);
			$this->value=stripslashes($this->value);

			$global_db->dbms_free_result($res);

			if(!$this->is_public) {
				$user= new user($USER_ID);
				if($user->level != 0) die("Access denied to tag $this->tag_name");
			}
*/
			$this->is_ok= true;
		}

		public function add_parameter($parameter,$value) {
//			$this->extrainfo.=";$parameter=$value";
		}

		/**
		 * Get the real value of TAG
		 *
		 * @return (constant, string, url or operation)
		 */
		public function get_value() {
/*
			if(!$this->is_ok) return "<font color='red'>** TAG $this->tag_name NOT FOUND **<font>";

			$tags=get_tags($this->value);

			foreach($tags as $tag) {
				{
					$tag_instance= new tag($tag[0]);
					$new_tag= $tag_instance->get_value();

					//replace tag in report
					$this->value=str_replace('{' . $tag[0] . '}', $new_tag, $this->value);
				}
			}

			$tag_name= "tag_" . $this->calc_method;

			$file= INC_DIR . "/reports/tags/" . $tag_name  . ".class.php";
			if(!file_exists($file)) {
				$file= MY_INC_DIR . "/reports/tags/" . $tag_name  . ".class.php";
				if(!file_exists($file)) return "** Error: TAG type not implemented: $tag_name **";
			}

			include_once $file;

			$this->tag_ref= new $tag_name($this->value, $this->extrainfo);
			$value= $this->tag_ref->get_value();

			return $value;
		*/
		}
	}
?>