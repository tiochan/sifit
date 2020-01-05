<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage admin
 *
 * Filters related functions.
 */

	include_once INC_DIR . "/mail.inc.php";
	include_once INC_DIR . "/output.inc.php";
	include_once SYSHOME . "/my_include/filters.inc.php";

	/**
	 * Sort the vulnerability array using the severity property.
	 * The final order is: high, medium, low, and other (like unknown).
	 *
	 * @param unknown_type $vuln_array
	 * @return unknown
	 */
	function sort_vulnerability_array($vuln_array) {

		$high=array();
		$med=array();
		$low=array();
		$other= array();

		foreach($vuln_array as $vuln) {

			$severity= strtolower($vuln->severity);
			switch($severity) {
				case "high":
					$high[]=$vuln;
					break;
				case "medium":
				case "med":
					$med[]=$vuln;
					break;
				case "low":
					$low[]=$vuln;
					break;
				default:
					$other[]=$vuln;
					break;
			}
		}

		$my_vuln_array= array_merge($high, $med, $low, $other);

		return $my_vuln_array;
	}

	/**
	 * Send a vulnerability report to each administrator of the application. Those must have activated
	 * the "receive all" flag at the users management page.
	 *
	 * @param unknown_type $vuln_array
	 */
	function send_vulnerability_report($vuln_array) {
		global $global_db;
		global $MESSAGES;

		$email="";

		$date= date("F d \of o");
		$page_title= "SIGVI, Daily vulnerability report [$date]";
		$date= date("Y-m-d");
		$subject= "SIGVI, daily vulnerability report ($date)";

		// Select the users that have enabled send_notifications as well as receive_all flags
		$query="select id_user, email from users where receive_all='1' and send_notifications='1'";
		$res= $global_db->dbms_query($query);

		if($global_db->dbms_check_result($res)) {
			while($row=$global_db->dbms_fetch_row($res)) {

				$id_user= $row[0];
				$to= $row[1];

				$email= get_vulnerability_report($vuln_array, $id_user, true);

				send_mail($to, $subject, $subject, $email, DEFAULT_EMAIL_METHOD, "vmail.tpl");
			}
		}

		$global_db->dbms_free_result($res);
	}

	/**
	 * Return the array of vulnerabilities that complains the user filter identified by $id_user
	 *
	 * @param unknown_type $vuln_array
	 * @param integer $id_user
	 */
	function get_vulnerability_report($vuln_array, $id_user, $use_filter=true) {
		global $global_db;
		global $MESSAGES;

		$my_vuln_array= sort_vulnerability_array($vuln_array);

		$report="";
		$tmp_report="";
		$reported=0;
		$total=0;

		foreach($my_vuln_array as $vuln) {

			if(!$vuln->stored) continue;
			$total++;

			if(!$use_filter or vuln_pass_user_filter($id_user, $vuln)) {
				$reported++;

				$vreport= build_body($vuln, true, (DEFAULT_EMAIL_METHOD == "html"));
				$tmp_report.= $vreport . "\n";
			}
		}

		if($reported != $total) {
			$info="<BOLD>Info:</BOLD>  Reporting $reported vulnerabilities of $total (using your filter).<LINEBREAK>";
			// $report="<BOLD>Note:</BOLD> Using your user filter were reported $reported vulnerabilities, but $total were inserted.<LINEBREAK>" . $report;
			$report= "<PARAGRAPH>$info</PARAGRAPH>";
		}

		if(DEFAULT_EMAIL_METHOD == "html") {
			$report.=mount_vreport($tmp_report);
		} else {
			$report.= $tmp_report;
		}

		return $report;
	}

	/**
	 * Mount the header, the body and the tail of the table representing the data.
	 *
	 * @param unknown_type $report
	 * @return unknown
	 */
	function mount_vreport($report) {
		global $MESSAGES;

		$ret= "<PARAGRAPH>" .
				"<TABLE BORDER='1'>" .
				"<TH>" . $MESSAGES["NOTIFICATION_VULN_ID"] . "</TH>" .
				"<TH>SEV</TH>" .
				"<TH>CVSS score</TH>" .
				"<TH>REM</TH>" .
				"<TH>SPT</TH>" .
				"<TH>APV</TH>" .
				"<TH>SPV</TH>" .
				"<TH>CNF</TH>" .
				"<TH>INT</TH>" .
				"<TH>AVA</TH>" .
				"<TH>" . $MESSAGES["VULN_FIELD_DESCRIPTION"] . "</TH>" .
				"<TH>" . $MESSAGES["VULN_FIELD_VULN_SOFTWARE"] . "</TH>" .
				"<TH>+ inf</TH>" .
				$report .
				"</TABLE>" .
				"</PARAGRAPH>" .
				"<TABLE>" .
				"<TH>Key</TH><TH>Type<TH>Desc</TH>" .
				"<TR><TD><BOLD>SEV&nbsp;&nbsp;</BOLD></TD><TD>" . str_replace("<br>","&nbsp;&nbsp;</TD><TD>", $MESSAGES["VULN_FIELD_SEVERITY"]) . "</TD></TR>" .
				"<TR><TD><BOLD>REM</BOLD></TD><TD>" . str_replace("<br>","&nbsp;&nbsp;</TD><TD>", $MESSAGES["VULN_FIELD_AR_LAUNCH_REMOTELY"]) . "</TD></TR>" .
				"<TR><TD><BOLD>LOC</BOLD></TD><TD>" . str_replace("<br>","&nbsp;&nbsp;</TD><TD>", $MESSAGES["VULN_FIELD_AR_LAUNCH_LOCALLY"]) . "</TD></TR>" .
				"<TR><TD><BOLD>SPT</BOLD></TD><TD>" . str_replace("<br>","&nbsp;&nbsp;</TD><TD>", $MESSAGES["VULN_FIELD_SECURITY_PROTECTION"]) . "</TD></TR>" .
				"<TR><TD><BOLD>APV</BOLD></TD><TD>" . str_replace("<br>","&nbsp;&nbsp;</TD><TD>", $MESSAGES["VULN_FIELD_OBTAIN_ALL_PRIV"]) . "</TD></TR>" .
				"<TR><TD><BOLD>SPV</BOLD></TD><TD>" . str_replace("<br>","&nbsp;&nbsp;</TD><TD>", $MESSAGES["VULN_FIELD_OBTAIN_SOME_PRIV"]) . "</TD></TR>" .
				"<TR><TD><BOLD>CNF</BOLD></TD><TD>" . str_replace("<br>","&nbsp;&nbsp;</TD><TD>", $MESSAGES["VULN_FIELD_CONFIDENTIALITY"]) . "</TD></TR>" .
				"<TR><TD><BOLD>INT</BOLD></TD><TD>" . str_replace("<br>","&nbsp;&nbsp;</TD><TD>", $MESSAGES["VULN_FIELD_INTEGRITY"]) . "</TD></TR>" .
				"<TR><TD><BOLD>AVA</BOLD></TD><TD>" . str_replace("<br>","&nbsp;&nbsp;</TD><TD>", $MESSAGES["VULN_FIELD_AVAILABILITY"]) . "</TD></TR>" .
				"</TABLE>";

		return $ret;
	}
?>