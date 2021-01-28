<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage forms
 *
 */

// Load form styles
$GLOBAL_HEADERS["form_elements"] = "<link rel='stylesheet' type='text/css' href='" . HOME . "/include/styles/form_elements.css'>";
if (file_exists(MY_INC_DIR . "/my_styles/form_elements.css")) {
	$GLOBAL_HEADERS["my_form_elements"] = "<link rel='stylesheet' type='text/css' href='" . HOME . "/my_include/styles/form_elements.css'>";
}

class form_element
{

	public $doc_name;
	public $parent;

	public $doc_ref; 		// unique document reference on the form.

	public $first_time;		// Did this object exist on last submit?
	public $visible;

	public $form_name;		// Set by form class, when a form_element is added to it.
	protected $descents;

	protected $hidden_shown;

	public function form_element($doc_name, $doc_ref = "")
	{

		$this->doc_name = $doc_name;
		$this->descents = array();
		$this->parent = null;

		$this->doc_ref = $doc_ref . $this->doc_name;

		$this->first_time = (get_http_post_param($this->doc_ref, false) === false);

		$this->visible = true;
		$this->hidden_shown = false;
	}

	/**
	 * Adds a new descend
	 *
	 * @param form_element $element
	 */
	public function add_element(&$element)
	{

		if (isset($this->descents[$element->doc_name])) {
			html_showError("ERROR: form_element::add_element, Element " . $element->doc_name . " just exists.");
			exit;
		}

		$this->descents[$element->doc_name] = &$element;
		$element->parent = $this;
		$element->adopted();
	}

	public function propagate_form_name($form_name)
	{

		$this->form_name = $form_name;

		foreach ($this->descents as $elem) {
			$elem->propagate_form_name($form_name);
		}
	}

	public function get_form_name()
	{
		return $this->parent->get_form_name();
	}

	/**
	 * This method is called when this object is added to other
	 *
	 */
	public function adopted()
	{
	}

	public function replace_element($element_name, &$new_element)
	{

		foreach ($this->descents as $key => $elem) {
			if ($elem->doc_name == $element_name) {
				unset($this->descents[$key]);
				$this->add_element($new_element);
				return true;
			}
		}

		foreach ($this->descents as $elem) {

			if ($elem->replace_element($element_name, $new_element)) {
				return true;
			}
		}
		return false;
	}


	/**
	 * Return the reference to the element or false if doesn't exists
	 *
	 * @param string $element_name
	 */
	public function exists_element($element_name, &$ret)
	{

		if ($this->doc_ref == $element_name) {
			$ret = $this;
			return true;
		}

		foreach ($this->descents as $elem) {
			if ($elem->exists_element($element_name, $ret)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Echo the html code associated to the element into the form.
	 * Must set the object name into the
	 */
	public function show()
	{

		if (!$this->visible) return;

		foreach ($this->descents as $elem) {
			$elem->show();
		}
	}

	/**
	 * If the object need to write hidden HTML fields, here is the moment to do it.
	 *
	 * @param string form_name, the form which contains this element
	 * @return string
	 */
	public function show_hidden()
	{

		if ($this->hidden_shown) return;
?>
		<input type='hidden' name='<?php echo $this->doc_ref; ?>' value='0'>
<?php

		if (!$this->descents) return;

		foreach ($this->descents as $elem) {
			$elem->show_hidden();
		}

		$this->hidden_shown = true;
	}

	/**
	 * This function will be invoked when an event occurs on this object.
	 * (If by code is defined a submit())
	 *
	 * @param unknown_type $event_type
	 * @return integer, [ 0 == continue and call show for all elements, 1 == stop and don't show elements ]
	 */
	public function event($event_type)
	{
		return 0;
	}

	/**
	 * This function will be invoked when an event occurs on this object.
	 * (If by code is defined a submit())
	 * Propagates an event through the descents.
	 *
	 * @param string $object_name
	 * @param string $event_type
	 * @return integer, [ 0 == continue and call show for all elements, 1 == stop and don't show elements ]
	 */
	public function send_event($element_name, $event_type)
	{

		if ($this->doc_name == $element_name) {
			return $this->event($event_type);
		}

		foreach ($this->descents as $elem) {
			if ($elem->send_event($element_name, $event_type)) {
				return true;
			}
		}

		return false;
	}

	public function set_property($property, $value)
	{

		// Is this property defined?
		$arr = get_class_vars(get_class($this));
		if (array_key_exists($property, $arr)) {
			$this->$property = $value;
		}

		$this->changed($property);
	}

	/**
	 * This function is used to tell the object that a property has changed.
	 *
	 * @param string $property, the property that has been changed.
	 */
	public function changed($property)
	{
	}

	function get_unique_doc_id()
	{

		// No se puede usar el parent, porque si se realiza una consulta desde el constructor todavía no se ha asignado a ningún elemento.
		return $this->doc_ref;
	}

	function post_show_form()
	{
		if (!$this->descents) return;

		foreach ($this->descents as $elem) {
			$elem->post_show_form();
		}
	}

	/**
	 * Returns true if none of the parents is invisible, else returns false.
	 *
	 */
	protected function will_be_shown()
	{

		return ($this->visible and $this->parent->will_be_shown());
	}
}

/**
 * This generic function checks if a element $element_name is son of $parent_name
 *
 * @param string $element_name
 * @param string $parent_name
 * @return integer, 1 = Belongs, 0 = doesn't belongs.
 */
function belongs_to($element_name, $parent_name)
{
	global $form;

	if (!isset($form)) return false;

	$form->exists_element($parent_name, $ret);
	if (!$ret) return false;

	$ret->exists_element($element_name, $ret2);
	if ($ret2) {
		return true;
	} else {
		return false;
	}
}
