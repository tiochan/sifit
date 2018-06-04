<?php
/**
 * @author Sebastian Gomez (sebastian.gomez@upcnet.es)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage reports
 *
 * Tag elements definition class
 *
 */

	include_once INC_DIR . "/reports/tags.class.php";

	/**
	 * class tag_element
	 *
	 * Generic tags.
	 * Each new tag type must extend this class.
	 *
	 */
	class tag_element {

		protected $show_extra_info= true;
		protected $show_connection= true;
		protected $value;
		protected $extrainfo;
		protected $db;

		protected $parameters;


		public function tag_element($value, &$extrainfo="", &$db=null, &$parameters=null) {
			$this->value= $value;
			$this->extrainfo= $extrainfo;
			$this->db=$db;
			$this->parameters=array();
			$this->parse_extrainfo();
		}

		public function get_value() {
			$this->replace_parameters();

			return $this->value;
		}

		protected function replace_parameters() {
			// Replace vars in tag value
			foreach($this->parameters as $key => $value) {
				$this->value= str_replace('[$' . $key . ']', $value, $this->value);
			}
		}

		static public function check_value($value) {
			return 1;
		}

		public function & show_insert_form(&$field_list, $for_update) {

			global $MESSAGES;

			$first_field=null;
			$class= substr(__CLASS__,strpos(__CLASS__, "_") + 1);
?>
			<tr>
				<td colspan=2>
					<table width='100%' style='border-bottom: 2px solid #008800;'>
						<tr>
							<td>
								<div style='display: block; padding: 10px; vertical-align: middle; cursor: pointer' onclick='toggle_div_visibility("tag_help","block");'>
									<img style='vertical-align: middle;' height='16px' src='<?php echo ICONS; ?>/info.png'>
									<font style='font-size: 14px; font-weight: bold;'>
										&nbsp;<?php  echo $MESSAGES["HELP"]; ?>
									</font>
								</div>
								<div id='tag_help' style='display:none; padding: 10px; background-color: white;'>
<?php
									$this->show_help();
									echo "<br>";
									$this->show_extra_help();
?>
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
<?php

			foreach($field_list as $field) {

				if($field->name == "report_tags.calc_method") continue;
				if($field->name == "report_tags.extrainfo" and !$this->show_extra_info) continue;
				if($field->name == "report_tags.connection" and !$this->show_connection) continue;
				if(!$field->will_be_displayed(!$for_update, $for_update)) continue;

				// Here you can change the type of the field reference:
				if($field->name == "report_tags.value") $this->change_field_properties($field);

?>
				<tr class='data_box_rows_list_header'>
					<td class='data_box_cell_alias'>
					<?php
						echo $field->alias;
						if($field->required) {
							?><font color='red'>&nbsp;*</font><?php
						}
					?></td>
					<td class='data_box_cell_value'>
<?php

				if(($first_field === null) and ($field->visible)) {
					$first_field= $field;
				}

				$field->show(true, false);
?>
					</td>
				</tr>
<?php
			}

			return $first_field;
		}

		protected function change_field_properties(&$field) {
		}

		protected function show_help() {
?>
			<p>A TAG is an element to be used into Reports or other TAGs.<br>
			The return of each TAG depends on its type, so a "constant" TAG will return a value as is, a "graph" TAG
			will return an image, a "query" TAG will return an array or a result table, ..., and so on.<br>
			<br>
			The field <b>value</b> is needed, and is the value used to calculate the TAG return.<br>
			<br>
			You can insert <b>other TAGs</b> into the value field in the form "{TAG_NAME}" (claudators are required).<br>
<?php
		}

		protected function show_extra_help() {
?>
			<p>
			<b>Extra parameters</b><br>
			<br>
			You can also add some extra parameters into the <i>'additional information field'</i>, separated by ';'.<br>
			Those extra parameters depends on the TAG type.<br>
			</p>
<?php
		}

//		public function add_parameter($parameter,$value) {
//echo "- TAG ADD PARAMETER $parameter, $value<br>";
//			$this->parameters[$parameter]= $value;
//			$this->extrainfo.=";$parameter=$value";
//echo "- EXTRAINFO: $this->extrainfo<br>";
//		}
//
		protected function get_parameter($parameter) {

			if(!isset($this->parameters[$parameter])) return false;
			return $this->parameters[$parameter];
		}

		protected function parse_extrainfo() {

			$this->extrainfo=trim($this->extrainfo);
			$pairs=explode(";",$this->extrainfo);

			///////////////////////////////////////////////
			// First, parse those which are not parameters

			// Extract parameters and values
			foreach($pairs as $pair) {

				$pair= trim($pair);
				$elements=explode("=",$pair,2);

				if(!isset($elements[0]) or $elements[0] == "") continue;

				trim($elements[0]);
				$value= isset($elements[1]) ? trim($elements[1]) : "";

				$tags=get_tags($value);
				foreach($tags as $tag) {
					if(strpos($tag,"$") === false ) {
						$tag_value= get_tag_value($tag);
						$value=str_replace('{' . $tag . '}', $tag_value, $value);
					}
				}

				$this->parameters[$elements[0]]= $value;
			}

			///////////////////////////////////////////////
			// Then, parse those which are parameters

			// Extract parameters and values
			foreach($pairs as $pair) {

				$pair= trim($pair);
				$elements=explode("=",$pair,2);

				if(!isset($elements[0]) or $elements[0] == "") continue;

				trim($elements[0]);
				$value= isset($elements[1]) ? trim($elements[1]) : "";

				$vars=get_vars($value);

				foreach($vars as $var) {

					$var_value= $this->get_parameter($var);
					if($var_value === false) {
						echo "<font color='red'>*** Warning: Expected parameter $var on tag property $elements[0], but not found ***</font><br>";
					}

					$value=str_replace('{$' . $var . '}', $var_value, $value);
					$value=str_replace('[$' . $var . ']', $var_value, $value);
					$this->parameters[$elements[0]]= $value;
				}
			}
		}
	}
?>