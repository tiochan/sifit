<?php
/*
$w = stream_get_wrappers();
echo 'openssl: ',  extension_loaded  ('openssl') ? 'yes':'no', "\n";
echo 'http wrapper: ', in_array('http', $w) ? 'yes':'no', "\n";
echo 'https wrapper: ', in_array('https', $w) ? 'yes':'no', "\n";
echo 'wrappers: ', var_export($w);

exit;
*/

$shell_command="
#!/bin/env sh
exec 2>&1
OUTPUT_FILE='/tmp/kk.txt'

echo 'ola ke ase' > \$OUTPUT_FILE
echo 'line 1'
echo 'line 2'
echo 'line 3'
asdf fda
exit 0
";

$python_command="#!/usr/bin/env python
# Python program to swap two variables

# To take input from the user
# x = input('Enter value of x: ')
# y = input('Enter value of y: ')

x = 5
y = 10

# create a temporary variable and swap the values
temp = x
x = y
y = temp

print('The value of x after swapping: {}'.format(x))
print('The value of y after swapping: {}'.format(y))
";

$command= $python_command;

/*
$output= shell_exec($command);
echo $output;
*/
/*
exec($command, $output, $exit_code );
echo "Exit code: $exit_code\n";
$output_string= implode("\n",$output);
echo $output_string;
*/

$rand_name= "/tmp/rand_file_" . rand(1000,9999) . ".tmp";
file_put_contents($rand_name, $command);
chmod($rand_name, 0700);

ob_start();
passthru($rand_name . " 2>&1");
$var = ob_get_contents();
ob_end_clean();
echo $var;

unlink($rand_name);


exit;



$url="https://www.infojobs.net/ofertas-trabajo/barcelona/vilafranca-del-penedes";
//$url="http://www.ccma.cat/324/";
//$url='https://example.com/';
$opts = array(
	'https' => array(
		'method'=>"GET",
		'header' => "" .
		'User-Agent:"Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:45.0) Gecko/20100101 Firefox/45.0;' .
			'Content-Type: text/html; charset=utf-8;' .
			'Accept:"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"'
	),
	'ssl' => array(
		"verify_peer" => false,
		"allow_self_signed" => true,
	)
);

$context = stream_context_create($opts);
echo file_get_contents($url,false,$context);