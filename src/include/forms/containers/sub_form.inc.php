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
 * Abstract class to implement any kind of elements into a form
 *
 */
class sub_form extends form_element
{
	public $text;
	public $add_actions;

	/**
	 * Abstract class for form elements
	 *
	 * @param string $name, element name
	 * @param string $text
	 * @param bool $add_actions
	 * @return form_element
	 */
	public function sub_form($name, $text, $add_actions = false)
	{
		parent::form_element($name);

		$this->text = $text;
		$this->add_actions = $add_actions;
	}

	// Extend this function to include your own...
	protected function add_to_form()
	{
	}

	protected function action_accept()
	{
		return 0;
	}

	protected function action_cancel()
	{
		return 0;
	}

	public function event($event_type)
	{

		// Is comming from a submit?
		$sf_action = get_http_post_param("sf_action_" . $this->doc_name, 0);

		if (!$sf_action) return 0;

		$sf_action = "action_" . $sf_action;

		if (method_exists($this, $sf_action)) {
			return $this->$sf_action();
		} else {

			return parent::event($event_type);
		}
	}

	/**
	 * Echo the html code associated to the element into the form.
	 * Must set the object name into the
	 */
	public function show()
	{
		global $_POST;
		global $MESSAGES;

		if (!$this->visible) return;

		$this->show_pre_form();
		$this->add_to_form();

		foreach ($this->descents as $elem) {
			$this->show_pre_element();
			$elem->show();
			$this->show_post_element();
		}

		if ($this->add_actions) {
?>
			<tr bgcolor="#DDDDDD">
				<td colspan='2'>
					<center>
						<input type='submit' value='<?php echo $MESSAGES["BUTTON_ACCEPT"]; ?>' onclick='document.forms.<?php echo $this->form_name; ?>.element.value="<?php echo $this->doc_name; ?>";document.forms.<?php echo $this->form_name; ?>.sf_action_<?php echo $this->doc_name; ?>.value="accept";document.forms.<?php echo $this->form_name; ?>.submit()'>
						<input type='button' class='action' value='<?php echo $MESSAGES["BUTTON_CANCEL"]; ?>' onclick='document.forms.<?php echo $this->form_name; ?>.element.value="<?php echo $this->doc_name; ?>";document.forms.<?php echo $this->form_name; ?>.sf_action_<?php echo $this->doc_name; ?>.value="cancel";document.forms.<?php echo $this->form_name; ?>.submit()'>
					</center>
				</td>
			</tr>
		<?php
		}

		$this->show_post_form();
	}

	protected function show_pre_element()
	{
		echo "<tr><td><table boder=0><tr class='sub_form_element'><td class='sub_form_element'>";
	}

	protected function show_post_element()
	{
		echo "</td></tr></table></td></tr>";
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
					<table class='sub_form_element'>
						<?php if ($this->text != "") { ?>
							<tr class="sub_form_title">
								<td align="left" class="sub_form_title"><?php echo $this->text; ?></td>
							</tr>
						<?php					}
					}

					protected function show_post_form()
					{
						?>
					</table>
				</td>
			</tr>
		</table>
<?php
	}
}
