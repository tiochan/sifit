<?php

	include "../include/init.inc.php";
	include "../my_include/classes/user_random_key.class.php";

	$ret= generate_new_key();
	echo "Nueva clave: $ret";
?>