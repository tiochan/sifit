<?php
/**
 * @author Jorge Novoa (jorge.novoa@upcnet.es)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sifit
 * @subpackage reports
 *
 * Tags definition class
 *
 */

	include_once INC_DIR . "/reports/tags/tag_element.class.php";


	class tag_webservice extends tag_element {

		protected $show_connection= false;


		public function get_value() {

			global $USER_ID;

			$this->replace_parameters();

			$filename= SYSHOME . "/plugins/ws/" . $this->value . ".php";
			if(!file_exists($filename)) {
				return "** Error: can't locate Web Service file " . $filename . " **";
			}

			$url= SERVER_URL . HOME . "/plugins/ws/" . $this->value . ".php";
			$function = $this->value;

			try {
				$client= new SoapClient(null, array('location' => $url, 'uri' => $function));
				$result= $client->$function($USER_ID,$this->parameters);
			} catch (SoapFault $e) {
				return "** Error $e **";
			}

			return $result;
		}

		static public function check_value($value) {

			$filename= SYSHOME . "/plugins/ws/" . $value . ".php";
			if(!file_exists($filename)) {
				html_showError("Can't find Web Service file: $filename");
				return 0;
			}

			$url= SERVER_URL . HOME . "/plugins/ws/" . $value . ".php";
			$function = $value;

			try {
				$client= new SoapClient(null, array('location' => $url, 'uri' => $function));
				$result= $client->$function($USER_ID);
			} catch (SoapFault $e) {
				html_showError("Check web service:<br>Error: $e");
				return 0;
			}

			html_showInfo("Result from Web Service (using current user and environment values):<br> $result");

			return 1;
		}
	}

