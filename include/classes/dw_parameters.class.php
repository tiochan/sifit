<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage conf
 *
 * Datawindow class for parameters management.
 */

/*
	Table definition

	+-------------+--------------+------+-----+---------+-------+
	| Field       | Type         | Null | Key | Default | Extra |
	+-------------+--------------+------+-----+---------+-------+
	| parameter   | varchar(60)  |      | PRI |         |       |
	| value       | varchar(255) | YES  |     | NULL    |       |
	| description | varchar(255) | YES  |     | NULL    |       |
	+-------------+--------------+------+-----+---------+-------+

*/

	include_once "../include/init.inc.php";
	include_once INC_DIR . "/forms/form_elements/datawindow_ext.inc.php";

	class dw_parameter extends datawindow_ext  {

		public $undeletables= array("adm_email","app_version");
		public $uneditables= array("app_version");

		public function dw_parameter(&$optional_db=null) {

			global $MESSAGES, $USER_LEVEL;

			$qry= new datawindow_query();

			$fields[]= new field_ext("app_parameters.parameter",$MESSAGES["PARAMETERS_FIELD_NAME"],"fstring",true,true,1,true);
			$fields[]= new field_ext("app_parameters.value",$MESSAGES["PARAMETERS_FIELD_VALUE"],"fstring",false,false,2,true);
			$fields[]= new field_ext("app_parameters.description",$MESSAGES["PARAMETERS_FIELD_DESCRIPTION"],"text",false,false,3,true);

			$fields[0]->reference->size=30;
			$fields[1]->reference->size=60;
			$fields[0]->can_order= true;
			$fields[0]->default_order_by="a";

			$can_manage= ($USER_LEVEL == 0);

			$table_parameters= new datawindow_table("app_parameters", $fields, 0, $can_manage, $can_manage, $can_manage);
			$qry->add_table($table_parameters);
			$qry->add_order_by_field($table_parameters, 0);

			parent::datawindow_ext($qry);
		}

		function pre_show_row(&$values, &$can_update, &$can_delete) {

			// Check here for parameters that can't be deleted
			if(in_array($values["app_parameters.parameter"],$this->undeletables)) {
				$can_delete=false;
			}
			// Check here for parameters that can't be edited
			if(in_array($values["app_parameters.parameter"],$this->uneditables)) {
				$can_update=false;
			}
			return true;
		}

		function pre_delete($row_id, $values) {
			global $MESSAGES;

			// Check here for parameters that can't be deleted
			if(in_array($values["app_parameters.parameter"],$this->undeletables)) {
				html_showError($MESSAGES["PARAMETER_CANT_BE_DELETED"]);
				return 0;
			} else {
				return 1;
			}
		}
	}
?>
