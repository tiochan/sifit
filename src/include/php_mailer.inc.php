<?
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

/**
 *
 * References:
 *
 * https://github.com/PHPMailer/PHPMailer
 * http://stackoverflow.com/questions/12301358/send-attachments-with-php-mail
 */

require_once SYSHOME . "/conf/mail.conf.php";
require_once INC_DIR . "/html.inc.php";
require_once INC_DIR . "/PHPMailer-master/class.phpmailer.php";


/**
 * Send an email by default in plain format, but using PHPMailer.
 * Can use TAGs, see /conf/mail.conf.php
 *
 * @param string $to Destination recipents
 * @param string $page_title
 * @param string $subject
 * @param string $content  (the email body)
 * @param string $method ["plain" | "html"]
 * @param string $template
 * @param string $from
 * @param null $attachments, the array of {path + filename} to attach to.
 * @param null $embeddedAttachments
 * @return notifications_sent, the number of notifications that have been sent.
 */

function send_mail_PHPMailer($to, $page_title, $subject, $content, $method="plain", $template="default.tpl.php", $from="", $attachments=null, $embeddedAttachments=null) {

	global $GLOBALS;


	if($from == "")	$from= get_user_email();

	if(($from== "") and !CLI_MODE) {
		html_showError("You have to have an email address set on your user for sending mail.");
		return 0;
	}

	if(($from== "") and CLI_MODE) $from= ADM_EMAIL;

	$email = new PHPMailer();
	$email->From      = $from;
//	$email->FromName  = 'xxxx';
	$email->Subject   = $subject;

	$email->addAddress($to);

	if(!empty($attachments)) {
		foreach($attachments as $attachment) {
			$filename= basename($attachment);
			$email->addAttachment($attachment, $filename);
		}
	}

	if($method == "plain") {

		$body_plain= tags_to_text($content);
		$body_plain= wordwrap($body_plain, 70);

		$email->Body= $body_plain;
		$email->isHTML(false);

		$ret= $email->Send();

	} else {

		if(!empty($embeddedAttachments)) {
			foreach($embeddedAttachments as $embeddedAttachment) {
				$cid= $embeddedAttachment[0];
				$image= $embeddedAttachment[1];
				$email->addStringEmbeddedImage($image, $cid);
			}
		}

		$content_html= tags_to_html($content);
		$content_html= build_html_page($page_title, "", $content_html,$template);


		$email->Body= $content_html;
		$email->isHTML(true);

		$ret= $email->Send();
		//$ret=mail($to, $subject, $body, $header);
	}

	global $NO_SHOW_MAIL_LOG;

	if($NO_SHOW_MAIL_LOG == true) return $ret;

	if(!$ret) html_showError("Could not send mail to $to<br>");
	else html_showDetail("Mail sent to $to<br>");

	return $ret;
}
