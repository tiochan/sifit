<?php
/**
 * @author Jorge Novoa (jorge.novoa@upcnet.es)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage conf
 *
 * Datawindow class for parameters management.
 */

/*
	Table definition

	+--------------+--------------+------+-----+---------+----------------+
	| Field        | Type         | Null | Key | Default | Extra          |
	+--------------+--------------+------+-----+---------+----------------+
	| id_tag       | mediumint(9) | NO   | PRI | NULL    | auto_increment |
	| tag_name     | varchar(60)  | NO   | UNI | NULL    |                |
	| calc_method  | varchar(60)  | YES  |     | NULL    |                |
	| description  | varchar(255) | YES  |     | NULL    |                |
	| value        | text         | YES  |     | NULL    |                |
	| extrainfo    | text         | YES  |     | NULL    |                |
	| connection   | varchar(255) | YES  |     | NULL    |                |
	| is_public    | tinyint(1)   | NO   |     | 1       |                |
	| id_user      | mediumint(9) | YES  |     | NULL    |                |
	| id_group     | mediumint(9) | YES  |     | NULL    |                |
	| is_protected | tinyint(1)   | NO   |     | 0       |                |
	+--------------+--------------+------+-----+---------+----------------+

*/

	$AUTH_REQUIRED=true;
	$AUTH_LVL=5;			// ADMINS ONLY

	include_once "../include/functions.php";	

	$show_header_param= get_http_param("show_header","false");
	$show_header= ($show_header_param == "true");
	if(!$show_header) define("QUIET",true);

	define("SHOW_MENU",false);

	include_once "../include/init.inc.php";
	include INC_DIR . "/reports/reports.class.php";

	global $global_db, $USER_LEVEL;
	
	if($show_header) html_header("Tag preview");


	$tag_id= get_http_param("detail_tag_id",false);
	if($tag_id === false) {
		$tag_name= get_http_param("detail_tag_name",false);
		if($tag_name === false) {
			html_showError("** Tag id expected **", true);
			html_footer();
			exit;
		} else {
			$query="select tag_name from report_tags where tag_name='$tag_name'";
		}
	} else {
		$query="select tag_name from report_tags where id_tag='$tag_id'";
	}

	$res= $global_db->dbms_query($query);

	if(!$global_db->dbms_check_result($res)) {
		html_showError("** Tag id not found **", true);
		if($show_header) html_footer();
		exit;
	}

	list($tag_name)= $global_db->dbms_fetch_row($res);
	$global_db->dbms_free_result($res);

	$tag= new tag($tag_name);
	
	$parameters= get_http_params();
	foreach($parameters as $parameter => $value) {
		$tag->add_parameter($parameter, $value);
	}

	// Report access: only generic (id_group is null) and own group reports
	if($USER_LEVEL != 0 and $tag->is_public != 1) {
		html_showError("** Not allowed to see this tag **", true);
	} else {
		$content= $tag->get_value();
		if(get_http_param("show_header","false")=="true") {
			echo "<br><p><b>Showing preview for tag</b>: $tag_name</p>";
			echo "<p>(Shown after horizontal line)</p><br><hr>";			
		}
		echo $content;
	}

	if($show_header) html_footer();
?>
