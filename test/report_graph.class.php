<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage reports
 *
 * Alerts evolution on the last year (12 months).
 * Echo a png string, creating a graph that indicates how many alerts
 * have appeared on each month.
 */

	include_once INC_DIR . "/html.inc.php";

	if(file_exists(MY_INC_DIR . "/classes/user.class.php")) {
		include_once MY_INC_DIR . "/classes/user.class.php";
	} else {
		include_once INC_DIR . "/classes/user.class.php";
	}

	include_once INC_DIR . "/jpgraph/src/jpgraph.php";
	include_once INC_DIR . "/jpgraph/src/jpgraph_line.php";
	include_once INC_DIR . "/jpgraph/src/jpgraph_bar.php";
	include_once INC_DIR . "/jpgraph/src/jpgraph_pie.php";
	include_once INC_DIR . "/jpgraph/src/jpgraph_pie3d.php";
	include_once INC_DIR . "/jpgraph/src/jpgraph_canvas.php";



	// Global GRAPH SETTINGS
	define("DEFAULT_GRAHP_WIDTH", 800);
	define("DEFAULT_GRAHP_HEIGHT", 600);
	define("DEFAULT_GRAPH_MARGIN_TOP", 60);
	define("DEFAULT_GRAPH_MARGIN_BOTTOM", 120);
	define("DEFAULT_GRAPH_MARGIN_LEFT", 30);
	define("DEFAULT_GRAPH_MARGIN_RIGHT", 30);
	define("DEFAULT_GRAPH_LINE_STYLE", 'dotted');


	// Theme
	define("DEFAULT_THEME","SoftyTheme");

	// MARKS
	define("DEFAULT_MARK_TYPE", MARK_CIRCLE);
	define("DEFAULT_MARK_SIZE",3);

	// LEGEND
	define("DEFAULT_LEGEND_POS_X", 0.02);
	define("DEFAULT_LEGEND_POS_Y", 0.01);
	define("DEFAULT_LEGEND_FRAME_WEIGHT", 1);
	define("DEFAULT_LEGEND_LINE_WEIGHT", 2);
	define("DEFAULT_LEGEND_LEFT_MARGIN", 5);
	define("DEFAULT_LEGEND_LINE_SPACING", 10);



	global $jpgraph_colors;
	//$jpgraph_colors= array("#0000CD","#9370DB","#B0C4DE","#8B008B","#DA70D6","darkred","green","maroon");
	// Colors: see http://jpgraph.net/download/manuals/chunkhtml/apd.html
	$jpgraph_colors= array(
		'AntiqueWhite4','aquamarine4','azure4','bisque4',
		'blueviolet','brown','cadetblue','chartreuse4',
		'chocolate','gold3','black','indigodye'
	);

	abstract class report_graph {

		protected $extrainfo;
		protected $user;
		protected $parameters;

		protected $double=false;
		protected $accumulated=false;

		protected $graph;
		protected $graph_type="LinePlot";
		protected $db;

		// Global graph properties
		protected $width=DEFAULT_GRAHP_WIDTH;
		protected $height=DEFAULT_GRAHP_HEIGHT;
		protected $show_values= false;
		protected $rotate_labels=false;
		protected $show_legend=true;
		protected $title="";
		protected $subtitle="";
		protected $xtitle="";
		protected $ytitle="";
		protected $forced_width=false;
		protected $forced_height=false;
		protected $label_interval=0;

		// Orientation
		protected $vertical=false;

		protected $labels;
		protected $values;
		protected $plot_names;

		// Default Margin
		protected $forced_top = 0;
		protected $forced_bottom = 0;
		protected $forced_left = 0;
		protected $forced_right = 0;

		protected $top = DEFAULT_GRAPH_MARGIN_TOP;
		protected $bottom = DEFAULT_GRAPH_MARGIN_BOTTOM;
		protected $left = DEFAULT_GRAPH_MARGIN_LEFT;
		protected $right = DEFAULT_GRAPH_MARGIN_RIGHT;

		public $theme=DEFAULT_THEME;


		public function __construct($p_user_id=false, $parameters="", &$db=null) {

			global $USER_ID, $global_db;

			if($p_user_id === false) {
				if(!isset($USER_ID)) die("User id not defined");
				$this->user= new user($USER_ID);
			} else {
				$this->user= new user($p_user_id);
			}

			$this->db= ($db == null) ? $global_db : $db;

			//$this->extrainfo= $extrainfo;
			$this->parameters= $parameters ? $parameters : array();
			$this->parse_parameters();
		}

		/**
		 * This function must set the correct values on:
		 *  $labels -> array of strings/numbers ...
		 *  $values -> array of arrays of values. Each array will be displayed as a new plot into the same graph.
		 *  $plot_names -> For the legend.
		 *
		 * @param unknown_type $filename
		 */
		protected function get_data(&$labels, &$values, &$plot_names,$filename="") {}

		/**
		 * This function must be set on inherited classes in order to call the
		 * method create_graph with the correct graph_type.
		 *
		 * @param string $filename
		 */
		abstract public function render($filename="");
		/**$this
		 * Example:
		 *
		 * public function render($filename="") {
		 *
		 *	 $this->width= $this->forced_width ? $this->width : 600;
		 *	 $this->height= $this->forced_height ? $this->height : 300;
		 *
		 *	 $this->left= 40;
		 *	 $this->right= 10;
		 *	 $this->top= 10;
		 *	 $this->bottom= 40;
		 *
		 *	 $this->create_graph("LinePlot");
		 *	 $this->graph->Stroke($filename);
		 *	 unset($this->graph);
		 * }
		 */

		protected function get_overriden_values() {
			// See for values that are overriden:
			if($this->forced_top) $this->top= $this->forced_top;
			if($this->forced_bottom) $this->bottom= $this->forced_bottom;
			if($this->forced_left) $this->left= $this->forced_left;
			if($this->forced_right) $this->right= $this->forced_right;

			if($this->forced_width) $this->width= $this->forced_width;
			if($this->forced_height) $this->height= $this->forced_height;

			if($this->vertical and !$this->forced_height) {
				$this->height= count($this->values[1]) * 50;
			}
		}

		protected function create_graph($graph_type, $filename="") {

			global $MESSAGES,$jpgraph_colors;


			$data_res=$this->get_data($this->labels, $this->values, $this->plot_names, $filename);

			if(!$data_res) {
				$this->labels=array();
				$this->values=array();
				$this->plot_names=array();
				$this->showMessage("No data",$filename);
				return 0;
			}

			// TODO: ?? NO SENSE FOR GENERIC GRAPHS OR AUTO-SERIES...
			if($this->double) {
				$this->values[3]= array();

				for($i=1; $i<= count($this->values[1]); $i++) {
					$this->values[3][]=0;
				}
			}


			$this->get_overriden_values();

			///////////////////////////////////////////////////////////////////////////
			// Create graph
			$this->graph= new Graph($this->width, $this->height);
			$this->graph->SetMarginColor("#ffffff");
			$this->graph->SetScale("textlin");

			if(!file_exists(INC_DIR . "/jpgraph/src/themes/" . $this->theme . ".class.php")) {
				echo "<font color='red'>** Warning: theme $this->theme not found. Using default **</font>";
				$this->theme= DEFAULT_THEME;
			}

			$theme= $this->theme;
			$theme_class= new $theme();
			$this->graph->SetTheme($theme_class);

			$this->graph->img->SetAntiAliasing(false);

			if($this->double) {
				$this->graph->SetY2Scale("lin");
				$this->graph->yaxis->scale->SetGrace(10);
				$this->graph->y2axis->scale->SetGrace(10);
			}

			if($this->vertical) {
				$this->graph->Set90AndMargin($this->left,$this->right,$this->top,$this->bottom);
			} else {
				$this->graph->img->SetMargin($this->left,$this->right,$this->top,$this->bottom);
			}

			if($this->show_legend) {
//				$this->graph->legend->SetFillColor("white");
//				$this->graph->legend->SetShadow(true,1);
				$this->graph->legend->SetFrameWeight(DEFAULT_LEGEND_FRAME_WEIGHT);
				$this->graph->legend->SetLineWeight(DEFAULT_LEGEND_LINE_WEIGHT);
				$this->graph->legend->SetLayout(LEGEND_VERT);
				$this->graph->legend->Pos(DEFAULT_LEGEND_POS_X,DEFAULT_LEGEND_POS_Y);
				$this->graph->legend->SetLeftMargin(DEFAULT_LEGEND_LEFT_MARGIN);
				$this->graph->legend->SetLineSpacing(DEFAULT_LEGEND_LINE_SPACING);
			}

			$this->graph->SetFrameBevel(0,false);

			if($this->title != "") {
				$this->graph->title->Set($this->title);
				$this->graph->title->SetFont(FF_FONT2,FS_BOLD,12);
			}
			if($this->subtitle != "") {
				$this->graph->subtitle->Set($this->subtitle);
				$this->graph->title->SetFont(FF_FONT2,FS_ITALIC, 10);
			}

			// $this->graph->SetBox(true);

			$this->graph->img->SetAntiAliasing();

			$this->graph->ygrid->SetFill(true,'#EFEFEF@0.8','#BBCCFF@0.8');
			$this->graph->xgrid->Show();

			// Labels
			$this->graph->xaxis->SetTickLabels($this->labels);
			if($this->rotate_labels) $this->graph->xaxis->SetLabelAngle(90);
			$this->graph->xaxis->SetTextTickInterval(1);

			if($this->label_interval) $this->graph->xaxis->SetTextLabelInterval($this->label_interval);

			if($this->xtitle != "") {
				$this->graph->xaxis->title->Set($this->xtitle);
				$this->graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
			}

			if($this->ytitle != "") {
				$this->graph->yaxis->title->Set($this->ytitle);
				$this->graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
			}

			// Plots
			$lplots= array();

			if(!$this->double) {

				$num_plots= count($this->plot_names) + 1; // The first plot is empty

				for($i=1; $i < $num_plots; $i++) {

					$j= $i - 1;

					$current_graph_type= $this->get_parameter("SERIE" . $i . "_TYPE", $graph_type);
					if(!class_exists($current_graph_type)) die("** ERROR: Graph plot type $current_graph_type not found. **");
					$is_bar= (stripos($current_graph_type, "bar") !== false);

					$current_graph_color= $this->get_parameter("SERIE" . $i . "_COLOR", $jpgraph_colors[$j]);
					$current_fill_color=  $this->get_parameter("SERIE" . $i . "_FILL_COLOR", false);
					$current_line_style= $this->get_parameter("SERIE" . $i . "_LINE_STYLE", 'solid');
					$current_graph_show_values= $this->get_parameter("SERIE" . $i . "_SHOW_VALUES", false);

					$lplots[$j]=new $current_graph_type($this->values[$i]);

					$this->graph->Add($lplots[$j]);

					$lplots[$j]->SetColor($current_graph_color);
					$lplots[$j]->SetWeight(1);
					if(!$is_bar) $lplots[$j]->SetStyle($current_line_style);
					if($this->show_legend) $lplots[$j]->SetLegend($this->plot_names[$i]);

					if($this->show_values or $current_graph_show_values) {
						if(!$is_bar) {
							$lplots[$j]->mark->SetType(DEFAULT_MARK_TYPE);
							$lplots[$j]->mark->SetSize(DEFAULT_MARK_SIZE);
						}
						$lplots[$j]->value->SetFont(FF_FONT1,FS_NORMAL,10);
						$lplots[$j]->value->SetColor($current_graph_color);
						$lplots[$j]->value->SetFormat('%d');
						$lplots[$j]->value->Show();
					}

					if($current_fill_color) $lplots[$j]->SetFillColor($current_fill_color . "@0.7");

					//$this->graph->Add($lplots[$j]);
				}

				if(stripos($current_graph_type, "bar")!==false) {
					if($this->accumulated) {
						$gbbplot = new AccBarPlot($lplots);
						$group_plot= new GroupBarPlot(array($gbbplot));
					} else {
						$group_plot= new GroupBarPlot($lplots);
					}
					$this->graph->Add($group_plot);
				}

			} else {

				// Dummy barplot
				$lplots[0]= new BarPlot($this->values[3]);

				// First bar plot
				$lplots[1]=new BarPlot($this->values[1]);
				if($this->show_legend) $lplots[1]->SetLegend($this->plot_names[1]);
				$lplots[1]->SetFillColor($jpgraph_colors[0] . "@0.5");

				if($this->show_values) {
					if(stripos($graph_type, "bar")===false) {
						$lplots[1]->mark->SetType(DEFAULT_MARK_TYPE);
						$lplots[1]->mark->SetSize(DEFAULT_MARK_SIZE);
					}
					$lplots[1]->value->Show();
					$lplots[1]->value->SetFont(FF_FONT1,FS_NORMAL,10);
					$lplots[1]->value->SetColor($jpgraph_colors[0]);
					$lplots[1]->value->SetFormat('%d');
				}

				$ybplot = new GroupBarPlot(array($lplots[1],$lplots[0]));

				// Second bar plot
				$lplots[2]=new BarPlot($this->values[2]);
				if($this->show_legend) $lplots[2]->SetLegend($this->plot_names[2]);
				$lplots[2]->SetFillColor($jpgraph_colors[1] . "@0.5");

				if($this->show_values) {
					if(stripos($graph_type, "bar")===false) {
						$lplots[2]->mark->SetType(DEFAULT_MARK_TYPE);
						$lplots[2]->mark->SetSize(DEFAULT_MARK_SIZE);
					}
					$lplots[2]->value->Show();
					$lplots[2]->value->SetFont(FF_FONT1,FS_NORMAL,10);
					$lplots[2]->value->SetColor($jpgraph_colors[0]);
					$lplots[2]->value->SetFormat('%d');
				}

				if($this->show_values) $lplots[2]->value->Show();
				$yb2plot = new GroupBarPlot(array($lplots[0],$lplots[2]));

				// Build
				$this->graph->Add($ybplot);
				$this->graph->AddY2($yb2plot);
			}
		}


		protected function get_parameter($param_name, $default_value="") {

			return isset($this->$param_name) ? $this->$param_name : $default_value;
		}


		public function getImageTag() {

			$rnd= rand(10000,99999);
			$filename="/tmp/tmpimg_$rnd.png";
			$file_base= basename($filename);

			$this->render($filename);

			$tag= paste_image($filename);
			unlink($filename);

			return $tag;
		}

		public function showMessage($msg, $filename="") {
			// Create the graph.
			$this->graph = new CanvasGraph(350,200,"auto");

			$t1 = new Text($msg);
			$t1->SetPos(0.05,0.5);
			$t1->SetOrientation("h");
			$t1->SetFont(FF_FONT1,FS_NORMAL);
			$t1->SetBox("white","black",'gray');
//			$t1->SetColor("black");
			$this->graph->AddText($t1);

			$this->graph->Stroke($filename);
		}

		public function add_parameters($parameters) {

			if($this->extrainfo!= "") {
				$this->extrainfo.= ";" . $parameters;
			} else {
				$this->extrainfo= $parameters;
			}

			$this->parse_parameters();
		}

		protected function parse_parameters() {

			// Evalue parameters
			foreach($this->parameters as $parameter => $value) {

				switch(strtoupper($parameter)) {
					case "HEIGHT":
						$this->forced_height= $value;
						break;
					case "WIDTH":
						$this->forced_width= $value;
						break;
					case "SHOW_VALUES":
						$this->show_values= (strtolower($value) != "false");
						break;
					case "SHOW_LEGEND":
						$this->show_legend= (strtolower($value) != "false");
						break;
					case "ROTATE_LABELS":
						$this->rotate_labels= (strtolower($value) == "true");
						break;
					case "TITLE":
						$this->title= $value;
						break;
					case "SUBTITLE":
						$this->subtitle= $value;
						break;
					case "XTITLE":
						$this->xtitle= $value;
						break;
					case "YTITLE":
						$this->ytitle= $value;
						break;
					case "TOP":
						$this->forced_top= $value;
						break;
					case "BOTTOM":
						$this->forced_bottom= $value;
						break;
					case "LEFT":
						$this->forced_left= $value;
						break;
					case "RIGHT":
						$this->forced_right= $value;
						break;
					case "VERTICAL":
						$this->vertical= (strtolower($value) == "true");
						break;
					case "LABEL_INTERVAL":
						$this->label_interval= $value;
						break;
					case "THEME":
						$this->theme= $value . "Theme";
						break;
					case "GRAPH_TYPE":
						$this->graph_type=$value;
						break;

					default:
//						if(!property_exists($this,$parameter)) {
//							html_showWarning("Tag parameter unknown: $parameter -> $value");
//						}
						$this->$parameter=$value;
				}
			}
		}

		protected function combine_data() {

			$series= explode("<GRAPH_SERIE>", $this->data);

			// Remove empty sets
			for($i=0; $i < count($series); $i++) {
				if($series[$i] == "") {
					unset($series[$i]);
					continue;
				}
			}

			$num_series= count($series);

			// Get keys from all series
			$keys= array();
			$splitted_series= array();

			$i=0;
			foreach($series as $serie) {
				$splitted_series[$i]= array();

				$lines= explode("\n",$serie);

				foreach($lines as $line) {
					if($line == "") continue;
					$values= explode(",",$line);

					$key= $values[0];
					$keys[$key]=0;
					unset($values[0]);

					$separator="";
					$linear_values="";
					foreach($values as $value) {
						$linear_values= $separator . trim($value);
						$separator=",";
					}
					$splitted_series[$i][$key]= $linear_values;
				}
				$i++;
			}

			// Now generate an unique array using keys
			$data_array= array();

			$i=0;
			foreach($keys as $key => $value) {

				$data_array[$i]= "$key";

				foreach($splitted_series as $serie) {

					$values= isset($serie[$key]) ? $serie[$key] : "0";
					$data_array[$i].= "," . $values;
				}
				$i++;
			}

			$this->data= implode("\n", $data_array);
		}
	}
?>
