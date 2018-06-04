<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package admin
 *
 * Marks page.
 */

	$AUTH_REQUIRED=false;


	include_once "../../include/init.inc.php";

	define("SHOW_MENU",false);
	html_showSimpleBody();


	global $global_db;

	$unique_key= get_http_param("id",0);
	if(!$unique_key) die("Key not set (parameter id)");

	$vote= intval(get_http_param("vote",0));
	if(!$unique_key) die("Vote not set (parameter vote)");

	$unique_key= addslashes($unique_key);
	$query="SELECT user_id from mark_keys where user_key='$unique_key'";
	$res= $global_db->dbms_query($query);
	if(!$global_db->dbms_check_result($res)) die("Key not found");

	$row= $global_db->dbms_fetch_row($res);
	$global_db->dbms_free_result($res);

	$user_id= $row[0];

	$query="insert into day_poll (id_user, vote_date, vote) values ('$user_id', date(now()), '$vote')";
	$global_db->dbms_query($query);
	$error= $global_db->dbms_error();
	if($error) die("Error inserting vote: " . $error);

	if($vote < 4) echo ("Apa, que estar&aacute;s cansado, eh?.");
	elseif($vote < 7) echo "Hasta ma&ntilde;ana!";
	else echo "Un mal dia, veo. Ves a tomarte unas cervezas, o a que te den un masaje.";
	echo "<br>";
	echo "\n<br><br><br><hr><input type='button' onclick='document.location.href=\"" . HOME . "/login.php?id=$unique_key\"' class='action' value='Go to marks'>";
	html_showSimpleFooter();
?>