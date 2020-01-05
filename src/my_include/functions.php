<?php
/*
	Author: Sebastian Gomez, (tiochan@gmail.com)
	For: Politechnical University of Catalonia (UPC), Spain.

	Respoitory auxiliar functions

	require:
*/

	/**
	 * This function is used to normalize a hostname or ip, set on user_entered.
	 * If user_entered is an IP, will set on $ip itself, and on $hostname, the
	 * corresponding DNS resolution, or the same IP if there is not resolution.
	 *
	 * If user_entered is the hostname, will set on $ip the DNS resolved, and on
	 * $hostname the corresponding DNS resolution (which can be different from
	 * original).
	 *
	 * It can be used to normalize IPs and hostnames specially for nmap usage.
	 *
	 * @param string $user_entered
	 * @param string $hostname
	 * @param string $ip
	 * @return boolean
	 */
	function normalize_hostname_and_ip($user_entered, &$hostname, &$ip) {

		global $MESSAGES;

		// What have the user entered into the dummy field, an IP or the hostname?
		$t_ip= @gethostbyname($user_entered);
		// Check if is a valid IP
		$l_ip= ip2long($t_ip);
		if($l_ip === false) return false;

		$t_hn= @gethostbyaddr($t_ip);

		if($t_ip == $t_hn) html_showWarning("No DNS resolution found for $user_entered");

		$ip= $t_ip;
		$hostname= $t_hn;

		return true;
	}

	function return_mac_address($ip) {
		// This code is under the GNU Public Licence
		// Written by michael_stankiewicz {don't spam} at yahoo {no spam} dot com
		// Tested only on linux, please report bugs


		// WARNING: the commands 'which' and 'arp' should be executable
		// by the apache user; on most linux boxes the default configuration
		// should work fine


		// get the arp executable path
		$location = 'which arp';
		$location = rtrim ( $location );
		// Execute the arp command and store the output in $arpTable
		$arpTable = '$location -n';
		// Split the output so every line is an entry of the $arpSplitted array
		$arpSplitted = explode( "\n", $arpTable );
		// get the remote ip address (the ip address of the client, the browser)
		//$remoteIp = $_SERVER ['REMOTE_ADDR'];
		$remoteIp= $ip;
		$remoteIp = str_replace ( ".", "\\.", $remoteIp );
		// Cicle the array to find the match with the remote ip address
		foreach ( $arpSplitted as $value ) {
			// Split every arp line, this is done in case the format of the arp
			// command output is a bit different than expected
			$valueSplitted = explode( " ", $value );
			foreach ( $valueSplitted as $spLine ) {
				if (preg_match ( "/$remoteIp/", $spLine )) {
					$ipFound = true;
				}
				// The ip address has been found, now rescan all the string
				// to get the mac address
				if ($ipFound) {
					// Rescan all the string, in case the mac address, in the string
					// returned by arp, comes before the ip address
					// (you know, Murphy's laws)
					reset ( $valueSplitted );
					foreach ( $valueSplitted as $spLine ) {
						if (preg_match ( "/[0-9a-f][0-9a-f][:-]" . "[0-9a-f][0-9a-f][:-]" . "[0-9a-f][0-9a-f][:-]" . "[0-9a-f][0-9a-f][:-]" . "[0-9a-f][0-9a-f][:-]" . "[0-9a-f][0-9a-f]/i", $spLine )) {
							return $spLine;
						}
					}
				}
				$ipFound = false;
			}
		}
		return false;
	}

?>