<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage forms
 *
 */

	include_once SYSHOME . "/include/forms/form_elements.inc.php";
	include_once SYSHOME . "/include/forms/form_elements/common_data_elements_ext.inc.php";


	/**
	 * Defines a new interface to dabatase tables.
	 *
	 */
	class datawindow_array extends common_data_elements_ext {

		// Aspect related properties
		public $tabular= true;			// The show mode (tabular or not tabular (sequential))
		public $nav_enabled= true;		// Is enabled the navigation bar (if needed)?
		public $show_no_rows= true;		// If no rows, show headers or a "NO ROWS" message?
		public $show_toolbar= true;		// By default set to true
		public $show_reload_button= true;
		public $show_rows_info= true;
		public $width;					// Datawindow container width
		public $export_allowed;
		public $table_title;

		public $store_data_in_session=false;

		protected $data;				// The data Array that contain rows

		protected $col_modifier;
		protected $form_id;

		protected $headers;
		protected $col_align;


		/**
		 * Creates a new datawindow improved object
		 *
		 * @param datawindow_query $datawindow_query
		 * @return datawindow_ext
		 */
		public function datawindow_array(&$dataArray, $width=0, $table_title="") {

			global $MESSAGES;

			$aux_name= "datawindow_Array" . get_new_id();
			parent::common_data_elements_ext($aux_name);

			$this->width= $width;

			$this->search_box= array();
			$this->table_title= $table_title;
			$this->custom_query="";
			$this->export_allowed= false;

			$this->data=$dataArray;
		}

		/**
		 * Set the column names.
		 *
		 * $headers is an array where each position is the name of its column.
		 * @param string array $headers
		 */
		public function set_headers($headers) {
			$this->headers= $headers;
		}

		/**
		 * Set the column align.
		 * By default all data is threaten as string.
		 *
		 * $col_align is an array where each row is the postiion of its column.
		 * Correct values are allowed from CSS Styles for td::text-align.
		 *
		 * Common values:
		 * - left (default)
		 * - center
		 * - right
		 *
		 * @param string array $col_align
		 */
		public function set_col_align($col_align) {
			$this->col_align= $col_align;
		}

		public function set_arrayData(& $data) {

			$this->data= $data;
		}

		/**
		 *
		 * You can set code on this method.
		 * Is called for:
		 * - "refresh" event thrown
		 * - if $this->$store_data_in_session== true -> first time to show data
		 * - else each time that retrieve is called
		 */
		public function get_data() {
			return 1;
		}

		protected function action_retrieve() {

			return 0;
		}

		protected function action_start_export($row_id) {

			return $this->export_data();
		}

		public function show_hidden() {

			// No actions allowed on CLI mode
			if(CLI_MODE) return 0;


			if($this->hidden_shown) return;
			?>

			<input type='hidden' name='dw_action_<?php echo $this->doc_name; ?>' value=''>
			<input type='hidden' name='row_id_<?php echo $this->doc_name; ?>' value='-1'>
			<input type='hidden' name='start_in_<?php echo $this->doc_name; ?>' value='0'>
			<?php

			// For each field store some hidden information (if it need to do it)

			parent::show_hidden();
		}

		public function show() {
			parent::show();

			if(!$this->visible) return;
		}


		/**
		 * Show the rows contained into the data array
		 *
		 * @return integer, 0=any error happend.
		 */
		protected function retrieve() {

			global $MESSAGES;

			/**
			 * Disable some features on CLI mode
			 */
			if(CLI_MODE) {
				$this->nav_enabled= false;
				$this->export_allowed= false;
				$this->show_no_rows= false;
				$this->show_toolbar= false;
				$this->row_selection_enabled= false;
			}

			$start_in= get_http_post_param("start_in_" . $this->doc_name, 0);

			$stop=false;
			$nav_str="";

			// Perhaps the inherited class want to put something here! (hidden fields, buttons, etc.)
			if(method_exists($this,"pre_retrieve")) {
				// The first table to show is the table which will store the
?>
				<table class='data_box_previous'>
					<tr class='data_box_previous'><td class='data_box_previous'>
							<?php $stop= !$this->pre_retrieve(); ?>
					</td></tr>
				</table>
<?php			}

			if($stop) {
				if(DEBUG) html_showInfo("Info: datawindow::retrieve cancelled by user.");
				return 0;
			}

			$numRows=count($this->data);
			$res=array();

			if($this->nav_enabled) {
				// The second table will show the navigation bar:
				// Navigation bar can be disabled. By default is enabled.
				// Ok, navigation is allowed, but, is really necessary?

				$nav_str= $this->get_navigationString($start_in, $numRows);

				for($i=$start_in, $j=0; $j < $this->max_lines_per_page and $i < $numRows; $i++, $j++) {

					$res[]=$this->data[$i];
				}
			} else {
				$res= &$this->data;
			}

			$totalNumRows= $numRows;
			$numRows= count($res);

			if($numRows==0 and $this->show_no_rows) {
				html_showInfo($MESSAGES["NO_ROWS"]);
				return 1;
			}

			if($numRows!=0) $current_row=0;

			$values= Array();

			$form_ref= "document.forms." . $this->form_name;
			$row_id_ref= $form_ref . ".row_id_" . $this->doc_name;
			$action_ref= $form_ref . ".dw_action_" . $this->doc_name;

			$field_list= $this->headers ? $this->headers : array_keys($res[0]);

			$width= ($this->width) ? " width='$this->width'" : "";

?>
				<br>
				<table class="data_box_external" <?php echo $width; ?>>
<?php
				if($this->table_title != "") {
?>
				<tr class="data_box_title">
					<td align="left" class="data_box_title" colspan="3"><?php echo $this->table_title; ?></td>
				</tr>
<?php
				}

				if($this->show_toolbar) $this->show_toolbar_header($totalNumRows, $nav_str);
?>
				<tr><td colspan="3">
					<table class="data_box_rows" cellspacing="0">
						<tr>
<?php
						// FIELD HEADERS //////////////////////////////////
						foreach($field_list as $field_name) {
							echo "<th class='data_box_rows'>" . $field_name . "</th>";
						}

						// Extended methods
						if(method_exists($this, "post_show_row")) echo "\n<th class='data_box_rows'></th>";
						if($this->row_selection_enabled) echo "\n<th class='data_box_rows'><center>" . $this->show_select_all() . "</center></th>";
?>
						</tr>
<?php
						// FIELD VALUES ///////////////////////////////////
						$EvenRow= false;
						foreach($res as $row) {

							unset($values);
							$values= array();

							foreach($row as $key => $value) $values[]=$value;

							// PRE_SHOW_ROW ///////////////////////////////
							$can_update=false;
							$can_delete=false;
							if(!$this->pre_show_row($values, $can_update, $can_delete)) {
								continue;
							}

							// GET_ROW_COLOR //////////////////////////////
							$row_color= $this->get_row_color($EvenRow, $values);
							if($row_color!="") { $row_color="style='background-color: " . $row_color . ";'"; }

							if($EvenRow) { ?>
								<tr class='data_box_rows_tabular_even'>
<?php						} else { ?>
								<tr class='data_box_rows_tabular_odd'>
<?php						}

							// SHOW_ROW ///////////////////////////////////
							$id= $this->show_row($values);

							// The user want to add something at the end of the row?
							if(method_exists($this, "post_show_row")) {
?>
								<td class='data_box_cell'><center><?php $this->post_show_row($values); ?></center></td>
<?php						}

							$this->show_select_box($id);
?>
							</tr>
<?php
							$EvenRow= !$EvenRow;
						}

?>
						</table>
					</td></tr>
<?php
				$this->show_toolbar_footer($nav_str);
?>
				</table>
<?php

			if(method_exists($this,"post_retrieve")) {
				// The first table to show is the table which will store the
?>
				<table class='data_box_post'>
					<tr class='data_box_post'><td class='data_box_post'>
<?php						$stop= !$this->pre_postretrieve();
?>
					</td></tr>
				</table>
<?php			}

			return 1;
		}

		protected function show_row($values) {

			$first=1;
			$first_key=false;

			$cont=0;
			foreach($values as $key => $value) {
				if($first) {
					$first_key=$key;
					$first=0;
				}

				$cell_color= $this->get_cell_color($key, $value);
				$cell_color= $cell_color != "" ? "bgcolor=$cell_color" : "";

				$style_changer= (isset($this->col_align) and isset($this->col_align[$cont]) and ($this->col_align[$cont] != "")) ? " style='text-align: " . $this->col_align[$cont] . "'" : "";
			?><td <?php echo $cell_color; echo $style_changer; ?> class='data_box_cell'><?php echo $value; ?></td>
			<?php
				$cont++;
			}

			return $first_key;
		}

		protected function export_data() {
			global $MESSAGES;

			include_once INC_DIR . "forms/form_basic.inc.php";

			$rnd= rand(1000,2000);

			$name= ($this->save_as_name!="") ? $this->save_as_name : $this->doc_name;
			$base_name= $name . "_" . $rnd . ".csv";
			$file= "/tmp/$base_name";

			if(exportArrayCSV($this->data, $file)) {
				$url= HOME . "/tools/download.php?file=$base_name";
				?>
					<script>openWin("<?php echo $url; ?>")</script>
					<script>parent.close();</script>
				<?php

				html_showSuccess($MESSAGES["MGM_EXPORTED"]);
				return 0;
			} else {
				html_showError("Error exporting file");
				return 1;
			}
		}

		private function save_datawindow_action($row_id, $action) {

			$this->get_unique_doc_id();

			$row_id_ref= $this->get_unique_var("row_id");
			$action_ref= $this->get_unique_var("action");

			$_SESSION[$row_id_ref]= $row_id;
			$_SESSION[$action_ref]= $action;
		}

		private function restore_datawindow_action(&$row_id, &$action) {

			$row_id_ref= $this->get_unique_var("row_id");
			$action_ref= $this->get_unique_var("action");

			$row_id= $_SESSION[$row_id_ref];
			$action= $_SESSION[$action_ref];

		}

		private function get_unique_var($var_name) {

			return APP_NAME . $this->doc_ref . "." . $var_name;

		}

		protected function show_toolbar_header($numRows, $nav_str) {
			global $MESSAGES;



?>
					<tr class="data_box_toolbar">
						<td align="left" class="data_box_toolbar_tools">
<?php

						if($this->show_global_actions_first) {

							$this->show_global_actions();
							echo $this->create_group_actions_select();
							if($this->show_reload_button) { $this->show_reload_button(); }
							echo "&nbsp;";
							if($this->export_allowed) {	$this->show_export_button(); }

						} else {

							if($this->show_reload_button) { $this->show_reload_button(); }
							echo "&nbsp;";
							if($this->export_allowed) {	$this->show_export_button(); }
							$this->show_global_actions();
							echo $this->create_group_actions_select();

						}
?>
						</td>
						<td class="data_box_toolbar_num_rows" colspan="2">
<?php
							if($this->show_rows_info) { echo sprintf($MESSAGES["NUM_ROWS"], $numRows); }
?>
						</td>
					</tr>
					<tr class="data_box_toolbar">
						<td class="data_box_toolbar_nav" colspan="3">
							&nbsp;&nbsp;<?php echo $nav_str; ?>
						</td>
					</tr>
<?php
		}

		protected function show_toolbar_footer($nav_str) {
			if($nav_str=="") return;
?>
					<tr class="data_box_toolbar">
						<td align="right" class="data_box_toolbar_nav_footer" colspan="3">
							&nbsp;&nbsp;<?php echo $nav_str; ?>
						</td>
					</tr>
<?php
		}
	}
?>