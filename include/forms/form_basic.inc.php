<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage forms
 *
 */

	include_once SYSHOME . "/conf/forms.conf.php";


	function showRows($db, & $res, $tabular = true, $num_rows_first=false) {
		global $MESSAGES;

		$numRows = $db->dbms_num_rows($res);
		$msg = sprintf($MESSAGES["NUM_ROWS"], $numRows);

		if($num_rows_first) html_showInfo($msg);
		echo convertResultToString($db,$res, $tabular);
		if(!$num_rows_first) html_showInfo($msg);


	}

	function showArray(& $res, $tabular = true) {
		global $MESSAGES;

		$numRows = count($res);

		echo convertArrayResultToString($res, $tabular);

		$msg = sprintf($MESSAGES["NUM_ROWS"], $numRows);
		html_showInfo($msg);
	}

	function formModify($db, & $res, $page_new, $page_modify, $page_delete) {

		$colors = Array ();
		$colors[true] = "#F2F2F2";
		$colors[false] = "#FFFFFF";

		if ($page_new != "") {
	?>
			<form name='f_new' method='post' action='<?php echo $page_new ?>'>
				<input type='hidden' name='row_id' value='-1'>
			</form>
	<?php


			$b_new = true;
		} else {
			$b_new = false;
		}

		if ($page_modify != "") {
	?>
			<form name='f_modify' method='post' action='<?php echo $page_modify ?>'>
				<input type='hidden' name='row_id' value='-1'>
			</form>
	<?php


			$b_modify = true;
		} else {
			$b_modify = false;
		}

		if ($page_delete != "") {
	?>
			<form name='f_delete' method='post' action='<?php echo $page_delete ?>'>
				<input type='hidden' name='row_id' value='-1'>
			</form>
	<?php


			$b_delete = true;
		} else {
			$b_delete = false;
		}

		if ($b_new)
			echo "<input type='button' value='nou' onclick='document.forms.f_new.row_id.value=1;document.forms.f_new.submit()'>";

		$numRows = $db->dbms_num_rows($res);

		if (!$numRows) {
			echo "<font color=gray><i>No data.<b></b></i></font>";
			return;
		}

		$db->dbms_data_seek($res, 0);
	?>
			<table border=0 cellspacing=1 cellpadding=1 bgcolor=#aaaaaa>
				<tr><td>
					<table border=0 cellspacing=1 cellpadding=1 bgcolor='white'>
			<?php


		// Cabeceras de los campos
		$num_fields = $db->dbms_num_fields($res);
		for ($i = 1; $i < $num_fields; $i ++) {
			echo "<th>".$db->dbms_field_name($res, $i)."</th><th>&nbsp;</th>";
		}

		if ($b_modify)
			echo "<th>edt</th>";

		if ($b_delete)
			echo "<th>del</th>";

		$filaPar = false;
		while ($row = $db->dbms_fetch_array($res)) {
			echo "<tr bgcolor='".$colors[$filaPar]."'>\n";
			$id = $row[0];
			for ($i = 1; $i < $num_fields; $i ++) {
				echo "<td>".$row[$i]."</td><td>&nbsp;</td>\n";
			}
			if ($b_modify)
				echo "<td><input type='button' value='*' onclick='document.forms.f_modify.row_id.value=$id;document.forms.f_modify.submit()'></td>\n";
			if ($b_delete)
				echo "<td><input type='button' value='-' onclick='if(confirm(\"Eliminar el registre?\")) { document.forms.f_delete.row_id.value=$id;document.forms.f_delete.submit()}'></td>\n";
			echo "</tr>\n";
			$filaPar = !$filaPar;
		}
	?>
					</table>
				</td></tr>
			</table>
			<font color=gray><i>Total: <b><?php echo $numRows ?></b> registres.</i></font>
			<?php
	}

	function exportData($db, & $res, $filename) {

		global $MESSAGES;

		$colors = Array ();
		$colors[true] = "#F2F2F2";
		$colors[false] = "#FFFFFF";

		$numRows = $db->dbms_num_rows($res);

		if (!$numRows) {
			html_showInfo($MESSAGES["NO_ROWS"]);
			return;
		}

		$fd = fopen($filename, "w");
		if (!$fd)
			return 0;

		$db->dbms_data_seek($res, 0);

		fwrite($fd, "
						<table border=0 cellspacing=0 cellpadding=0 bgcolor=gray>
						<tr><td>
							<table border=0 cellspacing=1 cellpadding=1>
						");

		// Cabeceras de los campos
		$num_fields = $db->dbms_num_fields($res);
		for ($i = 0; $i < $num_fields; $i ++) {
			fwrite($fd, "<th bgcolor=white>".$db->dbms_field_name($res, $i)."</th>\n");
		}

		$filaPar = false;
		while ($row = $db->dbms_fetch_array($res)) {
			fwrite($fd, "<tr bgcolor='".$colors[$filaPar]."'>");
			for ($i = 0; $i < $num_fields; $i ++)
				fwrite($fd, "<td>".$row[$i]."</td>\n");
			fwrite($fd, "</tr>\n");
			$filaPar = !$filaPar;
		}
		fwrite($fd, "
								</table>
							</td></tr>
						</table>
						");

		fclose($fd);
		return 1;
	}

	function exportDataCSV($db, & $res, $filename) {

		global $MESSAGES;

		$numRows = $db->dbms_num_rows($res);

		if (!$numRows) {
			html_showInfo($MESSAGES["NO_ROWS"]);
			return 0;
		}

		if(!($fd = fopen($filename, "w"))) return 0;

		$db->dbms_data_seek($res, 0);

		// Cabeceras de los campos
		$num_fields = $db->dbms_num_fields($res);
		$separator="";
		for ($i = 0; $i < $num_fields; $i ++) {
			fwrite($fd, $separator . $db->dbms_field_name($res, $i));
			$separator= CSV_SEPARATOR;
		}
		fwrite($fd, "\n");

		while ($row = $db->dbms_fetch_array($res)) {
			$separator="";
			for ($i = 0; $i < $num_fields; $i ++) {
				fwrite($fd, $separator . $row[$i]);
				$separator= CSV_SEPARATOR;
			}
			fwrite($fd, "\n");
		}

		fclose($fd);
		return 1;
	}

	function exportArrayCSV(& $res, $filename) {

		global $MESSAGES;

		$numRows = count($res);

		if (!$numRows) {
			html_showInfo($MESSAGES["NO_ROWS"]);
			return 0;
		}

		if(!($fd = fopen($filename, "w"))) return 0;

		// Cabeceras de los campos
		$keys= @array_keys($res[0]);
		$num_fields = count($keys);
		$separator="";
		for ($i = 0; $i < $num_fields; $i ++) {
			fwrite($fd, $separator . $keys[$i]);
			$separator=CSV_SEPARATOR;
		}
		fwrite($fd, "\n");

		foreach($res as $row) {
			$separator="";
			foreach($row as $key => $value) {
				fwrite($fd, $separator . $value);
				$separator=CSV_SEPARATOR;
			}
			fwrite($fd, "\n");
		}

		fclose($fd);
		return 1;
	}

	function exportDataXML($db, & $res, $filename) {

		if(!($fd = fopen($filename, "w"))) return 0;

		fwrite($fd,"<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n");
		fwrite($fd,"<data>\n");
		$db->dbms_data_seek($res, 0);

		$filaPar = false;
		while ($row = $db->dbms_fetch_array($res)) {
			fwrite($fd,"<row>\n");
			$i=false;
			foreach($row as $key => $value) {
				if($i) {
					fwrite($fd,"<$key><text>$value</text></$key>\n");
				}
				$i= !$i;
			}
			fwrite($fd,"</row>\n");
		}

		fwrite($fd,"</data>\n");
		fclose($fd);
		return 1;
	}

	function convertResultToCSV($db, & $res, $show_field_names=true) {

		global $MESSAGES;
		global $GLOBAL_HEADERS;

		$numRows = $db->dbms_num_rows($res);
		$num_fields = $db->dbms_num_fields($res);

		if (!$numRows) {
			return "no rows";
		}

		$db->dbms_data_seek($res, 0);

		$result="";

		// Field headers

		if($show_field_names) {
			for ($i = 0, $pre=""; $i < $num_fields; $i ++) {
				$result.= $pre . $db->dbms_field_name($res, $i);
				$pre=",";
			}
			$result.="\r\n";
		}

		$filaPar = false;
		while ($row = $db->dbms_fetch_array($res)) {

			for ($i = 0, $pre=""; $i < $num_fields; $i ++) {
				$result.= $pre . $row[$i];
				$pre=",";
			}
			$result.= "\r\n";
		}

		return $result;
	}

	function convertResultToString($db, & $res, $tabular = true) {

		global $MESSAGES;
		global $GLOBAL_HEADERS;

		$GLOBAL_HEADERS["form_elements"]="<link rel='stylesheet' type='text/css' href='" . HOME . "/include/styles/form_elements.css'>";

		$colors = Array ();
		$colors[true] = "data_box_rows_tabular_even";
		$colors[false] = "data_box_rows_tabular_odd";


		$numRows = @$db->dbms_num_rows($res);
		$num_fields = @$db->dbms_num_fields($res);

		if (!$numRows) {
			html_showInfo($MESSAGES["NO_ROWS"]);
			return;
		}

		$db->dbms_data_seek($res, 0);

		$result="
				<table class='data_box_external' cellpadding='2' align=left>
					<tr>
						<td>
							<table class='data_box_rows' cellspacing='0' cellpadding='10'>\n";

		if($tabular) {

			// Field headers
			for ($i = 0; $i < $num_fields; $i ++) {
				$result.= "<th class='data_box_rows'>".$db->dbms_field_name($res, $i)."</th>\n";
			}

			$filaPar = false;
			while ($row = $db->dbms_fetch_array($res)) {
				$result.= "<tr class='".$colors[$filaPar]."'>";
				for ($i = 0; $i < $num_fields; $i ++)
					$result.= "<td class='data_box_cell'>".$row[$i]."</td>\n";
				$result.= "</tr>\n";
				$filaPar = !$filaPar;
			}

		} else {

			$cont = 1;
			while ($row = $db->dbms_fetch_array($res)) {
				$result.="<tr class='".$colors[true]."'><td colspan='$num_fields' class='data_box_cell'><font size=1><i><b>#".$cont ++."</b></i></font></td></tr>";
				for ($i = 0; $i < $num_fields; $i ++) {
					$result.="<tr class='".$colors[false]."'><td class='data_box_cell'>".$db->dbms_field_name($res, $i)."</td><td class='data_box_cell'><font color='#777777'>".$row[$i]."</font></td></tr>";
				}
			}
		}

		$result.= "
							</table>
						</td>
					</tr>
				</table>\n";

		return $result;
	}

	function convertArrayResultToString(& $res, $tabular = true) {

		global $MESSAGES;
		global $GLOBAL_HEADERS;

		$GLOBAL_HEADERS["form_elements"]="<link rel='stylesheet' type='text/css' href='" . HOME . "/include/styles/form_elements.css'>";


		$colors = Array ();
		$colors[true] = "data_box_rows_tabular_even";
		$colors[false] = "data_box_rows_tabular_odd";

		$numRows = count($res);

		if (!$numRows) {
			html_showInfo($MESSAGES["NO_ROWS"]);
			return;
		}

		$field_names= array_keys($res[0]);
		$num_fields= count($field_names);

		$result="
				<table class='data_box_external' cellpadding='2' align=left>
					<tr><td>
						<table class='data_box_rows' cellspacing='0' cellpadding='10'>\n";

		if($tabular) {

			// Field headers
			for ($i = 0; $i < $num_fields; $i ++) {
				$result.= "<th class='data_box_rows'>".$field_names[$i]."</th>\n";
			}

			$filaPar = false;
			foreach($res as $row) {
				$result.= "<tr class='".$colors[$filaPar]."'>";

				foreach($row as $key => $value)
					$result.= "<td class='data_box_cell'>".$value."</td>\n";
				$result.= "</tr>\n";
				$filaPar = !$filaPar;
			}

		} else {

			$cont = 1;
			foreach($res as $row) {
				$result.="<tr class='".$colors[true]."'><td colspan='$num_fields' class='data_box_cell'><font size=1><i><b>#".$cont ++."</b></i></font></td></tr>";
				foreach($row as $key => $value) {
					$result.="<tr class='".$colors[false]."'><td class='data_box_cell'>".$key."</td><td class='data_box_cell'><font color='#777777'>".$value."</font></td></tr>";
				}
			}
		}

		$result.= "
					</table>
				</td></tr>
			</table>\n";

		return $result;
	}
?>
