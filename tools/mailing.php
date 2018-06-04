<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage admin
 *
 */


	$AUTH_REQUIRED=true;
	$AUTH_LVL=3;			// Group admins or greater

	include_once "../include/init.inc.php";
	include_once SYSHOME . "/include/mail.inc.php";
	include_once SYSHOME . "/include/forms/forms.inc.php";
	include_once SYSHOME . "/include/forms/field.inc.php";
	include_once SYSHOME . "/include/forms/containers/sub_form.inc.php";
	include_once SYSHOME . "/include/forms/form_elements/label.inc.php";
	include_once SYSHOME . "/include/forms/form_elements/button.inc.php";
	include_once SYSHOME . "/include/forms/form_elements/field_box.inc.php";




	class btn_send extends button {

		function clicked() {
			// Check destinatary
			$group= get_http_post_param("group", "-1");
			$level= get_http_post_param("level", "-1");
			$users= get_http_post_param("users", "-1");

			if( ($group == -1) and ($level == -1) and ($users == -1)) {
				html_showError("Please, select at least one destinatary");
				return 0;
			}

			// Check message
			if( ($subject= get_http_post_param("subject","")) == "") {
				html_showError("Write a mail subject");
				return 0;
			}

			if( ($content= get_http_post_param("msg","")) == "") {
				html_showError("Write a mail message");
				return 0;
			}

			$info="";
			if($group != -1 and !(count($group) == 1 and $group[0]== "-1")) {
				$info.="Sending mail to groups:<br>";
				foreach($group as $value) {
					$info.= "- $value<br>";
					$ret=send_group_mail($value, $subject, $content, DEFAULT_EMAIL_METHOD);
				}
			} elseif ($level != -1 and !((count($level) == 1 and $level[0]== "-1"))) {
				$info.="Sending mail to levels:<br>";
				foreach($level as $value) {
					$info.= "- $value<br>";
					$ret=send_level_mail($value, $subject, $subject, $content, DEFAULT_EMAIL_METHOD);
				}
			} elseif ($users != -1 and !(count($users) == 1 and $users[0]== "-1")) {
				$info.="Sending mail to users:<br>";
				foreach($users as $value) {
					$info.="- $value<br>";
					$ret=send_user_mail($value, $subject, $content, DEFAULT_EMAIL_METHOD);
				}
			} else {
				html_showError("Please, select at least one destinatary");
				return 0;
			}

			if($ret) {
				html_showSuccess("Mail sent");
			}

			return 0;
		}
	}

	$form_name= "form_mailing";
	html_header("Mailing");

	/* TO Form */

	global $USER_LEVEL;

	$fields= array();
	if($USER_LEVEL == 0) {
		$lg= new list_groups(true);
		$ll= new list_levels(true);
		$fields[]= new field("group","Group","list_groups", false, false, true, true,null,$lg);
		$fields[]= new field("level","Level","list_levels", false, false, true, true,null, $ll);
	}

	if($USER_LEVEL <= 3) {
		$lu= new list_users(true);
		$fields[]= new field("users","Users","list_users", false, false, true, true, null, $lu);
	}

	$fb_to= new field_box("field_box","",$fields);

	$lbl_to= new label("lbl_to","<b>Note:</b> Select values from one list.<br>If you select values on more than one list, the first will be used.");

	$sfrm_to= new sub_form("subfrm1","To:");
	$sfrm_to->add_element($lbl_to);
	$sfrm_to->add_element($fb_to);

	/* MSG Form */
	$lbl_msg= new label("lbl_msg","<b>Note:</b> You can use tags:<br>" .
	                             " - <b>{USER_NAME}</b> will be replaced with the name of the user.<br>" .
	                             " - <b>{USER_LAST}</b> will be replaced with the last name of the user."
	                             );

	$fields2= array();
	$fields2[0]= new field("subject","Subject","string",false, false, true, true);


	$fields2[1]= new field("msg","Text","html",false, false, true, true);
	//$fields2[1]= new field("msg","Text","text",false, false, true, true);

	$fb_msg= new field_box("field_box2","",$fields2);
	$btn= new btn_send("btn_submit","Send");

	$sfrm_msg= new sub_form("subfrm2","Msg:");
	// $sfrm_msg->add_element($lbl_msg);
	$sfrm_msg->add_element($fb_msg);
	$sfrm_msg->add_element($btn);


	$frm= new form($form_name);
	$frm->add_element($sfrm_to);
	$frm->add_element($sfrm_msg);

	$frm->form_control();

	html_footer();
?>