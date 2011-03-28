<?php
// no direct access
defined('_VALID_SETUP') or die('Restricted Access');

// setup authentication list
$dh = opendir(RAIDER_BASE_PATH.'authentication'.DIRECTORY_SEPARATOR);
while(false != ($filename = readdir($dh))) {
	if (preg_match("/^[\w\-]+$/si", $filename) == 1) {
		if (is_dir(RAIDER_BASE_PATH.'authentication'.DIRECTORY_SEPARATOR.$filename)) {
			if (file_exists(RAIDER_BASE_PATH.'authentication'.DIRECTORY_SEPARATOR.$filename.DIRECTORY_SEPARATOR.$filename.'.functions.php')) {
				$files[] = $filename;
			}
		}
	}
}
natcasesort($files);

if(empty($_POST) || !in_array($_POST['authentication'],$files)) {

	$games = '<select name="authentication">';

	foreach($files as $value) {
		if($value == 'phpraider') {
			$name = $value.' [default]';
			$selected = 'selected';
		} else {
			$selected = '';
			$name = $value;
		}

		$games .= '<option value="'.$value.'"'.$selected.'>'.$name.'</option>';
	}

	$games .= '</select>';

	$p->assign('games', $games);

	unset($files);

	// template file
	$p->assign('auth_method',$games);
	$p->assign('next_option',$next_option);
	$p->display('7.tpl');
} else {
	include(RAIDER_BASE_PATH.'configuration.php');

	// set authentication method
	$link = mysql_connect($pConfig['db_server'], $pConfig['db_user'], $pConfig['db_pass']);
	mysql_select_db($pConfig['db_name']);
	mysql_query("UPDATE `{$pConfig['db_prefix']}config` SET `value`='{$_POST['authentication']}' WHERE `name`='authentication'");

	$_SESSION['pConfig_authentication'] = $_POST['authentication'];

	header("Location:install.php?option=".$next_option);
	exit;
}
?>