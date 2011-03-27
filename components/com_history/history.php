<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// load footer?
$load_footer = 1;

// verify permissions
if(!$pMain->checkPerm('view_history_own')) {
	pRedirect('index.php?option=com_login&task=login');
}

// report for output
include(RAIDER_CLASS_PATH.'report'.DIRECTORY_SEPARATOR.'report.php');
$report = &new ReportList;

if(empty($task) || $task == '') {
	$history = getRaidList(1, $pMain->getProfileID(), 1);

	// setup raid output
	setupOutput();

	// paging and sorting
	$report->showRecordCount(true);
	$report->allowPaging(true, $_SERVER['PHP_SELF'].'?option='.$option.'&amp;Base=');
	$report->setListRange((empty($_GET['Base'])?'0':$_GET['Base']), $pConfig['report_max']);
	$report->allowLink(ALLOW_HOVER_INDEX, '', array());
	$report->allowSort(true, $_GET['Sort'], $_GET['SortDescending'], 'index.php?option='.$option);

	// setup column headers
	$report->addOutputColumn('location', $pLang['location'], 'null', 'center', null, null);
	$report->addOutputColumn('officer', $pLang['officer'], 'null', 'center', null, '__NOLINK__');
	$report->addOutputColumn('date', $pLang['date'], 'null', 'center', null, '__NOLINK__');
	$report->addOutputColumn('invite_time', $pLang['invite_time'], 'null', 'center', null, '__NOLINK__');
	$report->addOutputColumn('start_time', $pLang['start_time'], 'null', 'center', null, '__NOLINK__');

	// put data into variable for output
	$output = $report->getListFromArray($history);

	$p->assign('create_new', $pLang['create_new']);
	$p->assign('header', $pLang['rhHeader']);
	$p->assign('output', $output);
	$p->display(RAIDER_TEMPLATE_PATH.'history.tpl');
} else {
	printError($pLang['invalidOption']);
}
?>