<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage forms
 *
 */

/**
 * The layout is a form element, that allow to set the location of each
 * object into the form.
 *
 * Initially, will generate a table layout and will "show" each object into
 * its cell.
 *
 */

include_once INC_DIR . "/forms/containers/sub_form.inc.php";


class form_layout extends sub_form
{

	public $text;
	protected $layout;
	protected $rows;
	protected $cols;

	/**
	 * Constructor
	 *
	 * @param string $name, element name
	 * @param string $text, text to display as layout title
	 * @return form_element
	 */
	public function form_layout($name, $text)
	{
		parent::sub_form($name, $text);

		$this->text = $text;
		$this->layout = array();
	}

	public function add_element($row, $col, &$element)
	{

		parent::add_element($element);

		if (!isset($this->layout[$row])) $this->layout[$row] = array();
		if (!isset($this->layout[$row][$col])) $this->layout[$row][$col] = array();

		// Save the reference ID to the object
		$this->layout[$row][$col][] = $element->doc_name;

		$this->rows = max($this->rows, $row);
		$this->cols = max($this->cols, $col);
	}

	/*
		// Extend this function to include your own...
		abstract function add_to_form() {
		}
		*/

	/**
	 * Echo the html code associated to the element into the form.
	 * Must set the object name into the
	 */
	public function show()
	{
		global $_POST;

		if (!$this->visible) return;

		$this->show_pre_form();

		if (method_exists($this, "add_to_form")) {
			$this->show_pre_row();
			echo "<td class='form_layout'>";
			$this->add_to_form();
			echo "</td>";
			$this->show_post_row();
		}

		for ($row = 0; $row <= $this->rows; $row++) {

			$this->show_pre_row();

			for ($col = 0; $col <= $this->cols; $col++) {

				$this->show_pre_element();

				if (isset($this->layout[$row][$col])) {

					foreach ($this->layout[$row][$col] as $elem)
						$this->descents[$elem]->show();
				} else {
					echo "&nbsp;";
				}

				$this->show_post_element();
			}

			$this->show_post_row();
		}

		$this->show_post_form();
	}

	protected function show_pre_form()
	{
?>
		<br>
		<input type='hidden' name='_action' value='0'>
		<input type='hidden' name='sf_action_<?php echo $this->doc_name; ?>' value=''>
		<table class='sub_form_external' width="100%">
			<tr class='sub_form_external'>
				<td class='sub_form_external'>
					<table class='form_layout'>
						<?php if ($this->text != "") { ?>
							<tr class="form_layout">
								<td align="left" class="form_layout" colspan="<?php echo ($this->cols + 1); ?>"><?php echo $this->text; ?></td>
							</tr>
<?php					}
	}

	protected function show_pre_row()
	{
		echo "<tr class='sub_form_element'>";
	}

	protected function show_pre_element()
	{
		echo "<td class='sub_form_element'>";
	}

	protected function show_post_element()
	{
		echo "</td>";
	}

	protected function show_post_row()
	{
		echo "</tr>";
	}
}
