<?php

// $seconds_per_day=86400;
// $current_date= strtotime('now');

$string_date='2018-12-11T16:44:16Z';

$date1= strtotime($string_date);
$seconds=round(($current_date - $date1)/(24*60*60),1);
