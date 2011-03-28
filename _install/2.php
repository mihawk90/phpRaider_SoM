<?php
// no direct access
defined('_VALID_SETUP') or die('Restricted Access');

// introduction file
if($task == '') {
	$p->assign('next_option',$next_option);
	$p->display($option.'.tpl');
} else {
	printError($pLang['invalidOption']);
}
?>