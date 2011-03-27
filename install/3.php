<?php
// no direct access
defined('_VALID_SETUP') or die('Restricted Access');

// introduction file
if($task == '') {
	if(empty($_POST)) {
		// setup install types
		$install = '<select name="install" class="post">';
		$install .= '<option value="install">Fresh Installation</option>';
		$install .= '<option value="upgrade">Upgrade</option>';
		$install .= '</select>';
	} else {
		if($_POST['install'] == 'install') {
			header("Location: install.php?option=4");
			exit;
		} else {
			header("Location: upgrade.php");
		}
	}

	$p->assign('install',$install);
	$p->assign('next_option',$next_option);
	$p->display($option.'.tpl');
} else {
	printError($pLang['invalidOption']);
}
?>