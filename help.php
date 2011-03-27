<?php
// set flag for parent component
define('_VALID_RAID',1);

// setup base paths (added in 1.0.2 to simplify directories)
define('RAIDER_BASE_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('RAIDER_INCLUDE_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR);
define('RAIDER_LANGUAGE_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR);

// connect to database server
include(RAIDER_BASE_PATH.'configuration.php');
include(RAIDER_BASE_PATH.'includes'.DIRECTORY_SEPARATOR.'database.php');

global $db_raid;
$db_raid = new pr_sql_db($pConfig['db_server'], $pConfig['db_user'], $pConfig['db_pass'], $pConfig['db_name'], $pConfig['db_prefix'], $pConfig['db_pers'],(isset($pConfig['db_newlink'])?$pConfig['db_newlink']:false));

// show database error message
if(!$db_raid->db_connect_id)
	die('Database Connection Error');

// unset database password for security reasons
// we won't use it after this point
unset($pConfig['db_pass']);

// load configuration variables into configuration array
$sql["SELECT"] = "*";
$sql["FROM"] = "config_auth";
$db_raid->set_query('select', $sql, __FILE__, __LINE__);

while($data = $db_raid->fetch()) {
	$pConfig_auth[$data['name']] = $data['value'];
}

// load configuration authentication variables into configuration array
$sql["SELECT"] = "*";
$sql["FROM"] = "config";
$db_raid->set_query('select', $sql, __FILE__, __LINE__);

while($data = $db_raid->fetch()) {
	$pConfig[$data['name']] = $data['value'];
}

include_once(RAIDER_LANGUAGE_PATH.$pConfig['language'].'.php');

$topic = $_GET['topic'];

if($topic == '' || empty($pLang['he_'.$topic])) {
	// load default component
	echo sprintf($pLang['heInvalidTopic'], $topic);
} else {
	// display the help file
	echo $pLang['he_'.$topic];
}
?>