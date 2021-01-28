<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage forms
 *
 */


require_once "basic.inc.php";

/**
 * file is the field type that implements the files
 * that are contained under a repository (system dir).
 *
 * The table field will contain the file name, referencing to a
 * file of the repository.
 */
class file extends field_type
{
	protected $repository;
	protected $show_current;
	private $system_repository;
	private $is_new;

	public function file($repository, $show_current = true)
	{
		$this->repository = $repository;
		$this->show_current = $show_current;
		$this->system_repository = SYSHOME . $repository;

		if (!file_exists($this->system_repository)) {
			if (!mkdir($this->system_repository)) {
				html_showError("Repository $this->system_repository doesn't exists and couldn't be created.");
				exit;
			}
		}
	}

	public function show($field_name, $readonly, $for_search = false)
	{
		global $MESSAGES;

		if ($readonly) {
			return parent::show($field_name, $readonly);
		}

		if ($for_search) {
			return  "<input type='text' name='$field_name'>";
		}

		echo $MESSAGES["FILE_NEW"] . ":&nbsp;&nbsp;";
		echo "<input type='file' class='file' name='file-type-" . $field_name  . "' onchange='$field_name.value=value'>";

		if ($this->show_current) {
			echo "<b>[</b><i>" . $MESSAGES["PARAMETERS_FIELD_NAME"] . ":</i>";
			echo "<input type='text' name='<?php echo $field_name; ?>' readonly='1'><b>]</b>";
		}
	}

	public function get_value_from_post($field_name)
	{
		global $_FILES;

		if (isset($_FILES["file-type-$field_name"])) {
			$ret = $_FILES["file-type-$field_name"]['name'];
		} else {
			$ret = "";
		}

		if ($ret != "") {
			$this->is_new = true;
			return $ret;
		}

		// ELSE...
		$this->is_new = false;
		$ret = get_http_param($field_name);
		return ($ret != null) ? $ret : false;
	}

	public function field_insert($field_name, $field_value)
	{
		global $_FILES;

		if (isset($_FILES["file-type-$field_name"])) {
			$this->save_file("file-type-$field_name");
			return 1;
		} else {
			return false;
		}
	}

	public function field_update($field_name, $old_field_value, $new_field_value)
	{
		global $_FILES;

		if ($new_field_value == "") return 1;

		if ($this->is_new === false) return 1;

		if (!$this->delete_file($old_field_value)) {
			return 0;
		}

		if (isset($_FILES["file-type-$field_name"])) {
			$this->save_file("file-type-$field_name");
			return 1;
		} else {
			return false;
		}

		return 1;
	}

	public function field_delete($field_name, $field_value)
	{
		return $this->delete_file($field_value);
	}


	private function save_file($field_name)
	{
		global $_FILES;

		if (!isset($_FILES[$field_name])) return 0;

		$uploadfile = $this->system_repository . "/" . basename($_FILES[$field_name]['name']);
		if (move_uploaded_file($_FILES[$field_name]['tmp_name'], $uploadfile)) {
			//
		} else {
			html_showError("Possible file upload attack!\n");
			exit;
		}
	}

	private function delete_file($file_name)
	{

		$file = $this->system_repository . "/" . basename($file_name);

		if (!file_exists($file)) {
			html_showError("File $file doesn't exists.");
			return false;
		}

		if (is_dir($file)) {
			if (!rmdir($file)) {
				html_showError("Couldn't delete the directory.");
				return false;
			}
		} else {

			if (!unlink($file)) {
				html_showError("Couldn't delete the file.");
				return false;
			}
		}
		return 1;
	}
}

class file_image extends file
{

	function get_value($field_value, $for_show = true)
	{
		if ($for_show) {
			$temp_file = htmlspecialchars($field_value);
			$img = HOME . "/" . $this->repository . "/" . htmlspecialchars($temp_file);
			return "<a href='#' onclick='openLookup(\"$img\")'><img align='middle' border='0' width='64' src='$img'></a> [$temp_file]";
		} else {
			return $field_value;
		}
	}
}
