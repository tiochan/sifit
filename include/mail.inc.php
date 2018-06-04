<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage mail
 *
 * Mail related functions
 * require:
 * - init.inc.php loaded
 */

	require_once SYSHOME . "/conf/mail.conf.php";

	global $NO_SHOW_MAIL_LOG;
	$old_value= $NO_SHOW_MAIL_LOG;

	global $GLOBALS;
	if(!isset($GLOBALS["attach"])) $GLOBALS["attach"]=array();


	function send_user_mail($id_user, $subject, $content, $method="plain") {
		global $global_db;

		if(defined("AVOID_EMAIL") and (AVOID_EMAIL === true)) return;

		$query="select email, send_notifications from " . DBName . ".users where id_user='$id_user'";
		$res= $global_db->dbms_query($query);
		if(!$global_db->dbms_check_result($res)) {
			html_showError("User id $id_user doesn't exists.");
			return;
		}

		list($email, $send_notification)= $global_db->dbms_fetch_row($res);
		$global_db->dbms_free_result($res);

		if($send_notification != "1") {
			html_showError("User id $id_user don't want to receive notifications.");
			return;
		}

		return send_mail($email, $subject, $subject, $content, $method);
	}

	/**
	 * Send a email to each user of a group. Those must have activated
	 * the "send_notifications" flag at the users management page.
	 *
	 * @param integer $id_group
	 * @param string $subject
	 * @param string $content
	 * @param string $method
	 */
	function send_group_mail($id_group, $subject, $content, $method="plain") {

		global $global_db;
		global $MESSAGES;

		if(defined("AVOID_EMAIL") and (AVOID_EMAIL === true)) return;

		$query="select distinct email from " . DBName . ".users where id_group='$id_group' and send_notifications='1'";
		$res= $global_db->dbms_query($query);

		$ret= true;
		if($global_db->dbms_check_result($res)) {
			while($row=$global_db->dbms_fetch_row($res)) {
				$to= $row[0];
				$ret= $ret and send_mail($to, $subject, $subject, $content, DEFAULT_EMAIL_METHOD);
			}
		}

		$global_db->dbms_free_result($res);

		return $ret;
	}

	/**
	 * Send an email by default in plain format to all users with admin level [0]
	 * Can use TAGs. See /conf/mail.conf.php.
	 *
	 * @param integer $level, the level of the users to send to
	 * @param string $page_title
	 * @param string $subject
	 * @param string $content, the email body
	 * @param string $method ["plain" | "html"]
	 * @return notifications_sent, the number of notifications that have been sent.
	 */
	function send_admins_mail($page_title, $subject, $content, $method="plain") {

		if(defined("AVOID_EMAIL") and (AVOID_EMAIL === true)) return;

		send_level_mail(0, $subject, $subject, $content, $method);
	}

	/**
	 * Send an email by default in plain format to all users with group admin level [3]
	 * Can use TAGs. See /conf/mail.conf.php.
	 *
	 * @param integer $level, the level of the users to send to
	 * @param string $page_title
	 * @param string $subject
	 * @param string $content, the email body
	 * @param string $method ["plain" | "html"]
	 * @return notifications_sent, the number of notifications that have been sent.
	 */
	function send_group_adm_level_mail($page_title, $subject, $content, $method="plain") {

		if(defined("AVOID_EMAIL") and (AVOID_EMAIL === true)) return;

		send_level_mail(3, $subject, $subject, $content, $method);
	}

	/**
	 * Send an email by default in plain format to all users with user level [5]
	 * Can use TAGs. See /conf/mail.conf.php.
	 *
	 * @param integer $level, the level of the users to send to
	 * @param string $page_title
	 * @param string $subject
	 * @param string $content, the email body
	 * @param string $method ["plain" | "html"]
	 * @return notifications_sent, the number of notifications that have been sent.
	 */
	function send_user_level_mail($page_title, $subject, $content, $method="plain") {

		if(defined("AVOID_EMAIL") and (AVOID_EMAIL === true)) return;

		send_level_mail(5, $subject, $subject, $content, $method);
	}

	/**
	 * Send an email by default in plain format.
	 * Can use TAGs. See /conf/mail.conf.php.
	 *
	 * @param integer $level, the level of the users to send to
	 * @param string $page_title
	 * @param string $subject
	 * @param string $content, the email body
	 * @param string $method ["plain" | "html"]
	 * @return notifications_sent, the number of notifications that have been sent.
	 */

	function send_level_mail($level, $page_title, $subject, $content, $method="plain") {

		global $global_db;

		if(defined("AVOID_EMAIL") and (AVOID_EMAIL === true)) return;

		$query="select email from users where level='$level' and send_notifications=1";
		$res= $global_db->dbms_query($query);

		if($global_db->dbms_check_result($res)) {
			while($row=$global_db->dbms_fetch_row($res)) {
				$to= $row[0];
				send_mail($to, $subject, $subject, $content, $method);
			}
		}

		// send_mail($to, $subject, $subject, $content, $method);
	}

	/**
	 * Send a email to each user of a group. Those must have activated
	 * the "send_notifications" flag at the users management page.
	 *
	 * @param integer $id_group
	 * @param string $subject
	 * @param string $content
	 * @param string $method
	 */
	function send_group_report($id_group, $subject, $content, $method="plain") {

		global $global_db;
		global $MESSAGES;

		if(defined("AVOID_EMAIL") and (AVOID_EMAIL === true)) return;

		html_showDetail("Sending report to group $id_group<br>");

		$query="select distinct email from " . DBName . ".users where id_group='$id_group' and send_notifications='1'";
		$res= $global_db->dbms_query($query);

		if($global_db->dbms_check_result($res)) {
			while($row=$global_db->dbms_fetch_row($res)) {

				$to= $row[0];

				echo "- Sending mail to $to, reporting the results.\n";
				send_mail($to, $subject, $subject, $content, DEFAULT_EMAIL_METHOD, "vmail.tpl.php");
			}

		} else {
			echo "- No users found on this group.\n";
		}
		echo "\n";

		$global_db->dbms_free_result($res);
	}

	/**
	 * Send an email by default in plain format.
	 * Can use TAGs, see /conf/mail.conf.php
	 *
	 * @param string $to Destination recipents
	 * @param string $page_title
	 * @param string $subject
	 * @param string $content  (the email body)
	 * @param string $method ["plain" | "html"]
	 * @return notifications_sent, the number of notifications that have been sent.
	 */

	function send_mail($to, $page_title, $subject, $content, $method="plain", $template="default.tpl.php", $from="") {

		require_once SYSHOME . "/include/html.inc.php";
		global $GLOBALS;

		if(defined("AVOID_EMAIL") and (AVOID_EMAIL === true)) return;

//		$subject= "[" . APP_NAME . "] $subject";

		if($from == "")	$from= get_user_email();

		if(($from== "") and !CLI_MODE) {
			html_showError("You have to have an email address set on your user for sending mail.");
			return 0;
		}

		if(($from== "") and CLI_MODE) $from= ADM_EMAIL;

		if($method == "plain") {

			$body_plain= tags_to_text($content);
			$body_plain= wordwrap($body_plain, 70);

			$ret=mail($to, $subject, $body_plain);

		} else {

			$semi_rand = md5(time());
			$mime_boundary= str_pad($semi_rand,38,'-',STR_PAD_LEFT);

			$content_html= tags_to_html($content);
			$content_html= build_html_page($page_title, "", $content_html,$template);

			$semi_rand = md5(date('u'));
			$over_mime_boundary= str_pad($semi_rand,38,'-',STR_PAD_LEFT);

			$header= <<< HEADER
From: $from
Content-Type: multipart/alternative;
 boundary="$over_mime_boundary"


HEADER;

			$body=	<<< BODY
--$over_mime_boundary
Content-Type: multipart/related;
 boundary="$mime_boundary"


--$mime_boundary
Content-Transfer-Encoding: 7bit
Content-Type: text/html; charset=UTF-8

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
</head>
<body bgcolor="#ffffff" text="#000000">
$content_html
</body>
</html>

BODY;

			// Now, attach the images or files inline.
			// Those scripts must use the $GLOBALS["attach"]
//			if(CLI_MODE) {

				if(isset($GLOBALS["attach"]) and count($GLOBALS["attach"])) {

					for($i=0; $i < count($GLOBALS["attach"]); $i++) {

						if($GLOBALS["attach"][$i] != "") {
							$body.= <<< IMAGE
--$mime_boundary

IMAGE;
							$body.= $GLOBALS["attach"][$i];
							$GLOBALS["attach"][$i]="";
						}
					}
					$body.= <<< END

$mime_boundary--

END;

//				}
			}

			$subject= '=?UTF-8?B?'.base64_encode($subject).'?=';
			$ret=mail($to, $subject, $body, $header);
		}
//Content-Type: text/html; charset=ISO-8859-1

		global $NO_SHOW_MAIL_LOG;

		if($NO_SHOW_MAIL_LOG == true) return $ret;

		if(!$ret) html_showError("Could not send mail to $to<br>");
		else html_showDetail("Mail sent to $to<br>");

		return $ret;
	}

	function & get_attachments($content) {

		preg_match_all('#==Multipart_Boundary_x([a-z0-9\-_]*?)x#is', $content, $attachments, PREG_OFFSET_CAPTURE);
		return $attachments;
	}

	function get_user_email() {

		global $global_db, $USER_ID;

		if(defined("AVOID_EMAIL") and (AVOID_EMAIL === true)) return;

		if(isset($USER_ID) and $USER_ID != "") {
			$query= "select email from users where id_user='" . $USER_ID . "'";
			$res= $global_db->dbms_query($query);

			if(!$global_db->dbms_check_result($res)) return "";

			list($email)= $global_db->dbms_fetch_row($res);
			$global_db->dbms_free_result($res);
		} else {
			$email= ADM_EMAIL;
		}

		return $email;
	}
