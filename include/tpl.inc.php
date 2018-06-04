<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage sequence
 *
 * Template functions
 */

$tpl_tags= array("{SERVER_URL}" => SERVER_URL,
	"{HOME}" => HOME,
	"{SYSHOME}" => SYSHOME,
	"{INC_DIR}" => INC_DIR,
	"{ICONS}" => ICONS,
	"{IMAGES}" => IMAGES,
	"{MY_IMAGES}" => MY_IMAGES,
	"{MY_ICONS}" => MY_ICONS,
	"{APP_NAME}" => APP_NAME,
	"{APP_DESCRIPTION}" => APP_DESCRIPTION,
	"{APP_VERSION}" => APP_VERSION,
	"{APP_LOGO}" => APP_LOGO,
	"{APP_MINILOGO}" => APP_MINILOGO
);

$tpl_objects= array(
	'/<\{IMAGE ([^>]*)\}>/i' => "paste_image_force"
);


function paste_image_force($file) {
	return paste_image($file, true);
}

/**
 * Get the contents of a template. Will search for it on:
 *  - First on the my_include/templates dir, if not found, then at
 *  - include/templates
 *
 * @param string $tpl
 * @return string
 */
function tpl_get($tpl) {
	global $tpl_tags, $tpl_objects;

	$tpl_dir= $tpl;
	if(!file_exists($tpl_dir)) {

		$tpl_dir= MY_TPL_DIR . "/$tpl";

		if(!file_exists($tpl_dir)) {
			$tpl_dir= TPL_DIR . "/$tpl";
			!file_exists($tpl_dir) and die("Can't find template $tpl at ./, " . MY_TPL_DIR . " nor " . TPL_DIR . "\n");
		}
	}

	$content=file_get_contents($tpl_dir);

	//$style= file_get_contents(INC_DIR . "/styles/styles.css");
	$style= file_get_contents(INC_DIR . "/styles/vmail_styles.css");
	$content= str_replace("{STYLE}",$style, $content);

	foreach($tpl_tags as $tag => $replacement) {
		$content= str_replace($tag,$replacement,$content);
	}

	foreach($tpl_objects as $object => $function) {

		$count= preg_match_all($object, $content, $matches);

		for($i=0; $i < $count; $i++) {
			$args= $matches[1][$i];
			$ret= $function($args);

			$content= preg_replace($object, $ret, $content,1);
		}
	}

	return $content;
}

function tpl_include($tpl) {
	global $tpl_tags;


	$tpl_dir= MY_TPL_DIR . "/$tpl";

	if(!file_exists($tpl_dir)) {
		$tpl_dir= TPL_DIR . "/$tpl";
		!file_exists($tpl_dir) and die("Can't find template $tpl at " . MY_TPL_DIR . " nor " . TPL_DIR . "\n");
	}

	include $tpl_dir;
}

class template{

	protected $tpl_file;
	protected $mihtml;
	protected $template_file;
	protected $vars;

	public function template($template_file){

		$this->tpl_file = $template_file . '.tpl.php';
	}

	public function set_vars($vars){

		$this->vars= (empty($this->vars)) ? $vars : $this->vars . $vars;
	}

	public function show(){

		if (!($this->fd = @fopen($this->tpl_file, 'r'))) {
			html_showError('Error opening template ' . $this->tpl_file);
		} else {
			$this->template_file = fread($this->fd, filesize($this->tpl_file));
			fclose($this->fd);
			$this->mihtml = $this->template_file;
			$this->mihtml = str_replace ("'", "\'", $this->mihtml);
			$this->mihtml = preg_replace('#\{([a-z0-9\-_]*?)\}#is', "' . $\\1 . '", $this->mihtml);

			reset ($this->vars);
			while (list($key, $val) = each($this->vars)) {
				$$key = $val;
			}
			eval("\$this->mihtml = '$this->mihtml';");
			reset ($this->vars);
			while (list($key, $val) = each($this->vars)) {
				unset($$key);
			}
			$this->mihtml=str_replace ("\'", "'", $this->mihtml);
			echo $this->mihtml;
		}
	}
}
