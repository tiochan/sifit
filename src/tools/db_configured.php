<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage dbms
 *
 */


	$AUTH_REQUIRED=true;
	$AUTH_LVL=0;

	include "../include/init.inc.php";
	include_once "../include/forms/forms.inc.php";
	include_once "../include/forms/form_basic.inc.php";
	global $MESSAGES;

	if(!isset($_SESSION[APP_NAME . "_DBMS_QUERYES"])) $_SESSION[APP_NAME . "_DBMS_QUERYES"]= array();


	html_header($MESSAGES["DDBB_CURRENT_HEADER"]);

	error_reporting(E_ALL);

	$clear_previous_queries= get_http_param("clear_previous_queries");
	if($clear_previous_queries=="1") {
		unset($_SESSION[APP_NAME . "_DBMS_QUERYES"]);
		$_SESSION[APP_NAME . "_DBMS_QUERYES"]= array();
		$query="";
	} else {
		$query= stripslashes(get_http_param("p_query", ""));
	}

	$num_rows= get_http_param("p_num_rows", 0);
	$num_rows= is_numeric($num_rows) ? $num_rows : 0;
	$offset= get_http_param("p_offset", 0);
	$offset= is_numeric($offset) ? $offset : 0;

?>
	<script type="text/javascript">

		function disableCopyPaste(elm) {
		    // Disable cut/copy/paste key events
		    elm.onkeydown = interceptKeys

		    // Disable right click events
		    elm.oncontextmenu = function() {
		        return false
		    }
		}

		function interceptKeys(evt) {
		    evt = evt||window.event // IE support
		    var c = evt.keyCode
		    var ctrlDown = evt.ctrlKey||evt.metaKey // Mac support

		    // Check for Alt+Gr (http://en.wikipedia.org/wiki/AltGr_key)
		    if (ctrlDown && evt.altKey) return true

		    // Check for ctrl+c, v and x
		    else if (ctrlDown && c==67) return false // c
		    else if (ctrlDown && c==86) return false // v
		    else if (ctrlDown && c==88) return false // x

		    // Otherwise allow
		    return true
		}

		function executeQuery(evt) {
		    evt = evt||window.event // IE support
		    var c = evt.keyCode
		    var ctrlDown = evt.ctrlKey||evt.metaKey // Mac support

		    // Check for ctrl+e
		    if (ctrlDown && c==69) return true

		    return false
		}
	</script>

	<form method="POST">
		<input type='hidden' name='clear_previous_queries' value='false'>
		<table class='data_box_rows'>
			<tr><td class="sub_form_title"><b>Query (current date: <?php echo date('H:i:s'); ?>)</b></td><td class="sub_form_title">DB Info</td></tr>
			<tr style='background-color: #efffef;'>
				<td>
					<textarea class='courier' rows=15 cols=60 name="p_query" style="width: 600px"
						onkeyDown="javascript:if(executeQuery(event)) {document.forms[0].submit();};"
						><?php echo $query ?></textarea>
				</td>
				<td valign="top">
					<table>
						<tr><td><b>DBType:</b></td><td><input readonly value='<?php echo DBType; ?>'></td></tr>
						<tr><td><b>DBServer:</b></td><td><input readonly value='<?php echo DBServer; ?>'></td></tr>
						<tr><td><b>DBname:</b></td><td><input readonly value='<?php echo DBName; ?>'></td></tr>
						<tr><td><b>DBUser:</b></td><td><input readonly value='<?php echo DBUser; ?>'></td></tr>
					</table>
					<hr><b>Query options:</b>
					<table>
						<tr>
							<td>Num rows:</td>
							<td><input name='p_num_rows' value='<?php echo $num_rows; ?>' size=5></td>
						</tr>
						<tr>
							<td>Offset:</td>
							<td><input name='p_offset' value='<?php echo $offset; ?>' size=5></td>
						</tr>
					</table>
					<hr>
					<input type='submit' value='Execute query (Ctrl+e)'>
				</td>
			</tr>
		</table>
	</form>

	<script>
		// document.forms[0].p_query.focus();
	</script>

	<table class='data_box_rows'>
		<tr>
			<td class='sub_form_title' colspan='3'>Query Result</td>
		</tr>
		<tr>
			<td class='data_box_cell' style='border-left: 1px solid #aaddaa; border-top: 1px solid #aaddaa;'>
				<div id="" style="overflow:scroll; height:400px;">
<?php

	if($query != "") {
		global $global_db;

		if($num_rows) {
			$query= $global_db->dbms_limit($query, $num_rows, $offset);
		}

		$res=$global_db->dbms_query($query);

		if($global_db->dbms_error() != "") {
			$ok="ERROR";
			html_showWarning("<b>ERROR EXECUTING QUERY:</b><hr><p><font color='red'>$query</font></p><hr><b>ERROR MESSAGE:</b><br>" . $global_db->dbms_error());
		} else {
			$ok="OK";

			if(@$global_db->dbms_check_result($res)) {
				@showRows($global_db,$res,true);
				$global_db->dbms_free_result($res);
			} else {
				$affected_rows= $global_db->dbms_affected_rows();
				html_showInfo("No rows returned.<br>Affected rows: $affected_rows");
			}
		}

		$_SESSION[APP_NAME . "_DBMS_QUERYES"][]= array($ok, $query);
	}

?>
				</div>
			</td>
		</tr>
	</table>

	<div id="" style="overflow:scroll; height:250px">
	<table class='data_box_rows' >
		<tr>
			<td class='sub_form_title' colspan='4'>
				Previous queries
				<input type='button' value='(Clear)' onclick="
					set_value('clear_previous_queries', '1');
					document.forms[0].submit();" />
			</td>
		</tr>
		<tr>
			<th width='10px' class='data_box_rows'></th>
			<th class='data_box_rows'>Id</th>
			<th class='data_box_rows'>Result</th>
			<th class='data_box_rows'>Query</th>
		</tr>
<?php

	$ordered_array= $_SESSION[APP_NAME . "_DBMS_QUERYES"];
	krsort($ordered_array);

	$i=0;
	foreach($ordered_array as $key => $old_query) {
		$i++;
		echo "<tr style='border-left: 1px solid #aaddaa; border-top: 1px solid #aaddaa;'>";
?>
		<td class='data_box_cell' style='border-left: 1px solid #aaddaa; border-top: 1px solid #aaddaa;'>
			<input type='button' value='Exec' onclick="
				set_value('p_query', '<?php echo $old_query[1];?>');
				document.forms[0].submit();
				" />
		</td>
<?php
		echo "
			<td class='data_box_cell' style='border-left: 1px solid #aaddaa; border-top: 1px solid #aaddaa;'><b>$key</b></td>
			<td class='data_box_cell' style='border-left: 1px solid #aaddaa; border-top: 1px solid #aaddaa;'>" . $old_query[0] . "</td>
			<td class='data_box_cell' style='border-left: 1px solid #aaddaa; border-top: 1px solid #aaddaa;'>" . $old_query[1] . "</td></tr>";
	}
?>
	</table>
	</div>
<?php

	html_footer();
