<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage lookups
 *
 * Lookup javascript side functions.
 *
 * Those functions are required to link a page with a lookup field with the
 * lookup page.
 */

	/**
	 * For lookup fields (the opener window, not the lookup window)
	 *
	 * Creates the action to open a lookup window against a URL
	 *
	 */
	include_once SYSHOME . "/include/html.inc.php";

	global $GLOBAL_SCRIPTS;
	$GLOBAL_SCRIPTS[]="" .
"		function lookup_function(lookup_url,field_name,parameters) {
			var url;

			url= lookup_url + \"?lookup_field=\" + field_name + parameters;
			openLookup(url);
		}
";



	/**
	 * For Lookup Windows.
	 *
	 * This function is needed by forms.
	 * The form will automatically manage a lookup field if exists.
	 *
	 * This way will perpetuate the values across multiples calls.
	 */
	class lookup_form_field extends form_element {

		public $lookup_field;

		public function lookup_form_field($doc_name) {

			$this->lookup_field= get_http_param("lookup_field", false);

			parent::form_element($doc_name);
			if($this->lookup_field) {
?>
				<script language='javascript'>

				function set_values(myId, myDescription) {

					var lookup_field="<?php echo $this->lookup_field; ?>";

					if(lookup_field == "") {
						alert("ERROR: Empty lookup field name");
					} else {

						opener.set_value('<?php echo $this->lookup_field; ?>',myId);
						opener.set_value('description_<?php echo $this->lookup_field; ?>',myDescription);
					}
					close(self);
				}

				function cancel() {
					close(self);
				}
			</script>
<?php
			}
		}

		public function show() {

			parent::show();
		}

		public function show_hidden() {

			if($this->lookup_field) {
				echo "<input type='hidden' name='lookup_field' value='" . $this->lookup_field . "'>";
				parent::show_hidden();
			}
		}

		public function set_values($id, $description) {
?>
			<script>
				set_values('<?php echo $id; ?>','<?php echo $description; ?>');
			</script>
<?php
		}
	}
?>