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

	class tag_html_extract extends tag_element {

		protected $show_connection= false;

		public function get_value() {

			$this->replace_parameters();

			// Overrides URL
			$url= $this->get_parameter("URL");
echo "URL: $url";
			if($url != false) {
				if( (stripos($url, "http://")!==0) and (strpos($url, "https://")!==0) ) $url= "http://" . $url;

				$opts = array(
					'http' => array(
						'method'=>"GET",
						'header'=>"Content-Type: text/html; charset=utf-8"
					)
				);

				$context = stream_context_create($opts);
				$this->value= file_get_contents($url,false,$context);
echo "Value: " . $this->value;
				if($this->value=="") return "";

				echo "hola";
				$this->DOM= new DOMDocument();
				if(!@$this->DOM->loadHTML($this->value)) {
					// TO-DO
				}
			}

			$search= $this->get_parameter("SEARCH");
			$search= $search != false ? $search : "";

			$object_path= $this->get_parameter("OBJECT_PATH");

			$object_type= $this->get_parameter("OBJECT_TYPE");
			if(!$object_type) $object_type = "div";

			$object_id= $this->get_parameter("OBJECT_ID");
			$object_class= $this->get_parameter("OBJECT_CLASS");

			$regexp_user= $this->get_parameter("REGEXP");

			$get_styles= $this->get_parameter("INCLUDE_STYLES");
			$get_styles= ( ($get_styles == 1) or ($get_styles === false) );
			$get_scripts= $this->get_parameter("INCLUDE_SCRIPTS");
			$get_scripts= ( ($get_scripts == 1) or ($get_scripts === false) );

			$object_attribute= ($object_id !== false) ? "id" : ( ($object_class !== false) ? "class" : "" );
			$object_value= ($object_id !== false) ? $object_id : ( ($object_class !== false) ? $object_class : "" );

			if($object_path != false) {

				$return_value="";

				if($get_styles) {
					$return_value.=DOMgetTags($this->DOM, "link", "type", "text/css") . "\n";

				}
				if($get_scripts) {
					$return_value.= DOMgetTags($this->DOM, "script", "type", "text/javascript") . "\n";
				}
				$return_value.= DOMgetPath($this->DOM, $object_path, $search);

				$return_value= str_ireplace("=\"/", "=\"" . $url, $return_value);
				return $return_value;

			} elseif ($object_attribute != "") {

				$return_value="";

				if($get_styles) {
					$return_value.= DOMgetTags($this->DOM, "link", "type", "text/css") . "\n";
				}
				if($get_scripts) {
					$return_value.= DOMgetTags($this->DOM, "script", "type", "text/javascript") . "\n";
				}
				$return_value.= DOMgetTags($this->DOM, $object_type, $object_attribute, $object_value, $search);

				$return_value= str_ireplace("=\"/", "=\"" . $url, $return_value);
				return $return_value;

			} elseif ($regexp_user !== false) {

				$return_value="";

				$ret_divs= preg_match_all($regexp_user, $this->value, $results_divs, PREG_PATTERN_ORDER);

				if($ret_divs) {

					if($get_styles) {
						$css= DOMgetTags($this->DOM, "link", "type", "text/css") . "\n";
						$css= str_ireplace("=\"/", "=\"" . $url, $css);

					}
					if($get_scripts) {
						$scripts= DOMgetTags($this->DOM, "script", "type", "text/javascript") . "\n";
						$scripts= str_ireplace("=\"/", "=\"" . $url, $scripts);
					}

					$return_value.=$css . "\n" . $scripts;

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
						<td class='data_box_cell'>SEARCH=&lt;search text 1 [| search text 2 [ | search text ... N&gt;</td>
						<td class='data_box_cell'></td>
						<td class='data_box_cell'>If this parameter is set, for each occurrence of this object type, search this string into it to pass the filter.<br>
						You can define more than one text to search, separated by '|'
						</td>
					</tr>
					<tr>
						<td class='data_box_cell'>URL=&lt;URL to page&gt;</td>
						<td class='data_box_cell'></td>
						<td class='data_box_cell'>Use the content of this page instead the content of the "value" field.</td>
					</tr>
					<tr>
						<td class='data_box_cell' colspan=3>Advanced</td>
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
					<tr>
						<td class='data_box_cell'>OBJECT_PATH=&lt;Xpath path expression&gt;</td>
						<td class='data_box_cell'></td>
						<td class='data_box_cell'>Use XPath expression to get the piece(s).<br>
						<a href="http://www.w3schools.com/xpath/xpath_syntax.asp">See this page for more info</a><br>
						</td>
					</tr>
					<tr>
						<td class='data_box_cell'>INCLUDE_STYLES=[ 1 | 0 ]</td>
						<td class='data_box_cell'>1</td>
						<td class='data_box_cell'>Determine if styles of remote page must be shown (1) or not (0).</td>
					</tr>
					<tr>
						<td class='data_box_cell'>INCLUDE_SCRIPTS=[ 1 | 0 ]</td>
						<td class='data_box_cell'>1</td>
						<td class='data_box_cell'>Determine if scripts of remote page must be shown (1) or not (0).</td>
					</tr>
				</table>
			</p>
<?php
		}
	}
?>