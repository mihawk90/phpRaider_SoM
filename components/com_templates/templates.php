<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// load footer?
$load_footer = 1;

// verify permissions
if(!$pMain->checkPerm('edit_raid_templates')) {
	pRedirect('index.php?option=com_login&task=login');
}

// report for output
include(RAIDER_CLASS_PATH.'report'.DIRECTORY_SEPARATOR.'report.php');
$report = &new ReportList;

if(empty($task) || $task == '') {
	// output templates list
	$sql["SELECT"] = "*";
	$sql["FROM"] = "raid_templates";
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	// array for data
	$phpr_a = array();

	while($data = $db_raid->fetch()) {
		// setup array for data output
		array_push($phpr_a,
			array(
				'location' => $data['location'],
				'icon_name' => $data['icon_name'],
				'show_icon' => $show_icon,
				'checkbox' => $admin.' <input type="checkbox" name="select[]" value="'.$data['raid_template_id'].'">',
			)
		);
	}

	// report setup
	setupOutput();

	// paging and sorting
	$report->showRecordCount(true);
	$report->allowPaging(true, $_SERVER['PHP_SELF'].'?option='.$option.'&amp;Base=');
	$report->setListRange((empty($_GET['Base'])?'0':$_GET['Base']), $pConfig['report_max']);
	$report->allowLink(ALLOW_HOVER_INDEX, '', array());
	$report->allowSort(true, $_GET['Sort'], $_GET['SortDescending'], 'index.php?option='.$option);

	// setup column headers
	$report->addOutputColumn('location', $pLang['location'], 'null', 'center');
	$report->addOutputColumn('icon_name', $pLang['icon_name'], 'null', 'center');

	$report->addOutputColumn('checkbox', '<input type="checkbox" onClick="invertAll(this, this.form);" />', 'null', 'right');

	// put data into variable for output
	$output = $report->getListFromArray($phpr_a);

	$p->assign('create_new', $pLang['create_new']);
	$p->assign('header', $pLang['tHeader']);
	$p->assign('output', $output);
	$p->display(RAIDER_TEMPLATE_PATH.'templates.tpl');
} else if($task == 'delete') {
	for($i = 0; $i < count($_POST['select']); $i++) {
		$sql['DELETE'] = 'raid_templates';
		$sql['WHERE'] = 'raid_template_id='.intval($_POST['select'][$i]);
		$db_raid->set_query('delete', $sql, __FILE__, __LINE__);

		$sql['DELETE'] = 'raid_templates_limits';
		$sql['WHERE'] = 'raid_id='.intval($_POST['select'][$i]);
		$db_raid->set_query('delete', $sql, __FILE__, __LINE__);
	}

	pRedirect('index.php?option='.$option);
} else {
	printError($pLang['invalidOption']);
}
?>