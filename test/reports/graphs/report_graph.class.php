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


	global $jpgraph_colors;
//	$jpgraph_colors= array("blue","darkred","green","maroon");
	$jpgraph_colors= array("#0000CD","#9370DB","#B0C4DE","#8B008B","#DA70D6","darkred","green","maroon");


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
		protected $width=200;
		protected $height=200;
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

		protected $top = 60;
		protected $bottom = 120;
		protected $left = 30;
		protected $right = 30;


		public $show_values=true;

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

			if($this->double) {
				$this->values[3]= array();

				for($i=1; $i<= count($this->values[1]); $i++) {
					$this->values[3][]=0;
				}
			}


			$this->get_overriden_values();

			///////////////////////////////////////////////////////////////////////////
			// Create graph
			$this->graph= new Graph($this->width, $this->height, "auto");
			$this->graph->SetMarginColor("#ffffff");
			$this->graph->SetScale("textlin");

			$theme_class= new SoftyTheme();
			$this->graph->SetTheme($theme_class);

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
			$this->graph->SetFrameBevel(0,false);

			if($this->title != "") {
				$this->graph->title->Set($this->title);
				$this->graph->title->SetFont(FF_FONT2,FS_BOLD);
			}
			if($this->subtitle != "") $this->graph->subtitle->Set($this->subtitle);

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

					$lplots[$j]=new $graph_type($this->values[$i]);
					$lplots[$j]->SetColor($jpgraph_colors[$j]);
					$lplots[$j]->SetWeight(1);
					if($this->show_legend) $lplots[$j]->SetLegend($this->plot_names[$i]);

					if($this->show_values) {
						$lplots[$j]->value->Show();
						$lplots[$j]->value->SetFont(FF_FONT1,FS_NORMAL,10);
						$lplots[$j]->value->SetColor($jpgraph_colors[$j]);
						$lplots[$j]->value->SetFormat('%d');
					}
					if(stripos($graph_type, "bar")!==false) {
						$lplots[$j]->SetFillColor($jpgraph_colors[$j] . "@0.7");
					} else {
						$this->graph->Add($lplots[$j]);
					}
				}

				if(stripos($graph_type, "bar")!==false) {
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
					$lplots[2]->value->Show();
					$lplots[2]->value->SetFont(FF_FONT1,FS_NORMAL,10);
					$lplots[2]->value->SetColor($jpgraph_colors[1]);
					$lplots[2]->value->SetFormat('%d');
				}
				if($this->show_values) $lplots[2]->value->Show();
				$yb2plot = new GroupBarPlot(array($lplots[0],$lplots[2]));

				// Build
				$this->graph->Add($ybplot);
				$this->graph->AddY2($yb2plot);
			}
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
			$t1->SetColor("black");
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

/*
			$serie0_plain= explode("\n",$series[1]);
			$serie0= array();
			foreach($serie0_plain as $line) {

				$values= explode(",",$line);

				$key= $values[0];
				unset($values[0]);
				if(count($values)==0) continue;

				foreach($values as $value) {
					$serie0[$key][]= trim($value);
				}
			}

			for($i=2; $i < count($series); $i++) {
				$serieN_plain= explode("\n",$series[$i]);

				foreach($serieN_plain as $line) {

					$values= explode(",",$line);

					$key= $values[0];
					unset($values[0]);

					if(count($values)==0) continue;

					foreach($values as $value) {
						$serie0[$key][]= trim($value);
					}
				}
			}

			$this->data="";
			foreach($serie0 as $key=>$values) {
				if(count($values) == 0) continue;
				$this->data.= $key . "," . implode(",", $values) . "\n";
			}
*/
		}
	}
?>
