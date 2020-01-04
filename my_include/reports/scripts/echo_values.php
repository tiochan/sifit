<?php

	function echo_values() {

		//$values= array(300, 200, 550, 220, 1000, 1200 ,500);
		$values= array(rand(10,1000), rand(10,1000), rand(10,1000), rand(10,1000), rand(10,1000), rand(10,1000), rand(10,1000));

		$return= "";

		for($i=1; $i<=7; $i++) {
			$return.= $i . "," . $values[($i - 1)] . "\n";
		}

		return $return;
	}

?>
