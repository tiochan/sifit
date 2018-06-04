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

	include_once INC_DIR . "/reports/graphs/report_graph.class.php";


	class tag_generic_graph extends tag_element {

		protected $show_connection= false;

		public function get_value() {

			global $USER_ID;

			$this->replace_parameters();

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
			This tag will create a graph built from value.<br>
			The value can be the result of any other kind of tag, like a query, a file dump, etc.<br>
			</p>
			<p>
			Take in consideration that:<br>
			<br>
			 - If parameter COL_NAMES is not set, the first row is used as col names,<br>
			 - The <b>first field will be used as labels</b>,<br>
			 - For each other field, a plot will be shown,<br>
			 - All sequences are shown over the same graph, each sequence with one different color.<br>
			</p>
			<br>
			<p><b>Example #1: Use a query TAG to get data from</b></p>
			<p style='font-family: courier; white-space: show' nowrap>
			<b>-- TAG NAME: EXAMPLE_OF_QUERY</b><br>
			SELECT date_format(process_start,'%Y, Week %u') as 'Week', --> This field will be used as labels<br>
			  (sum(vol) >> 40) as 'TB', --> Generates one bar sequence<br>
			  avg(duration) as 'Duration' --> Generates another bar sequence<br>
			FROM backup_results<br>
			GROUP BY date_format(start,'%Y, Week %u')<br>
			ORDER BY Week;
			</p>
			<p>
			Then use that tag as value:
			{EXAMPLE_OF_TAG}
			</p>
			<br>
			<p><b>Example #2: Specify data manually</b></p>
			<p>In this example the data used to generate the report is a TAG that must return values in the form:
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
						<td class='data_box_cell'><?php echo DEFAULT_GRAHP_HEIGHT; ?></td>
						<td class='data_box_cell'>To set the height of the graph</td>
					</tr>
					<tr>
						<td class='data_box_cell'>WIDTH=value</td>
						<td class='data_box_cell'><?php echo DEFAULT_GRAHP_WIDTH; ?></td>
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
						<td class='data_box_cell'><?php echo DEFAULT_GRAPH_MARGIN_TOP . "," . DEFAULT_GRAPH_MARGIN_BOTTOM . "," . DEFAULT_GRAPH_MARGIN_LEFT . "," . DEFAULT_GRAPH_MARGIN_RIGHT ;?></td>
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
						<td class='data_box_cell'>Set name for each serie.</td>
					</tr>
					<tr>
						<td class='data_box_cell'>THEME= [ Aqua | Green | Ocean | Orange | Pastel | Rose | Softy | Universal | Vivid ]</td>
						<td class='data_box_cell'><?php echo DEFAULT_THEME; ?></td>
						<td class='data_box_cell'>Graph theme.</td>
					</tr>
					<tr>
						<td class='data_box_cell'>GRAPH_TYPE= [ LinePlot | BarPlot ]</td>
						<td class='data_box_cell'>LinePlot</td>
						<td class='data_box_cell'>Graph type.</td>
					</tr>
					<tr>
						<td class='data_box_cell'>GRAPH_LINE_STYLE= [ solid | dotted | dashed ]</td>
						<td class='data_box_cell'>Solid</td>
						<td class='data_box_cell'>Only for line plots: define line style for all line plots.</td>
					</tr>
					<tr>
						<td class='data_box_cell'>ACCUMULATED= [ true | false ]</td>
						<td class='data_box_cell'>false</td>
						<td class='data_box_cell'>For BarPlot, determine if are grouped or not.</td>
					</tr>
					<tr>
						<td class='data_box_cell'>SERIE[num_serie]_TYPE= [ LinePlot | BarPlot ]</td>
						<td class='data_box_cell'>Default set for GRAPH_TYPE</td>
						<td class='data_box_cell'>Graph type for [num_serie] serie.</td>
					</tr>
					<tr>
						<td class='data_box_cell'>SERIE[num_serie]_COLOR= color</td>
						<td class='data_box_cell'>Next different color</td>
						<td class='data_box_cell'>Graph color for [num_serie] serie.</td>
					</tr>
					<tr>
						<td class='data_box_cell'>SERIE[num_serie]_FILL_COLOR= color</td>
						<td class='data_box_cell'>None</td>
						<td class='data_box_cell'>Graph color for [num_serie] serie.</td>
					</tr>
					<tr>
						<td class='data_box_cell'>SERIE[num_serie]_LINE_STYLE= [ solid | dotted | dashed ]</td>
						<td class='data_box_cell'>Solid</td>
						<td class='data_box_cell'>Only for line plots: define line style for [num_serie] serie.</td>
					</tr>
					<tr>
						<td class='data_box_cell'>SERIE[num_serie]_SHOW_VALUES= [ true | false ]</td>
						<td class='data_box_cell'>false</td>
						<td class='data_box_cell'>Determine if show values for [num_serie] serie.</td>
					</tr>
				</table>
			</p>
<?php
		}
	}
?>