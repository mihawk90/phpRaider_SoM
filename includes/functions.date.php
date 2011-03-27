<?php
// date format with timezone offset
function newDate($format, $timestamp, $tz) {
	if (!empty($timestamp))	{
		$offset = (60 * 60) * ($tz / 100); // Seconds from GMT
		$timestamp = $timestamp + $offset;
	} else {
		$timestamp = 0;
	}
	return gmdate($format, $timestamp);
}

function timeDiff($timestamp,$detailed=false, $max_detail_levels=8, $precision_level='second'){
	global $pLang,$pConfig;

	$now = time()-getLocaleOffset(); // Calculated time to check against.

	// If the difference is positive "ago" - negative "away"
	($timestamp >= $now) ? $action = $pLang['away'] : $action = $pLang['ago'];

	// Set the periods of time
	$periods = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade');

	$lengths = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);

	$diff = ($action == $pLang['away'] ? $timestamp - $now : $now - $timestamp);

	$prec_key = array_search($precision_level,$periods);

	// round diff to the precision_level
	$diff = round(($diff/$lengths[$prec_key]))*$lengths[$prec_key];

	// if the diff is very small, display for ex "just seconds ago"
	if ($diff <= 10) {
		$periodago = max(0,$prec_key-1);
		$agotxt = (empty($pLang[$periods[$periodago].'_plural']))?$pLang[$periods[$periodago]].$pLang['plural']:$pLang[$periods[$periodago].'_plural'];
		return "just $agotxt $action";
	}

	// Go from decades backwards to seconds
	$time = '';
	for ($i = (sizeof($lengths) - 1); $i>=0; $i--) {
		if($diff > $lengths[$i] && ($max_detail_levels > 0)) {	// if the difference is greater than the length we are checking... continue
			$val = floor($diff / $lengths[$i]);					// 65 / 60 = 1.  That means one minute.  130 / 60 = 2. Two minutes.. etc
			$time .= $val ." ". ($val > 1 ? ((empty($pLang[$periods[$i].'_plural']))?$pLang[$periods[$i]].$pLang['plural']:$pLang[$periods[$i].'_plural']):$pLang[$periods[$i]]).' ';  // The value, then the name associated, then add 's' if plural
			$diff -= ($val * $lengths[$i]);    // subtract the values we just used from the overall diff so we can find the rest of the information
			if(!$detailed) { $i = 0; }    // if detailed is turn off (default) only show the first set found, else show all information
			$max_detail_levels--;
		}
	}

	// Basic error checking.
	if(empty($time)) {
		return 'Error-- Unable to calculate time.';
	} else {
		if($action == $pLang['away'])
			return $pLang['upcoming_raid'].$time.$action.$pLang['end_raid'];
		else
			return $pLang['old_raid'].$time.$action.$pLang['end_raid'];
	}
}

function getLocaleOffset(){
	global $pConfig;

	return (date('Z',time())-(36*($pConfig['timezone']+$pConfig['dst'])));
}

// Get current UTC time
function getUTCTime(){
	$utc_str = gmdate("M d Y H:i:s", time());
	return strtotime($utc_str);
}
?>
