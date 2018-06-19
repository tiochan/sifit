<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sifit
 * @subpackage reports
 *
 * Tags definition class
 *
 */

	include_once INC_DIR . "/forms/field_types/listbox.inc.php";
	include_once INC_DIR . "/reports/tags/tag_element.class.php";


	class tag_image extends tag_element {

		protected $show_connection= false;


		public function get_value() {

			$this->replace_parameters();

			$filename= SYSHOME . "/" . $this->value;

			return paste_image($filename);
		}

		static public function check_value($value) {

			$filename= SYSHOME . "/" . $value;
			if(!file_exists($filename)) {
				html_showError("Can't find image file: $filename");
				return 0;
			}

			return 1;
		}

		protected function change_field_properties(&$field) {
			$field->reference= new image_tag();
			$field->alias= "Image";
		}
	}

	/**
	 * Images must be located under application dir (SYSHOME)
	 *
	 */
	class image_tag extends listbox {

		protected $image_dirs=array(
				IMAGES,
				ICONS,
				MY_IMAGES,
				MY_ICONS,
				"/include/reports/images",
				"/my_include/reports/images"
			);

		public function image_tag() {

			parent::listbox();

			$this->lb[""]="";


			$len= strlen(SYSHOME);

			foreach($this->image_dirs as $dir) {

				if(file_exists(SYSHOME . $dir)) {

					$files= read_dir(SYSHOME . $dir);
					foreach($files as $file) {
						if(is_dir($dir . "/" . $file)) continue;
						$this->lb[$dir . "/" . $file]= $dir . "/" . $file;
					}
				}
			}

			asort($this->lb);
		}
	}
