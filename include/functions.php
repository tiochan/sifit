<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage auxiliar functions
 *
 */

function isNaN( $var ) {
    return ereg ('^[-]?[0-9]+([\.][0-9]+)?$', $var);
}

function number_unformat($formatted_number) {

    $aux= $formatted_number;

    // Convert formatted currency to float format ('.' as decimal separator)

    if(MON_THOUSAND_SEP=='.') {
        $aux= str_replace(MON_THOUSAND_SEP, "", $aux);
        $aux= str_replace(MON_DECIMAL_SEP, ".", $aux);
    } else {
        $aux= str_replace(",", "", $aux);
    }

    return $aux;
}

function get_http_param($parm_name, $default_value= null) {

    $ret= get_http_post_param($parm_name, false);
    if($ret === false) {
        $ret= get_http_get_param($parm_name, false);
        $ret= ($ret === false) ? $default_value : $ret;
    }

    return $ret;
}

function get_http_post_param($parm_name, $default_value= null) {

    if(isset($_POST[$parm_name])) {
        return $_POST[$parm_name];
    }

    return $default_value;
}

function get_http_get_param($parm_name, $default_value= null) {

    if(isset($_GET[$parm_name])) {
        return $_GET[$parm_name];
    }

    return $default_value;
}

function remove_double_spaces($str) {
    $ret= $str;
    while(strpos($ret, "  ") !== false) {
        $ret= str_replace("  "," ",$ret);
    }
    return $ret;
}

function check_referer() {
    global $SCRIPT_FILENAME;
    global $REMOTE_ADDR;

    html_showInfo("<br><b>Verifying remote IP </b>($REMOTE_ADDR)...");

    $dir_name = dirname($SCRIPT_FILENAME);
    $hosts_file = $dir_name."/hosts";

    if (!file_exists($hosts_file)) {
        html_showInfo("No hosts defined.");
        return true;
    }

    $valid_ip = file($hosts_file);

    if (count($valid_ip) == 0) {
        html_showInfo("No hosts defined.");
        return true;
    }

    foreach ($valid_ip as $ip) {
        if (trim($ip) == $REMOTE_ADDR) {
            html_showInfo("<b> Ok</b><br>");
            return true;
        }
    }
    html_showInfo("<b> Not granted.</b>");
    return false;
}

function read_dir($dir, $extension = "") {

    $array = array ();

    if(file_exists($dir)) {

        $d = dir($dir);

        while (false !== ($entry = $d->read())) {

            if ($extension != "") {
                if (strpos($entry, $extension) > 0)
                    $array[] = $entry;
            } else {
                if ($entry != '.' and $entry != '..')
                    $array[] = $entry;
            }
        }
        $d->close();
    }
    return $array;
}

function print_object($object) {

    echo "<PRE>";
    print_r($object);
    echo "</PRE>";
}

function substrings($text, $sopener, $scloser,$k=1){
    $result = array();

    $noresult = substr_count($text, $sopener);
    $ncresult = substr_count($text, $scloser);
    if ($noresult < $ncresult)
        $nresult = $noresult;
    else
        $nresult = $ncresult;

    unset($noresult);
    unset($ncresult);

    for ($i=0;$i<$nresult;$i++){
        $pos = strpos($text, $sopener) + strlen($sopener);

        $text = substr($text, $pos, strlen($text));

        $pos = strpos($text, $scloser);

        $result[] = substr($text, 0, $pos);

        $text = substr($text, $pos + strlen($scloser), strlen($text));
    }
    if($k && count($result)==1) return $result[0];
    else return $result;
}

function is_included($filename) {

    $included_files= get_included_files();

    foreach($included_files as $inc) {
        if($filename == basename($inc)) return true;
    }
    return false;
}

function include_one_file_if_exists($first_preference, $include_if_first_doesnt_exists)
{

    if (file_exists($first_preference)) {
        include_once $first_preference;
        return true;
    }

    if(file_exists($include_if_first_doesnt_exists)) {
        include_once $include_if_first_doesnt_exists;
        return true;
    }

    return false;
}

function get_app_param($parameter, $default_value=null) {
    global $global_db;

    $ret= $default_value;

    $query= "select value from app_parameters where parameter='$parameter'";
    $res= $global_db->dbms_query($query);

    if(!$res) return $ret;

    if(!(list($ret)= $global_db->dbms_fetch_row($res))) return null;

    $global_db->dbms_free_result($res);
    return $ret;
}

function myempty($var) {
    return (!isset($var) or (empty($var) and ($var !== 0) and ($var !== '0')));
}

function cut_string($str, $len, $add_str=" [..]") {

    $add_len= strlen($add_str);

    $new_str= strlen($str) > $len ? substr($str, 0, $len - $add_len) . $add_str : $str;
    return $new_str;
}

/**
 * Returns the relative web reference to the root of the application
 *
 * @return string
 */
function app_get_home_dir() {

    if(basename($_SERVER['REQUEST_URI']) == "app.conf.php") die("File not loadable.");

    $dir= dirname(dirname(__FILE__));
    $home= $_SERVER['REQUEST_URI'];
    while($home != "" and (strpos($dir, $home) === false)) {
        $home= dirname($home);
    }
    if($home == "/") return "";
    return $home;
}

/**
 *
 * Function extracted from the PHP Doc manual Web, wroten by calvin at g mail dot com
 * Thanks a lot for this function avoiding for mortals the use of the nightmare DOM and XPATH classes.
 * http://php.net/manual/en/domdocument.getelementsbytagname.php
 *
 * @param DOMDocument $dom the DOM Document to search into
 * @param string $tagName the Name of the tag for search
 * @param string $attrName
 * @param string $attrValue
 * @param string $search For each matching node, search the string into it (if not null) to pass filter.
 * @return string The HTML code
 */
function DOMgetTags( $dom, $tagName, $attrName, $attrValue, $search="" ){
    $html = '';
    $domxpath = new DOMXPath($dom);
    $newDom = new DOMDocument;
    $newDom->formatOutput = true;

    $search_terms= explode("|",$search);

    $filtered = $domxpath->query("//$tagName" . '[@' . $attrName . "='$attrValue']");
    // $filtered =  $domxpath->query('//div[@class="className"]');
    // '//' when you don't know 'absolute' path

    // since above returns DomNodeList Object
    // I use following routine to convert it to string(html); copied it from someone's post in this site. Thank you.
    $i = 0;
    while( $myItem = $filtered->item($i++) ){
        $node = $newDom->importNode( $myItem, true );    // import node
        if($search == "") $newDom->appendChild($node);    // append node
        else {
            foreach($search_terms as $search_term) {
                if(strpos($node->nodeValue, $search_term)!== false) {
                    $newDom->appendChild($node);                    // append node
                    break;
                }
            }
        }
    }
    $html = $newDom->saveHTML();
    return $html;
}

/**
 *
 * Same as above, but using path instead.
 *
 * @param DOMDocument $dom the DOM Document to search into
 * @param string $tagName the Name of the tag for search
 * @param string $path
 * @param string $search For each matching node, search the string into it (if not null) to pass filter.
 * @return string The HTML code
 */
function DOMgetPath( $dom, $path, $search="" ){
    $html = '';
    $domxpath = new DOMXPath($dom);
    $newDom = new DOMDocument;
    $newDom->formatOutput = true;

    $search_terms= explode("|",$search);

    $filtered = $domxpath->query($path);
    $i = 0;
    while( $myItem = $filtered->item($i++) ){
        $node = $newDom->importNode( $myItem, true );    // import node
        if($search == "") $newDom->appendChild($node);    // append node
        else {
            foreach($search_terms as $search_term) {
                if(strpos($node->nodeValue, $search_term)!== false) {
                    $newDom->appendChild($node);                    // append node
                    break;
                }
            }
        }
    }

    $html = $newDom->saveHTML();
    return $html;
}
