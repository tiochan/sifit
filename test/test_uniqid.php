<?php


define("QUIET",true);
define("SHOW_MENU",false);

include_once "../include/init.inc.php";

$endpoint= SERVER_URL . HOME . "/test/api_calls.php";


/**
 * Function for generating a random string, used for
 * generating a token for the XML-RPC session
 * ORIGIN: https://www.drupal.org/node/339845
 */
function getUniqueCode($length = "") {
  $code = md5(uniqid(rand(), true));
  if ($length != "") return substr($code, 0, $length);
  else return $code;
}

$id= getUniqueCode();
echo $id . " | Len: " . strlen($id) . "<br>";


if(!$api_key= get_http_param("API_KEY",0)) die("API_KEY not set (parameter API_KEY)");
if(!$secret_key= get_http_param("SECRET_KEY",0)) die("SECRET_KEY not set (parameter SECRET_KEY)");

file_get_contents($endpoint);
