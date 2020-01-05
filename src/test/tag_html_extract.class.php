<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage reports
 *
 * Tags definition class
 *
 */

	include_once INC_DIR . "/reports/tags/tag_element.class.php";

	class tag_html_extract extends tag_element {

		protected $show_connection= false;

		public function get_value() {

			$this->replace_parameters();

			// Overrides URL
			$url= $this->get_parameter("URL");

			if($url != false) {
				if( (stripos($url, "http://")!==0) and (strpos($url, "https://")!==0) ) $url= "http://" . $url;
				$this->value= file_get_contents($url);
			}

			$search= $this->get_parameter("SEARCH");

			$object_type= $this->get_parameter("OBJECT_TYPE");
			if(!$object_type) $object_type = "div";

			$object_id= $this->get_parameter("OBJECT_ID");
			$object_class= $this->get_parameter("OBJECT_CLASS");
			$regexp_user= $this->get_parameter("REGEXP");

			$to_search= "";

			if($object_id !== false) {
				$to_search="id=\"$object_id\"";
			} elseif ($object_class !== false) {
				$to_search="class=\"$object_class\"";
			}

			if($to_search != "") {

				$return_value="";

				$regexp_styles= '/<link.*?type=\"text\/css\"[^`]*?href=\"([^`]*?)\" \/>/';
				/*
				 * $regexp_divs= '/<div.*?id=\"' . $object_id . '\".*?>([^`]*?)<\/div>/';
				 */

				/**
				 * This useful regexp extrated from a post of Bart Kiers at:
				 * http://stackoverflow.com/questions/1721223/php-regexp-for-nested-div-tags
				 *
				 * Thanks a lot for people sharing knowledges!
				 *
				 * #<div\s+id="t\d+">[^<>]*(<div[^>]*>(?:[^<>]*|(?1))*</div>)[^<>]*</div>#si
				 * #<div class="meteoBox"[^>]*>[^<>]*?(<div[^>]*>(?:[^<>]*|(?1))*</div>)?[^<>]*</div>#sim
				 *
				 * <div\s+id="t\d+">  # match an opening 'div' with an id that starts with 't' and some digits
					[^<>]*             # match zero or more chars other than '<' and '>'
					(                  # open group 1
					  <div[^>]*>       #   match an opening 'div'
					  (?:              #   open a non-matching group
					    [^<>]*         #     match zero or more chars other than '<' and '>'
					    |              #     OR
					    (?1)           #     recursively match what is defined by group 1
					  )*               #   close the non-matching group and repeat it zero or more times
					  </div>           #   match a closing 'div'
					)                  # close group 1
					[^<>]*             # match zero or more chars other than '<' and '>'
					</div>             # match a closing 'div'
				 */
				$regexp_objects= '#<' . $object_type . '[^>]*' . $to_search . '[^>]*>[^<>]*?(<' . $object_type . '[^>]*>(?:[^<>]*|(?1))*</' . $object_type . '>)?[^<>]*</' . $object_type . '>#sim';
$regexp_objects= '/<' . $object_type . '.*?' . $to_search . '.*?>([^`]*?)<\/' . $object_type . '>/';

$recursivity='[^<>]*?(<' . $object_type . '[^>]*>(?:[^<>]*|(?1))*</' . $object_type . '>)?[^<>]*';
$regexp_objects= '#<' . $object_type . '.*?' . $to_search . '.*?>' . $recursivity . '</' . $object_type . '>#';

echo "<h1>REGEXP</h1>";
echo htmlspecialchars($regexp_objects);
echo "<hr>";

//NO SE PUEDEN USAR EXPRESIONES REGULARES RECURSIVAS PARA ESTAS COSAS.
//HAY QUE USAR XPATH O ALGO PARECIDO:
//http://stackoverflow.com/questions/1721223/php-regexp-for-nested-div-tags

				if(preg_match_all($regexp_styles, $this->value, $results_styles, PREG_PATTERN_ORDER)) {
					foreach($results_styles[0] as $style) {
						$return_value.= "\n" . $style;
					}
				}

				if(preg_match_all($regexp_objects, $this->value, $result_objects)) {
					foreach($result_objects[0] as $object) {
						if( ($search !== false) and (stripos($object, $search) === false) ) $object="";
						$return_value.= "\n" . $object;
					}
				}

				return $return_value;
			} elseif ($regexp_user !== false) {

				$return_value="";

				$regexp_styles= '/<link.*?type=\"text\/css\"[^`]*?href=\"([^`]*?)\" \/>/';

				$ret_styles= preg_match_all($regexp_styles, $this->value, $results_styles, PREG_PATTERN_ORDER);
				$ret_divs= preg_match_all($regexp_user, $this->value, $results_divs, PREG_PATTERN_ORDER);

				if($ret_styles) {
					foreach($results_styles[0] as $style) {
						$return_value.= "\n" . $style;
					}
				}

				if($ret_divs) {
					foreach($results_divs[0] as $div) {
						if( ($search !== false) and (stripos($div, $search) === false) ) $div="";
						$return_value.= "\n" . $div;
					}
				}

				return $return_value;
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
						<td class='data_box_cell'>OBJECT_TYPE=&lt;Object type&gt;</td>
						<td class='data_box_cell'>div</td>
						<td class='data_box_cell'>What type of HTML tag must be searched (div, li, table, ...).</td>
					</tr>
					<tr>
						<td class='data_box_cell'>OBJECT_ID=&lt;OBJECT_TYPE tag Id&gt;</td>
						<td class='data_box_cell'></td>
						<td class='data_box_cell'>Search for all occurrences of OBJECT_TYPE enclosed blocks identified by Id.</td>
					</tr>
					<tr>
						<td class='data_box_cell'>OBJECT_CLASS=&lt;OBJECT_TYPE tag Class&gt;</td>
						<td class='data_box_cell'></td>
						<td class='data_box_cell'>Search for all occurrences of OBJECT_TYPE enclosed blocks of class.</td>
					</tr>
					<tr>
						<td class='data_box_cell'>SEARCH=&lt;text to search into&gt;</td>
						<td class='data_box_cell'></td>
						<td class='data_box_cell'>If this parameter is set, for each occurrence of this object type, search this string into it to pass the filter.</td>
					</tr>
					<tr>
						<td class='data_box_cell'>URL=&lt;URL to page&gt;</td>
						<td class='data_box_cell'></td>
						<td class='data_box_cell'>Use the content of this page instead the content of the "value" field.</td>
					</tr>
					<tr>
						<td class='data_box_cell'>REGEXP=&lt;regular expression&gt;</td>
						<td class='data_box_cell'></td>
						<td class='data_box_cell'>This is an advandec parameter.<br>
						You can specify the regular expression to use for search HTML parts.<br>
						<a target='_blank' href='http://es1.php.net/manual/en/pcre.pattern.php'>See how to create your regular expressions (for PHP).</a><br>
						<br>
						<i>Example:</i><br>
						REGEXP= '/&lt;div.*?id=\"' . $object_id . '\"&gt;([^`]*?)&lt;\/div&gt;/';
						</td>
					</tr>
				</table>
			</p>
<?php
		}
	}
?>