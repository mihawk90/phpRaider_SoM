<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// load footer?
$load_footer = 1;

// report for output
include(RAIDER_CLASS_PATH.'report'.DIRECTORY_SEPARATOR.'report.php');
$report = &new ReportList;

if(!$pMain->checkPerm('view_members')) {
	pRedirect('index.php?option=com_login&task=login');
}

if(empty($task) || $task == '') {
	// report setup
	setupOutput();

	// paging and sorting
	$report->showRecordCount(true);
	$report->allowPaging(true, $_SERVER['PHP_SELF'].'?option='.$option.'&amp;Base=');
	$report->setListRange((empty($_GET['Base'])?'0':$_GET['Base']), $pConfig['report_max']);
	$report->allowLink(ALLOW_HOVER_INDEX, '', array());
	$report->allowSort(true, $_GET['Sort'], $_GET['SortDescending'], 'index.php?option='.$option);

	// setup column headers
	$report->addOutputColumn('username', $pLang['username'], null, 'left', null, '__NOLINK__');
	$report->addOutputColumn('email', $pLang['email'], null, 'center', null, '__NOLINK__');
	$report->addOutputColumn('join_date', $pLang['join_date'], null, 'center', null, '__NOLINK__');
	$report->addOutputColumn('group', $pLang['group'], null, 'right', null, '__NOLINK__');
	$report->addOutputColumn('checkbox', '<input type="checkbox" onClick="invertAll(this, this.form);" />', null, 'right', null, '__NOLINK__');

	// get data from database
	$members = array();

	$sql['SELECT'] = 'p.*, g.group_name AS group_name';
	$sql['FROM'] = 'profile p';
	$sql['JOIN'] = array(
						array('TYPE'=>'LEFT','TABLE'=>'groups AS g','CONDITION'=>'p.group_id = g.group_id')
	);
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);
	while($data = $db_raid->fetch()) {
		// checkboxes
		if(($pMain->getProfileID() == $data['profile_id']) || $pMain->checkPerm('edit_characters_any')) {
			$checkbox = ' <input type="checkbox" name="select[]" value="'.$data['profile_id'].'">';
		} else {
			$checkbox = '';
		}

		array_push($members,
			array(
				'username' => $data['username'],
				'join_date' => newDate($pConfig['date_format'].' @ '.$pConfig['time_format'], $data['join_date'], $pConfig['timezone'] + $pConfig['dst']),
				'group' => $data['group_name'],
				'email' => $data['user_email'],
				'checkbox' => $checkbox,
			)
		);
	}

	// put data into variable for output
	$output = $report->getListFromArray($members);

	if($pMain->checkPerm('edit_characters_own') || $pMain->checkPerm('edit_characters_any'))
		$output .= '<br><div style="text-align:right">'.$pLang['with_selected'].'
				<input type="image" src="templates/'.$pConfig['template'].'/images/icons/icon_delete.png"
				onClick="return display_confirm(\''.$pLang['confirm_delete'].'\')"></div>';

	$p->assign('header', $pLang['meHeader']);
	$p->assign('output', $output);
	$p->display(RAIDER_TEMPLATE_PATH.'members.tpl');
} else if($task == 'delete') {
	// verify permissions
	if($pMain->checkPerm('delete_members')) {
		for($i = 0; $i < count($_POST['select']); $i++) {
			$isSelf = ($pMain->getProfileID() == intval($_POST['select'][$i]));

			// Remove all signedup chars from this profile
			$sql2['SELECT'] = 'c.character_id';
			$sql2['FROM'] = 'character c';
			$sql2['WHERE'] = 'c.profile_id='.intval($_POST['select'][$i]);
			$sql['DELETE'] = 'signups';
			$sql['WHERE'] = 'character_id IN ('.$db_raid->parse_query('select', $sql2).')';
			$db_raid->set_query('delete', $sql, __FILE__, __LINE__);

			// Remove the characters
			$sql['DELETE'] = 'character';
			$sql['WHERE'] = 'profile_id='.intval($_POST['select'][$i]);
			$db_raid->set_query('delete', $sql, __FILE__, __LINE__);

			// Last, remove the profile
			$sql['DELETE'] = 'profile';
			$sql['WHERE'] = 'profile_id='.intval($_POST['select'][$i]);
			$db_raid->set_query('delete', $sql, __FILE__, __LINE__);
		}
		if ($isSelf == true) {
			// Kill session when deleting own character.
			pLogout();
		}
	}

	pRedirect('index.php?option='.$option);
} else {
	echo "Invalid option specified";
}
?>