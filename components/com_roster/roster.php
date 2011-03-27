<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// load footer?
$load_footer = 1;

// report for output
include(RAIDER_CLASS_PATH.'report'.DIRECTORY_SEPARATOR.'report.php');
$report = &new ReportList;

if(!$pMain->checkPerm('view_roster')) {
	pRedirect('index.php?option=com_login&task=login');
}

if(empty($task) || $task == '') {
	$char_data = getCharacterData($pMain);

	// report setup
	setupOutput();

	// paging and sorting
	$report->showRecordCount(true);
	$report->allowPaging(true, $_SERVER['PHP_SELF'].'?option='.$option.'&amp;Base=');
	$report->setListRange((empty($_GET['Base'])?'0':$_GET['Base']), $pConfig['report_max']);
	$report->allowLink(ALLOW_HOVER_INDEX, '', array());
	$report->allowSort(true, $_GET['Sort'], $_GET['SortDescending'], 'index.php?option='.$option);

	// setup column headers
	$report->addOutputColumn('name', $pLang['name'], null, 'left', null, '__NOLINK__');
	$report->addOutputColumn('race', $pLang['race'], null, 'center', null, '__NOLINK__');
	$report->addOutputColumn('class', $pLang['class'], null, 'center', null, '__NOLINK__');
	$report->addOutputColumn('level', $pLang['level'], null, 'center', null, '__NOLINK__');
	$report->addOutputColumn('guild', $pLang['guild'], null, 'center', null, '__NOLINK__');

	// setup attribute columns
	$sql["SELECT"] = "att_name, att_icon, att_show";
	$sql["FROM"] = "attribute";
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);
	while($data = $db_raid->fetch()) {
		if($data['att_show']) {
			$icon = '<img src="games/'.$pConfig['game'].'/images/attributes/'.$data['att_icon'].'"
					onMouseover="ddrivetip(\''.$data['att_name'].'\');" onMouseout="hideddrivetip();" height="18" width="18" alt="'.$data['att_name'].'">';
			$report->addOutputColumn($data['att_name'], $icon, 'null', 'center');
		}
	}

	if($pMain->checkPerm('edit_characters_own') || $pMain->checkPerm('edit_characters_any'))
		$report->addOutputColumn('checkbox', '<input type="checkbox" onClick="invertAll(this, this.form);">', 'null', 'right');

	// put data into variable for output
	$output = $report->getListFromArray($char_data);

	if($pMain->checkPerm('edit_characters_own') || $pMain->checkPerm('edit_characters_any'))
		$output .= '<br><div style="text-align:right">'.$pLang['with_selected'].'
				<input type="image" src="templates/'.$pConfig['template'].'/images/icons/icon_delete.png"
				onClick="return display_confirm(\''.$pLang['confirm_delete'].'\')"></div>';

	$p->assign('header', $pLang['roHeader']);
	$p->assign('output', $output);
	$p->display(RAIDER_TEMPLATE_PATH.'roster.tpl');
} else {
	echo "Invalid option specified";
	exit;
}
?>