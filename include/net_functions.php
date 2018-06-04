<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage net
 *
 * Net functions.
 *
 */

	/**
	 * ipInNetwork
	 *
	 * This function will return true if the IP $ip belongs to the network $net with
	 * mask $mask. Else return false.
	 *
	 * @param $ip string
	 * @param $net string
	 * @param $mask string
	 */
	function ipInNetwork($ip, $net, $mask) {

	   $ip = ip2long($ip);
	   $l_net = ip2long($net);
	   $l_mask = ip2long($mask);
	   $l_ip = $ip & $l_mask;

	   return ($l_ip == $l_net);
	}

	/**
	 * ipInNetworks
	 *
	 * This function will return true if the IP $ip belongs to any of the
	 * networks passed into the array $networks, each array element is a pair
	 * $network => $mask
	 * Else return false.
	 *
	 * @param $ip string
	 * @param $networks array($net => $mask)
	 */
	function ipInNetworks($ip, $networks) {
		foreach($networks as $net => $mask) {
			if(ipInNetwork($ip, $net, $mask)) return true;
		}
		return false;
	}
?>