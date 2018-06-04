<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage admin
 *
 * Filters related functions.
 */

	$severities= array( "" => 0,
						"low" => 1,
						"med" => 2,
						"medium" => 2,
						"high" => 3);

	define("FILTER_TYPE_FIELD_PASS_AND",0);
	define("FILTER_TYPE_FIELD_PASS_OR",1);
	define("FILTER_TYPE_FIELD_FILTER_AND",2);
	define("FILTER_TYPE_FIELD_FILTER_OR",3);


	define("FILTER_FIELDS","f_type,".
			"severity,".
			"ar_launch_remotely,".
			"ar_launch_locally,".
			"lt_security_protection,".
			"lt_obtain_all_priv,".
			"lt_obtain_some_priv,".
			"lt_confidentiality,".
			"lt_integrity,".
			"lt_availability,".
			"vt_input_validation_error,".
			"vt_boundary_condition_error,".
			"vt_buffer_overflow,".
			"vt_access_validation_error,".
			"vt_exceptional_condition_error,".
			"vt_environment_error,".
			"vt_configuration_error,".
			"vt_race_condition,".
			"vt_other_vulnerability_type");

	/**
	 * Generic test for vulnerability and filters.
	 *
	 * @param array $row    associative array containing the filter fields
	 * @param vulnerability $vuln
	 */
	function vuln_pass_filter($row, $vuln) {
		global $severities;


		$filter_type=$row["f_type"];
		if( $filter_type != FILTER_TYPE_FIELD_PASS_AND and
			$filter_type != FILTER_TYPE_FIELD_PASS_OR and
			$filter_type != FILTER_TYPE_FIELD_FILTER_AND and
			$filter_type != FILTER_TYPE_FIELD_FILTER_OR) {
		// Unknown filter type. In case of doubt don't discard this vulnerability.
// echo "UNKNOWN FILTER TYPE $filter_type. Doubt --> PASS\n";
			return true;
		}

		/**
		 * Initial value of return var. This var contains the ending value to be returned.
		 *
		 * So, depending on filter type, its initial value must be one or other:
		 * If filter type is:
		 * - FILTER_TYPE_FIELD_PASS_AND, if all values are ok, then return true. When one value
		 *     that doesn't fullfils is found, will return false.
		 *     If arrives to the end implies that all values are ok. Initial value: true.
		 * - FILTER_TYPE_FIELD_PASS_OR, if one value is ok, then return true. When one value
		 *     that fullfils is found, will return true.
		 *     If arrives to the end implies that none of then are ok. Initial value: false.
		 * - FILTER_TYPE_FIELD_FILTER_AND, if all values are ok, then return false. When one value
		 *     that doesn't fullfils is found, will return true.
		 *     If arrives to the end implies that all values are ok. Initial value: false.
		 * - FILTER_TYPE_FIELD_FILTER_OR, if one value is ok, then return false. When one value
		 *     that fullfils is found, will return false.
		 *     If arrives to the end implies that none of then are ok. Initial value: true.
		 */

		$ret= ( ($filter_type == FILTER_TYPE_FIELD_FILTER_AND) or (FILTER_TYPE_FIELD_PASS_OR)) ? true : false;

//echo "Vuln " . $vuln->vuln_id . "\n";
//echo "filter type: $filter_type\n";

//echo " * Initial return value: " . ($ret ? "true" : "false") . "\n";

		foreach($row as $property => $filter_value) {

			if($filter_value == '0') continue;			// Value '0' equal to 'any'

			if(!property_exists($vuln, $property)) continue;

			$current_value= $vuln->$property;

			switch(strtolower($property)) {
				case "severity":

					$lower= intval($filter_value);
					$val= strtolower($current_value);
					$current_value = $severities[$val];



					$current_ret= ($current_value >= $lower);
					break;

				default:

					$filter_value= $filter_value == '1' ? 1 : 0; // Filter values: 0 = any, 1= true, 2= false;
					// $current_value= $current_value == '1'? 1 : 0; // Filter values: 0 = any, 1= true, 2= false;
					$current_ret= ($current_value == $filter_value);

					break;
			}

//echo " * Comparing values. Field: $property, Filter value: $filter_value, current value: $current_value\n";

			// Depending on filter type must act as...
			switch($filter_type) {
				case FILTER_TYPE_FIELD_PASS_AND:
					// Must fullfil all properties. If one is false then doesn't pass the filter.
					if(!$current_ret) {
//echo " * Filter type $filter_type: return false.\n";
						return false;
					}
					break;

				case FILTER_TYPE_FIELD_PASS_OR:
					// Must fullfil at least one properties.
					if($current_ret) {
//echo " * Filter type $filter_type: return false.\n";
						return true;
					}
					break;

				case FILTER_TYPE_FIELD_FILTER_AND:
					// Must fullfil all properties. If one is false then doesn't pass the filter, then return true.
					if(!$current_ret) {
//echo " * Filter type $filter_type: return true.\n";
						return true;
					}
					break;

				case FILTER_TYPE_FIELD_FILTER_OR:
					// Must fullfil at least one properties.
					if(!$current_ret) {
//echo " * Filter type $filter_type: return true.\n";
						return false;
					}
					break;
			}
		}
//echo " * Filter type $filter_type: return INITIAL VALUE.\n";
		return $ret;
	}

	/**
	 * This function will check for user notification filter. It is defined at the same user table.
	 * This filter has fixed some properties of the vulnerabilities.
	 *
	 * If the vulnerability passes the user notificacion filter, then returns true, else returns false.
	 *
	 * @param integer $id_user
	 * @param array of vulnerability $vuln
	 * @return bool
	 */
	function vuln_pass_user_filter($id_user, $vuln) {
		global $global_db;
		global $severities;

		$ret= true;
// echo " - Checking filter for user $id_user for vulnerability " . $vuln->vuln_id . "\n";

		$query="select " . FILTER_FIELDS . " from users, filters where users.id_user= '$id_user' and users.id_filter = filters.id_filter";

		$res= $global_db->dbms_query($query);

		if($global_db->dbms_check_result($res)) {
			while(($row=$global_db->dbms_fetch_array($res)) && $ret) {
				$ret= vuln_pass_filter($row, $vuln);
			}

			$global_db->dbms_free_result($res);
		} else {
			// If no rows found, then no filter defined for this user.
		}
// if ($ret) echo "--------------- PASSED -------------\n";
		return $ret;
	}

	/**
	 * This function will check for server check filter. It is defined at the same server table.
	 * This filter has fixed some properties of the vulnerabilities.
	 *
	 * If the vulnerability passes the server check filter, then returns true, else returns false.
	 *
	 * @param integer $id_server
	 * @param vuln_gen $vuln
	 * @return bool
	 */
	function vuln_pass_server_filter($id_server, $vuln) {

		global $global_db;
		global $severities;

		$ret= true;

		$query="select " . FILTER_FIELDS . " from servers, filters where servers.id_server= '$id_server' and servers.deleted='0' and servers.id_filter = filters.id_filter";

		$res= $global_db->dbms_query($query);

		if($global_db->dbms_check_result($res)) {
			while(($row=$global_db->dbms_fetch_array($res)) && $ret) {
				$ret= vuln_pass_filter($row, $vuln);
			}

			$global_db->dbms_free_result($res);
		} else {
			// If no rows found, then no filter defined for this server.
		}
		// if ($ret) echo "--------------- PASSED -------------\n";
		// else echo "--------------- NOT PASSED -------------\n";
		return $ret;
	}
?>
