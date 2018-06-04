<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage news about vulnerabilities
 *
 * NVD version prior to 1.2 source plugin
 *
 * Based on the article "http://www.sitepoint.com/article/php-xml-parsing-rss-1-0" of Kevin Yank
 *
 */

/*
	This script is a modification of the original xml parser for NVD.NIST.GOV for the adaption to
	the SIGVI environment. At the end of the file is stored the original header message.
*/

	include_once INC_DIR . "/output.inc.php";

	abstract class xmlItem {

		protected $db;

		public function xmlItem(&$db = null) {
			global $global_db;

			$this->db= ($db !=null) ? $db : $global_db;
		}

		abstract public function store_it();
	}

	abstract class xmlParser {

		public $db;

		public $URL;
		public $max;
		public $xmlItems;

		protected $insideitem = false;
		protected $tag = "";

		protected $xml_parser;
		protected $fp;
		protected $continue;
		protected $counter;


		public function xmlParser($url, &$db= null, $max_items = 0) {

			global $global_db;

			$this->URL= $url;
			$this->db= ($db !=null) ? $db : $global_db;
			$this->max= $max_items;

			$this->xmlItems= array();
		}

		public function read_xml() {

			if($this->URL == "") return false;

			// code for this function is from http://us2.php.net/xml
			$this->xml_parser = xml_parser_create();
			// use case-folding so we are sure to find the tag in $map_array
			xml_parser_set_option($this->xml_parser, XML_OPTION_CASE_FOLDING, true);
			xml_set_object($this->xml_parser, $this);
			xml_set_element_handler($this->xml_parser, "startElement", "endElement");
			xml_set_character_data_handler($this->xml_parser, "characterData");

			if (!($this->fp = fopen($this->URL, "r"))) return false;

			$this->parse_this();

			fclose($this->fp);

			return true;
		}

		public function store_data() {

			// if($this->max == 0) return 0;

			foreach($this->xmlItems as $xmlItem) $xmlItem->store_it();
			return 1;
		}

		protected function parse_this() {

			$this->counter=0;
			$this->continue= true;

			while (($data = fread($this->fp, 4096)) and $this->continue) {
				{
					if (!xml_parse($this->xml_parser, $data, feof($this->fp))) {
						die(sprintf("XML error: %s at line %d",	xml_error_string(xml_get_error_code($this->xml_parser)), xml_get_current_line_number($this->xml_parser)));
					}
				}
			}

			xml_parser_free($this->xml_parser);
		}

		abstract protected function startElement($parser, $tagName, $attrs);
		abstract protected function endElement($parser, $tagName);
		abstract protected function characterData($parser, $data);
	}
?>