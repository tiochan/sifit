<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage forms
 *
 */

	include_once SYSHOME . "/include/forms/field.inc.php";
	include_once SYSHOME . "/include/forms/field_types/files.inc.php";
	include_once SYSHOME . "/include/forms/form_elements.inc.php";
	include_once SYSHOME . "/include/forms/form_elements/common_database_elements.inc.php";


	class system_browser extends common_database_elements {

		protected $main_dir;				// Admin given base dir (will not change)
		protected $base_dir;				// Navigated current dir (change on navigation)
		protected $list_files;
		protected $file_field;

		protected $insert_allowed;
		protected $delete_allowed;
		protected $navigation_allowed;

		/**
		 * button constructor
		 *
		 * @param string $name, internal button name
		 * @param string $text, the text to display
		 * @return button
		 */
		function system_browser($base_dir, $insert_allowed, $delete_allowed, $navigation_allowed) {



			$this->main_dir= $base_dir;
			$this->base_dir= $base_dir;
			$this->insert_allowed= $insert_allowed;
			$this->delete_allowed= $delete_allowed;
			$this->navigation_allowed= $navigation_allowed;

			// Navigation
			if($this->navigation_allowed) {
				if(isset($_POST["SYS_BWR_current_dir"]) and $_POST["SYS_BWR_current_dir"] != "") {
					$this->base_dir = $_POST["SYS_BWR_current_dir"];
				}
				// Check for cross scripting..,
				if(strpos($this->base_dir, "..") !== false) {
					$this->base_dir= $base_dir;
				}
			}

			// Create the field structure
			$this->file_field= new file($this->base_dir, false);

			$this->fields= array();
			$this->fields[]= new field("file_browser","","file",true,false,true,true,null,$this->file_field);

			$base_dir= str_replace("/","",$base_dir);
			parent::common_database_elements("system_browser_" . $base_dir, $this->fields, true, true, true);

			$this->identifier= $this->fields[0]->name;
		}

		public function action_retrieve() {
			return 0;
		}

		protected function action_start_insert($row_id) {

			if(!$this->insert_allowed) {
				if(DEBUG) {
					html_showError("Datawindow::ERROR: insert is not allowed!");
				} else {
					html_showError("DW[14-1]: Construction error. Contact administrator");
				}
				exit;
			}

			$this->show_insert_form("insert_row");
			return 1;
		}

		protected function action_start_delete($row_id) {

			if(!$this->delete_allowed) {
				if(DEBUG) {
					html_showError("Datawindow::ERROR: delete is not allowed!");
				} else {
					html_showError("DW[12-1]: Construction error. Contact administrator");
				}
				exit;
			}

			if($row_id == -1) {
				if(DEBUG) {
					html_showError("Datawindow::ERROR: datawindow::delete_row, row_id not defined.");
				} else {
					html_showError("DW[3]: Construction error. Contact administrator");
				}
				exit;
			}

			return $this->delete_row($row_id);
		}

		protected function action_insert_row($row_id) {

			if(!$this->check_parameters()) {					// Some parameters not set...
				$this->show_insert_form("insert_row");
				$this->recover_values_from_call();
				return 1;
			}

			$values= $this->get_values();

			if($this->insert_row($values)) {
				// $this->show_return_form();
			} else {
				$this->show_insert_form("insert_row");
				$this->recover_values_from_call();
				return 1;
			}

			return 0;
		}

		public function action_updir() {
			$this->go_up();
			return 0;
		}

		public function action_newdir() {
			global $MESSAGES;

			if(!isset($_POST["dir_name"]) or (trim($_POST["dir_name"]== ""))) {
				html_showError($MESSAGES["DIR_CANT_BE_VOID"]);
			} else {
				$this->create_dir($_POST["dir_name"]);
			}

			return 0;
		}

		public function show_hidden() {

			if($this->hidden_shown) return;
			?>

			<input type='hidden' name='SYS_BWR_current_dir' value='<?php echo  $this->base_dir; ?>'>
			<?php

			parent::show_hidden();
		}


		protected function pre_show_row(&$values, &$can_update, &$can_delete) {
			$can_update=1;
			$can_delete=1;
			return 1;
		}


		function retrieve() {
			global $PHP_SELF;
			global $start_in;
			global $MESSAGES;

			if(!isset($_POST["start_in"])) {
				$start_in=0;
			} else {
				$start_in= $_POST["start_in"];
			}

			$colors= Array();
			$colors[true]="#F2F2F2";
			$colors[false]="#FFFFFF";

			$stop=false;

			// Perhaps the inherited class want to put something here! (hidden fields, buttons, etc.)
			if(method_exists($this,"pre_retrieve")) {
				// The first table to show is the table which will store the
?>
				<table class='data_box_previous'>
					<tr class='data_box_previous'><td class='data_box_previous'>
							<?php $stop= !$this->pre_retrieve(); ?>
					</td></tr>
				</table>
<?php
			}

			if($stop) {
				html_showInfo("Info: datawindow::retrieve cancelled by user.");
				return 0;
			}


			$files= read_dir(SYSHOME . "/" . $this->base_dir);

			asort($files);

			$numRows= count($files);

			if(($numRows==0) and !$this->insert_allowed and !$this->delete_allowed) {
				html_showInfo($MESSAGES["NO_ROWS"]);
				return 1;
			}

			$num_fields= count($this->fields);
			$values= Array();

			$form_ref= "document.forms." . $this->form_name;
			$row_id_ref= $form_ref . ".row_id";
			$action_ref= $form_ref . ".dw_action";


			?>
			<table class="data_box_external">
				<tr class="data_box_toolbar">
					<td align="left" class="data_box_toolbar_tools">
						<?php	$this->show_reload_button();
							if($this->insert_allowed) echo "&nbsp;";$this->show_insert_button();
							if($this->navigation_allowed) echo "&nbsp;";$this->show_updir_button();
							?>
					</td>
					<td class="data_box_toolbar_num_rows" colspan="2">
						<?php
						echo "<b>" . $this->base_dir . "</b>, " . sprintf($MESSAGES["NUM_FILES"], $numRows);
						?>
					</td>
				</tr>
						<?php	if($this->insert_allowed) { ?>
				<tr class="data_box_toolbar">
					<td align="left" class="data_box_toolbar_tools" colspan=2>
						<?php	$this->show_new_dir();
						?>
					</td>
						<?php } ?>
					<td align="left" class="data_box_toolbar_tools">
						<?php	$this->show_preview_option();
						?>
					</td>
				</tr>
				<tr>
					<td colspan="3">
					<table class="data_box_rows" cellspacing="0">
						<tr>
						<?php

						// Field headers
						for($i=0; $i < $num_fields; $i++) {
							if($this->fields[$i]->name=="") continue;
							if($this->fields[$i]->visible) {
								echo "<th class='data_box_rows'>" . $this->fields[$i]->alias . "</th>";
							}
						}

						if(isset($_POST["file_preview"])) {
							echo "<th class='data_box_rows' width='64'>Preview</th>";
						}
						echo "<th class='data_box_rows' width='100'>size (Bytes)</th>";
						echo "<th class='data_box_rows' width='100'>Date</th>";
						echo "<th class='data_box_rows' width='60'>Time</th>";

						if($this->delete_allowed) {
							?><th class='data_box_rows' width='30'>&nbsp;Del</th>
							<?php
						}

						if(method_exists($this, "post_show_row")) {
							?><th class='data_box_rows'></th>
							<?php
						}
						?></tr>
						<?php

						/**
						 *
						 *	MAIN LOOP
						 *
						 */
						$filaPar= false;
						foreach($files as $values) {

							// What does the user to say for this row?
							$can_delete=true;
							if(!$this->pre_show_row($values, $can_update, $can_delete)) {
								continue;
							}

							if($filaPar) { ?>
								<tr class='data_box_rows_tabular_even'>
<?php							} else { ?>
								<tr class='data_box_rows_tabular_odd'>
<?php							}

							$id=$this->identifier;
							$value=0;
							// Field values
							$file_name= $this->fields[0]->get_value($values);
							if(method_exists($this,"get_default_action")) {
								$value= $this->get_default_action($file_name);
							} else {
								$value= "<a href='" . SERVER_URL . "/" . HOME . $this->base_dir . "/" . $file_name . "' target='_blank'>" . $this->get_file_string($file_name) . "</a>";
							}
							?><td class='data_box_cell'><?php echo $value; ?></td>
							<?php
							for($i=1; $i < $num_fields; $i++) {
								if($this->fields[$i]->name=="") continue;

								$value= $this->fields[$i]->get_value($values);

								if($this->fields[$i]->visible) {
									?><td class='data_box_cell'><?php echo $value; ?></td>
									<?php
								}
							}

							if(isset($_POST["file_preview"])) {
								?><td class='data_box_cell'><?php echo $this->show_preview($file_name); ?></td><?php
							}?>
							<td class='data_box_cell' style='text-align: right'><?php echo $this->file_size($file_name); ?></td>
							<td class='data_box_cell'><?php echo $this->file_date($file_name); ?></td>
							<td class='data_box_cell'><?php echo $this->file_time($file_name); ?></td>
							<?php

							if($can_delete and $this->delete_allowed and ($id != "")) {
								?><td class='data_box_cell'><?php $this->show_delete_button($file_name); ?></td>
								<?php
							}

							// The user want to add something at the end of the row?
							if(method_exists($this, "post_show_row")) {
								?><td class='data_box_cell'><?php $this->post_show_row($values); ?></td>
								<?php
							}

							?></tr>
							<?php

							$filaPar= !$filaPar;
						}

						?>
					</table>
				</td></tr>
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


		/**
		 * PRIVATE
		 *
		 *  - For every field, checks for its status.
		 *    if field is required and it has value, if value has the same type than field has, ...
		 *  - Finally calls the f_own_check if defined in the inherited class.
		 *
		 * @return integer, 1=Ok, 0=Error
		 */
		function check_parameters() {
			global $_POST;
			global $MESSAGES;
			global $global_db;

			for($i=0; $i<count($this->fields); $i++) {

				$tmp_field= $this->fields[$i];
				$tmp_name= $this->fields[$i]->name;
				$tmp_alias=$this->fields[$i]->alias;

				$value= $tmp_field->get_value_from_post();

				if($tmp_field->required and $tmp_field->updatable) {

					if($value === false) {
						html_showError(sprintf($MESSAGES["FIELD_REQUIRED"],$tmp_alias));
						return 0;
					}

					if($value=="") {
						html_showError(sprintf($MESSAGES["FIELD_REQUIRED"],$tmp_alias));
						return 0;
					}

					if(!$tmp_field->check($value)) {
						html_showError(sprintf($MESSAGES["FIELD_TYPE_INCORRECT"],$tmp_alias));
						return 0;
					}
				}
			}

			if(method_exists($this,"my_check")) {

				$values=$this->get_values();
				return $this->my_check($values);
			}

			return 1;
		}


/*		function show_insert_form($action) {
			global $MESSAGES;
			global $PHP_SELF;


?>
			<center>
			<table class="data_box_external">
<?php
			if(method_exists($this,"pre_show_form")) { ?>
				<tr class="data_box_external"><td>
<?php
				$this->pre_show_form(false); ?>
				</td></tr>
<?php
			}
		?>
				<tr class="data_box_external"><td class="data_box_external">
					<table class="data_box_rows">
		<?php
			$first_field="";
			for($i=0; $i<count($this->fields); $i++) {


				if(!$this->fields[$i]->will_be_displayed(true)) {
					continue;
				}

				if($this->fields[$i]->type=="html") {?>
					<tr class='data_box_rows_list'>
						<td class='data_box_edit_cell_alias' colspan='2'><?php
					echo $this->fields[$i]->alias;
					?><br><?php
				} else { ?>
					<tr>
						<td class='data_box_edit_cell_alias' valign='top'><?php
					echo $this->fields[$i]->alias;
					?></td><td class='data_box_edit_cell'><?php
				}

				$f_name= $this->fields[$i]->name;
				if(($first_field=="") and ($this->fields[$i]->visible)) {
					$first_field= $f_name;
				}

				$this->fields[$i]->show($f_name,true);

				if($this->fields[$i]->required) {
					?><font color='red'>*</font><?php
				}
				?></td></tr>
<?php
			}

			if(method_exists($this,"post_show_form")) {
				$this->post_show_form(false);
			}
		?>
					</table>
				</td></tr>

				<tr bgcolor="#DDDDDD"><td colspan='2'>
				<center>
				<input type='submit' value='<?php echo $MESSAGES["BUTTON_ACCEPT"]; ?>' onclick='document.forms.<?php echo $this->form_name; ?>.element.value="<?php echo $this->doc_name; ?>";document.forms.<?php echo $this->form_name; ?>.dw_action.value="<?php echo $action; ?>";document.forms.<?php echo $this->form_name; ?>.submit()'>
				<input type='button' value='<?php echo $MESSAGES["BUTTON_CANCEL"]; ?>' onclick='document.forms.<?php echo $this->form_name; ?>.element.value="<?php echo $this->doc_name; ?>";document.forms.<?php echo $this->form_name; ?>.dw_action.value="retrieve";document.forms.<?php echo $this->form_name; ?>.submit()'>
	       		</center>
	       		</td></tr>

			</table>
			</center>
			<script language="JavaScript" type="text/javascript">
				if(document.forms.<?php echo $this->form_name; ?>.<?php echo $first_field; ?>) {
					document.forms.<?php echo $this->form_name; ?>.<?php echo $first_field; ?>.focus();
				}
			</script>

		<?php
			$values= Array();

			for($i=0;$i<count($this->fields);$i++) {
				$this->fields[$i]->set_form_default_value($this->form_name);
			}
		}
*/
		/**
		 * PROTECTED
		 *
		 * @param array $values, associative array with the value of each row field.
		 */
		function insert_row($values) {
			global $_POST;
			global $MESSAGES;
			global $global_db;

			if(!$this->pre_insert($values)) {
				return 0;
			}

			$ok= true;
			for($i=0; $i<count($this->fields); $i++) {
				$ok= $this->fields[$i]->field_insert($values[$this->fields[$i]->name]);
				if($ok === false) break;
			}

			if($ok === false) {
				html_showError($MESSAGES["MGM_ERROR_CREATING"] . ": " . $global_db->dbms_error());
				$global_db->dbms_rollback();
				return 0;
			}

            $row= implode(",", $values);
            //log_write("FORM","Row inserted: $row");

			if(!$this->post_insert($values)) {
				return 0;
			}

			html_showSuccess($MESSAGES["MGM_CREATED"]);
			return 1;
		}

		/**
		 * PROTECTED
		 * - Deletes the row identified by row_id post var.
		 */
		function delete_row($row_id, $values) {
			global $_POST;
			global $MESSAGES;
			global $global_db;

			if($this->identifier!="") {

				if(!$this->pre_delete($row_id,array())) {
					return 0;
				}

				$ok= true;

				for($i=0; $i<count($this->fields); $i++) {
					$ok= $this->fields[$i]->field_delete($row_id);
					if($ok === false) break;
				}

				if($ok === false) {
					html_showError($MESSAGES["MGM_ERROR_DELETING"] . ": " . $global_db->dbms_error());
					return 0;
				}

				html_showSuccess($MESSAGES["MGM_DELETED"]);
				return 0;

			} else {
				// This rows has not id, and user delete method is not defined --> ERROR
				if(DEBUG) {
					html_showError("system_browser::ERROR: there is not any idetifier field and f_own_delete() is not defined!");
				} else {
					html_showError("SYS_BRW[14]: Error de fluxe. Consulteu a l'administrador");
				}
				exit;
			}
			return 0;
		}


		private function file_size ($file_name) {

			$complete_path= SYSHOME . "/" . $this->base_dir . "/" . $file_name;
			if(is_dir($complete_path)) {
				return "<img src='" . ICONS . "/folder_grey.png'>";
				return "<font color=blue><b>" . htmlspecialchars("[dir]") . "</b></font>";
			}

			if(!($file_size=filesize($complete_path))) {
				return "error";
			}

			//return round($file_size / 1024, 3);		// To Bytes?
			return $file_size;
		}


		private function file_date ($file_name) {

			$complete_path= SYSHOME . "/" . $this->base_dir . "/" . $file_name;

			return date ("Y-m-d", filemtime($complete_path));
		}

		private function file_time ($file_name) {

			$complete_path= SYSHOME . "/" . $this->base_dir . "/" . $file_name;

			return date ("H:i:s", filemtime($complete_path));
		}

		protected function get_file_string($file_name) {

			$ret= "";

			if(is_dir(SYSHOME . "/" . $this->base_dir . "/" . $file_name) and $this->navigation_allowed) {
				$ret= "<a href='#'
					onclick='document.forms." . $this->form_name . ".SYS_BWR_current_dir.value=\"". $this->base_dir . "/" . $file_name . "\";
					   		 document.forms." . $this->form_name . ".submit()
					   		'><b>$file_name</b></a>";
			} else {
				$ret= $file_name;
			}

			return $ret;
		}

		protected function show_updir_button() {
			global $MESSAGES;

			echo $this->create_row_action("..", "updir", -1, ICONS . "/up_1.png");
		}

		protected function show_new_dir() {
			global $MESSAGES;

			//echo $MESSAGES["DIR_NEW"] . "&nbsp;";
			echo "<input type='text' name='dir_name'>&nbsp;";
			echo $this->create_row_action($MESSAGES["DIR_NEW"],"newdir",-1,ICONS . "/folder_new.png");
		}

		private function go_up() {

			if(($first_pos= strpos($this->base_dir, "/")) === false) {
				$this->base_dir = $this->main_dir;
				return 0;
			}
			if(($last_pos= strrpos($this->base_dir, "/")) === false) {
				$this->base_dir = $this->main_dir;
				return 0;
			}
			/*
			if($first_pos == $last_pos) {
				echo "Auto change [3]<br>";
				$this->base_dir = $this->main_dir;
				return 0;
			}
			*/
			// Don't let to up more levels than base_dir.
			$this->base_dir = substr($this->base_dir, 0, $last_pos);
			rtrim($this->base_dir, "/");

			if((strpos($this->base_dir, $this->main_dir)) === false) {
				$this->base_dir = $this->main_dir;
				return 0;
			}
			?><script>document.forms.<?php echo $this->form_name; ?>.SYS_BWR_current_dir.value="<?php echo $this->base_dir; ?>"</script><?php
		}

		private function show_preview($file_name) {

			$ret="";
			$ext= substr($file_name, strlen($file_name) - 3);

			$relative_file= SERVER_URL . "/" . HOME . $this->base_dir . "/" . $file_name;
			$default_icon= ICONS . "/file.png";

			if(in_array($ext, array("png","gif","jpg"))) {
				$ret="<a target='_blank' border=0 href='$relative_file'><img border=0 height='48' src='" . $relative_file . "'></a>";
			} else {
				$ret="<a target='_blank' border=0 href='$relative_file'><img border=0 height='48' src='" . $default_icon . "'></a>";
			}

			return $ret;
		}

		private function show_preview_option() {
			global $_POST;

			echo "Preview? <input type='checkbox' class='action' name='file_preview'";
			if(isset($_POST["file_preview"])) echo " checked ";
			echo ">";
		}

		private function create_dir($dir_name) {
			global $MESSAGES;

			$complete_dir_name= SYSHOME . "/" . $this->base_dir . "/" . $dir_name;

			if(mkdir($complete_dir_name)) {
				html_showSuccess($MESSAGES["MGM_CREATED"]);
				return 1;
			} else {
				html_showError($MESSAGES["MGM_ERROR_CREATING"]);
				return 0;
			}
		}
	}
?>