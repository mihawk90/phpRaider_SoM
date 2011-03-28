<?php
// no direct access
defined('_VALID_SETUP') or die('Restricted Access');

include(RAIDER_BASE_PATH.'configuration.php');
include(RAIDER_BASE_PATH.'includes'.DIRECTORY_SEPARATOR.'functions.db.php');

// writing information to database
$link = mysql_connect($pConfig['db_server'], $pConfig['db_user'], $pConfig['db_pass']);

if(!$link) {
	$output .= '<font color=red>Unable to connect to database.</font><br>';
	$error = 1;
}

if(!mysql_select_db($pConfig['db_name']))
	$output .= '<font color=red>Unable to select database.</font><br>';
else
	$output .= '<font color=lime>Database selected.</font><br>';

// parse the sql file
$sqlErrors = sqlFromFile(RAIDER_BASE_PATH.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'schema'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'install.sql');
if ($sqlErrors) {
	if (is_array($sqlErrors)) {
		foreach ($sqlErrors as $sqlError) {
			$output .= '<font color=red><strong>Error:</strong> '.$sqlError['error_message'].' ('.$sqlError['error_number'].')</font> with sql <b>'.$sqlError['sql'].'</b><br>';
		}
	} else {
		$output .= $sqlErrors;
	}
} else {
	$output .= '<font color=lime>SQL execution complete.</font><br><br>Click <a href=install.php?option='.$next_option.'>here</a> to continue.';
}

// update a few configuration settings
$self = str_replace('/install/install.php','', $_SERVER['PHP_SELF']). '/';

mysql_query("UPDATE ".$pConfig['db_prefix']."config SET `value`='http://{$_SERVER['HTTP_HOST']}{$self}' WHERE `name`='site_url'");
mysql_close($link);

$p->assign('next_option',$next_option);
$p->assign('output',$output);
$p->display('6.tpl');
?>