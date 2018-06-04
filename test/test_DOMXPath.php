<?php


	$content= file_get_contents("http://www.upc.edu");

	$doc = new DOMDocument();
	if(!$doc->loadHTML($content)) die("Error 1");



	function getTags( $dom, $tagName, $attrName, $attrValue ){
	    $html = '';
	    $domxpath = new DOMXPath($dom);
	    $newDom = new DOMDocument;
	    $newDom->formatOutput = true;

	    $filtered = $domxpath->query("//$tagName" . '[@' . $attrName . "='$attrValue']");
	    // $filtered =  $domxpath->query('//div[@class="className"]');
	    // '//' when you don't know 'absolute' path

	    // since above returns DomNodeList Object
	    // I use following routine to convert it to string(html); copied it from someone's post in this site. Thank you.
	    $i = 0;
	    while( $myItem = $filtered->item($i++) ){
	        $node = $newDom->importNode( $myItem, true );    // import node
	        $newDom->appendChild($node);                    // append node
	    }
	    $html = $newDom->saveHTML();
	    return $html;
	}

	echo htmlspecialchars(getTags($doc, "link", "type", "text/css"));
	echo "<hr>";

	echo getTags($doc, "div", "id", "baners");
	echo "<hr>";
	echo getTags($doc, "div", "class", "columna_3");

	exit;

	$elem=$doc->getElementById('baners');

echo "The element whose id is 'php-basics' is: " . $doc->getElementById('baners')->tagName . "\n";
exit;
//print_r($baners);

echo "<br>--------------------------------------------------------<br>";
echo "<h1>XPATH</h1>";
echo "<br>--------------------------------------------------------<br>";


$xpath = new DOMXpath($doc);

// example 1: for everything with an id
//$elements = $xpath->query("//*[@id]");

// example 2: for node data in a selected id
//$elements = $xpath->query("/html/body/div[@id='yourTagIdHere']");

// example 3: same as above with wildcard

$query= "*/div[@id='baners']";
//$query= '//*[@id]';
$query= "//*[@id='baners']";

$elements = $xpath->query($query)->item(0);





if (!is_null($elements)) {
  foreach ($elements as $element) {
    echo "<br/><b>[". $element->nodeName. "]</b><br>";

    $nodes = $element->childNodes;
    foreach ($nodes as $node) {
      echo "<pre>\n" . htmlspecialchars($node->nodeValue) . "\n</pre><hr>\n";
    }
  }
}
?>