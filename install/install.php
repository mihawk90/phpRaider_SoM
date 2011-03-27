<?php
// set flag for parent component
define('_VALID_SETUP',1);

// setup base paths (added in 1.0.2 to simplify directories)
define('RAIDER_BASE_PATH', str_replace('install'.DIRECTORY_SEPARATOR,'',dirname(__FILE__).DIRECTORY_SEPARATOR));

// buffering
ob_start();

// start session to store variables we need later
session_start();

isset($_GET['upgrade']) ? $_SESSION['upgrade'] = 1 : false;

clearstatcache();

if (file_exists(RAIDER_BASE_PATH.'templates_c') && is_writeable(RAIDER_BASE_PATH.'templates_c')) {
	// setup smarty template object
	include(RAIDER_BASE_PATH.'includes'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'smarty'.DIRECTORY_SEPARATOR.'Smarty.class.php');
	include(RAIDER_BASE_PATH.'includes'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'smarty'.DIRECTORY_SEPARATOR.'SmartyValidate.class.php');

	global $p;
	$p = &new Smarty;
	$p->cache_dir = '../cache';
	$p->compile_dir = '../templates_c';
	$p->caching = false;

	// setup common variables
	isset($_GET['task']) ? $task = $_GET['task'] : $task = '';
	isset($_GET['option']) ? $option = $_GET['option'] : $option = '';

	// Make sure variables are initialized to avoid warnings.
	if (!isset($pConfig['template'])) {
		$pConfig['template'] = 'default';
	}
	$output = '';
	$sql = '';
	$scripts = '';

	// header
	include('header.php');

	isset($_GET['option']) ? $i = $_GET['option'] : $i = 1;
	$next_option = $i+1;

	include($i.'.php');

	include('footer.php');
} else {
	echo 'The directory \''.RAIDER_BASE_PATH.'templates_c\' either doesn\'t exist or it\'s not writeable.<br>';
	echo 'Please read the file docs\README.html before you continue.';
}

ob_flush();
?>