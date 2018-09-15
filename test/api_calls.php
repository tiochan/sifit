<?php

define("QUIET",true);

include_once "../include/init.inc.php";


if(!$api_key= get_http_param("API_KEY",0)) die("API_KEY not set (parameter API_KEY)");
if(!$secret_key= get_http_param("SECRET_KEY",0)) die("SECRET_KEY not set (parameter SECRET_KEY)");

echo "Hi $api_key\n";
echo "This is your place.";
