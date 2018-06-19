<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sifit
 * @subpackage reports
 *
 * Tags definition class
 *
 */

	include_once INC_DIR . "/reports/tags/tag_element.class.php";

	class tag_search extends tag_element {

		protected $show_connection= false;

		public function get_value() {
			$this->replace_parameters();

			$regexpall= $this->get_parameter("REGEXPALL");
			if($regexpall !== false) {
				$regexpall= "'|" . $regexpall . "|U'";
				$ret= preg_match_all($regexpall, $this->value, $results, PREG_PATTERN_ORDER );

				if(!$ret) return "";

				$return="";
				for($i=1; $i < count($results); $i++) {
					$return.= $results[$i][1] . "\n";
				}
				return $return;
			}

			$regexp= $this->get_parameter("REGEXP");
			if($regexp !== false) {
echo "<h1>REGEXP: " . htmlspecialchars($regexp) . "</h1>";

				$regexp= "'|" . $regexp . "|U'";
				$ret= preg_match($regexp, $this->value, $results, PREG_OFFSET_CAPTURE );
				if(!$ret) return "";

				if(!$ret) return "";

				for($i=1, $return=""; $i < count($results); $i++) {
					$return.= $results[$i][1] . "\n";
				}
				return $return;
			}

			$explode= $this->get_parameter("EXPLODE");
			if($explode !== false) {
				if($explode=="") return $this->value;
				$results= explode($explode, $this->value);
				if(!$results) return "";

				return implode("\n", $results);
			}


			// No operation, then return value itself.
			return $this->value;
		}

		protected function show_help() {
?>
			<p>
			This tag will search into the value for a substring and return the result of search.<br>
			How to search into value can be set by many forms (see extra parameters list).<br>
			</p>
<?php
		}

		protected function show_extra_help() {
?>
			<p>
				<b>Extra parameters</b><br>
				<br>
				You can also add some extra parameters into the <i>'additional information field'</i>, separated by ';'.<br>
				There are some examples of extra parameters:<br>
				<br>
				<table class='data_box_rows'>
					<tr>
						<th class='data_box_rows'>Parameter</th>
						<th class='data_box_rows'>Default</th>
						<th class='data_box_rows'>Description</th>
					</tr>
					<tr>
						<td class='data_box_cell'>REGEXP=&lt;regular expression&gt;</td>
						<td class='data_box_cell'>Example:<br><?php echo htmlspecialchars("REGEXPALL=<[^>]+>(.*)</[^>]+>;"); ?></td>
						<td class='data_box_cell'>Search for the first occurrence using regular a expression.</td>
					</tr>
					<tr>
						<td class='data_box_cell'>REGEXPALL=&lt;regular expression&gt;</td>
						<td class='data_box_cell'></td>
						<td class='data_box_cell'>Search for all occurrence using regular a expression.</td>
					</tr>
					<tr>
						<td class='data_box_cell'>EXPLODE=&lt;delimiter&gt;</td>
						<td class='data_box_cell'>Example:<br>EXPLODE=,</td>
						<td class='data_box_cell'>Explode into different lines the value, returning one line per piece.</td>
					</tr>
				</table>
			</p>
<?php
		}
	}
?>
