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


	class tag_generic_graph extends tag_element {

		protected $show_connection= false;

		public function get_value() {

			global $USER_ID;

			parent::get_value();

			$grapher= INC_DIR . "/reports/graphs/generic_graph.php";
			if(!file_exists($grapher)) return "** Error: Can't locate generic grapher **";

			include_once $grapher;

			$gr= new generic_graph($this->value, $this->parameters, $this->db);
			return $gr->getImageTag();
		}

/*		protected function change_field_properties(&$field) {
			$field->reference= new image_tag();
		}
*/
		protected function show_help() {
?>
			<p>
			This tag will create a bar graph, built from value.<br>
			The value can be the result of any other kind of tag, like a query, a file dump, etc.<br>
			</p>
			<p>
			Take in consideration that:<br>
			<br>
			 - The <b>first row is used as col names</b>,<br>
			 - The <b>first field will be used as labels</b>,<br>
			 - For each other field, a bar will be shown,<br>
			 - All sequences are shown over the same graph, each sequence with one different color.<br>
			</p>
			<p>Example:</p>
			<p style='font-family: courier; white-space: show' nowrap>
			SELECT date_format(process_start,'%Y, Week %u') as 'Week', --> This field will be used as labels<br>
			  (sum(vol) >> 40) as 'TB', --> Generates one bar sequence<br>
			  avg(duration) as 'Duration' --> Generates another bar sequence<br>
			FROM backup_results<br>
			GROUP BY date_format(start,'%Y, Week %u')<br>
			ORDER BY Week;
			</p>
			<p>Example:</p>
			<p>In this example the data used to generate the report is a TAG that must return values in the form (example):
			1,200,1500\n
			2,250,2400\n
			3,800,3600\n
			</p>
			<p><b>Series</b></p>
			<p>You can set series using the special string "&lt;GRAPH_SERIES&gt;" and the values for each serie.<br>
			For example:
			<p style='font-family: courier; white-space: show' nowrap>
			&lt;GRAPH_SERIES&gt;{GET_CURRENT_MONTH_DATA}<br>
			&lt;GRAPH_SERIES&gt;{GET_CURRENT_YEAR_DATA}
			</p>
			This will creat a combined graph, where for each serie will create a plot.
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
						<td class='data_box_cell'>HEIGHT=value</td>
						<td class='data_box_cell'>200</td>
						<td class='data_box_cell'>To set the height of the graph</td>
					</tr>
					<tr>
						<td class='data_box_cell'>WIDTH=value</td>
						<td class='data_box_cell'>200</td>
						<td class='data_box_cell'>To set the width of the graph</td>
					</tr>
					<tr>
						<td class='data_box_cell'>SHOW_VALUES=[true|false]</td>
						<td class='data_box_cell'>true</td>
						<td class='data_box_cell'>If you want to show or not the values
					</tr>
					<tr>
						<td class='data_box_cell'>SHOW_LEGEND=[true|false]</td>
						<td class='data_box_cell'>true</td>
						<td class='data_box_cell'>To set the width of the graph</td>
					</tr>
					<tr>
						<td class='data_box_cell'>ROTATE_LABELS=[true|false]</td>
						<td class='data_box_cell'>false</td>
						<td class='data_box_cell'>If you need to rotate the labels</td>
					</tr>
					<tr>
						<td class='data_box_cell'>TITLE=value</td>
						<td class='data_box_cell'></td>
						<td class='data_box_cell'>To set the title of the graph</td>
					</tr>
					<tr>
						<td class='data_box_cell'>SUBTITLE=value</td>
						<td class='data_box_cell'></td>
						<td class='data_box_cell'>To set the subtitle of the graph</td>
					</tr>
					<tr>
						<td class='data_box_cell'>XTITLE=value</td>
						<td class='data_box_cell'></td>
						<td class='data_box_cell'>To set the title of the X axis</td>
					</tr>
					<tr>
						<td class='data_box_cell'>YTITLE=value</td>
						<td class='data_box_cell'></td>
						<td class='data_box_cell'>To set the title of the Y axis</td>
					</tr>
					<tr>
						<td class='data_box_cell'>[TOP,BOTTOM,LEFT,RIGHT]=value</td>
						<td class='data_box_cell'>60,120,30,30</td>
						<td class='data_box_cell'>To set the margins of the graph</td>
					</tr>
					<tr>
						<td class='data_box_cell'>VERTICAL=[true|false]</td>
						<td class='data_box_cell'>false</td>
						<td class='data_box_cell'>To create the in vertical mode.</td>
					</tr>
					<tr>
						<td class='data_box_cell'>LABEL_INTERVAL=value</td>
						<td class='data_box_cell'>0</td>
						<td class='data_box_cell'>Set the label interval.</td>
					</tr>
					<tr>
						<td class='data_box_cell'>COL_NAMES=&lt;col_1_name&gt;,&lt;col_2_name&gt;,...</td>
						<td class='data_box_cell'>0</td>
						<td class='data_box_cell'>Set the label interval.</td>
					</tr>
				</table>
			</p>
<?php
		}
	}
?>