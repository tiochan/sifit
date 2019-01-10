<?php

$URL="project in ('CREHC', 'CRE - Incidents') and status='Feature request'";
echo $URL . "<br>";
echo urlencode($URL);


//QUERY="project%20in%20%28%27CREHC%27%2C%20%22CRE%20-%20Incidents%22%29%20and%20status%3D%22Feature%20request%22";
QUERY="project+in+%28%27CREHC%27%2C+%27CRE+-+Incidents%27%29+and+status%3D%27Feature+request%27";
URL="https://jira.schibsted.io/rest/api/2/search?jql=$QUERY"

$output_file='{{RANDOM_FILENAME}}';
`curl -s -u {{GENERIC_CRE_USER_NAME}}:{{GENERIC_CRE_USER_OKTA_PWD}} "$URL" -o $output_file`;

$output_mode='{{$OUTPUT_MODE}}';
$content= file_get_contents($output_file);
//unlink($output_file);

$content=json_decode($content);
echo "<pre>"; print_r($content); echo "</pre>";
/*
switch(strtolower($output_mode)) {
	case "table":
		$content=json_decode($content);
		echo $repo . ": " . count($content) . "n";
		echo "<table style='border: 1px solid #aaaaaa; padding: 15px'>";
		echo "<tr><th>PR</th><th>Owner</th><th>Created on</th><th>Days ago</th><th>Count</th></tr>";
		foreach($content as $pr) {
		$date1= strtotime($pr->created_at);
		$days=round(($current_date - $date1)/$seconds_per_day,1);

		echo "<tr>";
		echo "<td><a href='" . $pr->html_url . "' target='_blank'>" . $pr->title . "</a></td>";
		echo "<td>" . $pr->user->login . "</td>";
		echo "<td>" . $pr->created_at . "</td>";
		echo "<td style='text-align:right'>" . $days . "</td>";
		echo "<td style='text-align:right'>" . 0/3 . "</td>";
		echo "</tr>";
		}
		echo "</table>";
		break;
	case "json":
		echo $content;
		break;
	case "text":
		foreach($content as $issue) {
		echo $pr->title . "(" . $pr->user->login . "), ";
		echo $pr->created_at . " 0/3</br>";
		}
		break;
	default:
		print_r(json_decode($content));
}
*/