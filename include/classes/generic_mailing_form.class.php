<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage admin
 *
 */

	include_once INC_DIR . "/mail.inc.php";
	include_once INC_DIR . "/forms/field.inc.php";
	include_once INC_DIR . "/forms/containers/sub_form.inc.php";
	include_once INC_DIR . "/forms/form_elements/label.inc.php";
	include_once INC_DIR . "/forms/form_elements/button.inc.php";
	include_once INC_DIR . "/forms/form_elements/field_box.inc.php";


	class btn_send_general extends button {

		function clicked() {
			// Check destinatary
			$from= get_http_post_param("mail_from");
			$to= get_http_post_param("mail_to");
			$subject= get_http_post_param("mail_subject");
			$msg= get_http_post_param("mail_msg");

			$all= $from and $to and $subject and $msg;
			if(!$all) {
				html_showError("Please, enter all parameters");
				return 0;
			}

			if(send_mail($to, $subject, $subject, $msg, "html", "default.tpl",$from)) {
				html_showSuccess("Mail sent");
			} else {
				html_showError("Error sending Mail");
			}

			return 0;
		}
	}

	class generic_mailing_form extends sub_form {

		public function generic_mailing_form($name, $text) {

			global $USER_LEVEL;

			parent::sub_form($name, $text);


			// TO block
			$fields= array();
			if($USER_LEVEL == 0) {
				$fields[]= new field("mail_from","From","string", false, false, true, true);
				$fields[0]->reference->size=50;
				$fields[]= new field("mail_to","To","string", false, false, true, true);
				$fields[1]->reference->size=50;
				$fields[]= new field("mail_subject","Subject","string",false, false, true, true);
				$fields[2]->reference->size=75;
				$fields[]= new field("mail_msg","Text","html",false, false, true, true);
			}

			// MESSAGE block
			$fb_msg= new field_box("field_box_generic","",$fields);
			$btn= new btn_send_general("btn_submit_generic","Send");

			$sfrm= new sub_form("subfrm2","Msg:");
			$sfrm->add_element($fb_msg);
			$sfrm->add_element($btn);

			$this->add_element($sfrm);
		}
	}
