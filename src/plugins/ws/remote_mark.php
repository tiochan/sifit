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



	function show_poll($unique_key) {

		$colors= array();

		$colors[0]= "#ffffff";
		$colors[1]= "#ddffff";
		$colors[2]= "#aaffff";
		$colors[3]= "#99ff99";
		$colors[4]= "#33ff33";
		$colors[5]= "#ffffaa";
		$colors[6]= "#ffff66";
		$colors[7]= "#ffaa66";
		$colors[8]= "#ff6666";
		$colors[9]= "#ff0000";



		$style="text-align: center; width: 15px; border: 1px #000000 solid; padding: 10px;cursor: pointer;"

?>
		<br>
		<br>
		<div style='text-align: center; border: 1px #000000 solid; padding: 10px;'>
		<table>
			<tr>
				<td colspan=11>
					De 1 a 10, c&oacute;mo ha sido el d&iacute;a, siendo:<br>
						- 1: rascada total<br>
						- 5: d&iacute;a normal<br>
						- 10: propicio para el premio Kal&aacute;shnikov<br>
				</td>
			</tr>
			<tr>
				<td></td>
<?php
				for($i=0, $j=1; $i < 10; $i++, $j++) {
					echo "<td style='$style; background-color: " . $colors[$i] . "' onclick='window.location.href=\"" . HOME . "/plugins/ws/poll.php?id=$unique_key&vote=$j\"'>" . ($i + 1) . "</td>\n";
				}
?>
			</tr>
		</table>
		</div>
<?php
	}

	$unique_key= get_http_param("id",0);
	if(!$unique_key) die("Key not set (parameter id)");

	$unique_key= addslashes($unique_key);
//	$query="SELECT user_id from mark_keys where user_key='$unique_key'";
	$query="SELECT m.user_id, u.username from mark_keys m, users u where m.user_key='$unique_key' and m.user_id=u.id_user";
	$res= $global_db->dbms_query($query);
	if(!$global_db->dbms_check_result($res)) die("Key not found");

	$row= $global_db->dbms_fetch_row($res);
	$global_db->dbms_free_result($res);

	$user_id= $row[0];
	$user_name= $row[1];

	$query="select * from time_marks where user_id='$user_id' and mark_date= date(now())";
	$res= $global_db->dbms_query($query);
	if($global_db->dbms_check_result($res)) {
		$global_db->dbms_free_result($res);

		$query="update time_marks set marks= concat(marks,',',date_format(now(),'%H:%i')) where user_id='$user_id' and mark_date= date(now())";
	} else {
		$query="insert into time_marks (user_id, mark_date, marks) values ('$user_id', date(now()), date_format(now(),'%H:%i'))";
	}

	$global_db->dbms_query($query);
	$error= $global_db->dbms_error();
	if($error) die("Error inserting mark: " . $error);

	$query="select mark_date, marks, minutes from time_marks where user_id='$user_id' and mark_date= date(now())";

	$res= $global_db->dbms_query($query);
	if(!$global_db->dbms_check_result($res)) die("Error fetching marks: " . $global_db->dbms_error());

	list($date, $marks, $minutes)= $global_db->dbms_fetch_row($res);
	$global_db->dbms_free_result($res);

	$hours= floor($minutes / 60);
	$min= $minutes % 60;


	$parts= explode(",", $marks);
	if((count($parts) % 2) == 0) {
		echo "<p>Bye $user_name</p>";
		echo "<p>Marks on date $date -> $marks | $hours h $min min.</p>";
		show_poll($unique_key);
	} else {
		echo "<p>Hello $user_name</p>";
		echo "<p>Marks on date $date -> $marks | $hours h $min min.</p>";
	}

	echo "\n<br><br><br><hr><input type='button' onclick='document.location.href=\"" . HOME . "/login.php?id=$unique_key\"' class='action' value='Go to marks'>";

	html_showSimpleFooter();
?>