<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// load footer?
$load_footer = 1;

// verify permissions
if(!$pMain->checkPerm('edit_permissions')) {
	pRedirect('index.php?option=com_login&task=login');
}

// define permission types
$sets = array(
			// headers
					// items
			'general' =>
				array(
					'allow_signup',
					'view_members',
					'view_raids',
					'view_roster',
				),
			'member_profiles' =>
				array(
					'view_history_own',
					'edit_characters_own',
					'edit_announcements_own',
					'edit_raids_own',
					'edit_subscriptions_own',
				),
			'administration' =>
				array(
					'allow_backups',
					'edit_configuration',
					'edit_attributes',
					'edit_definitions',
					'edit_genders',
					'edit_guilds',
					'edit_groups',
					'edit_meetings',
					'edit_permissions',
					'edit_roles',
				),
			'moderation' =>
				array(
					'view_history_any',
					'edit_announcements_any',
					'edit_characters_any',
					'delete_members',
					'edit_raids_any',
					'edit_subscriptions_any',
					'edit_raid_templates'
				),
		);

// report for output
include(RAIDER_CLASS_PATH.'report'.DIRECTORY_SEPARATOR.'report.php');
$report = &new ReportList;

if(empty($task) || $task == '') {
	// output announcements list
	$sql['SELECT'] = '*';
	$sql['FROM'] = 'groups';
	$group_cursor = $db_raid->set_query('select', $sql, __FILE__, __LINE__);

	// array for data
	$phpr_a = array();

	while($data = $db_raid->sql_fetchrow($group_cursor)) {
		// admin options
		$admin = '<a href="index.php?option='.$option.'&task=edit&id='.$data['permission_id'].'">'.setIcon(_TEMPLATE_, 'icon_edit.png', $pLang['edit']).'</a> ';

		$sql['SELECT'] = 'COUNT(*) AS count';
		$sql['FROM'] = 'profile';
		$sql['WHERE'] = 'group_id = '.$data['group_id'];
		$db_raid->set_query('select', $sql, __FILE__, __LINE__);
		$members = $db_raid->fetch();

		$sql["SELECT"] = "COUNT(*) AS count";
		$sql["FROM"] = "permissions";
		$sql["WHERE"] = "group_id = {$data['group_id']}";
		$db_raid->set_query('select', $sql, __FILE__, __LINE__);
		$permissions = $db_raid->fetch();

		if(empty($members))
			$members = '0';

		if(empty($permissions))
			$permissions = '0';

		// setup array for data output
		array_push($phpr_a,
			array(
				'name'=>$data['group_name'],
				'members'=>'<a href="index.php?option=com_groups&task=details&id='.$data['group_id']. '"><strong>'.$members['count'].'</strong></a>',
				'permissions'=>$permissions['count'],
				'modify'=>'<a href="index.php?option='.$option.'&task=details&id='.$data['group_id'].'">'.$pLang['modify'].'</a>',
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
	$report->addOutputColumn('name', $pLang['name'], 'null', 'left');
	$report->addOutputColumn('members', $pLang['members'], 'null', 'center');
	$report->addOutputColumn('permissions', $pLang['permissions'], 'null', 'center');
	$report->addOutputColumn('modify', $pLang['modify'], 'null', 'center');

	// put data into variable for output
	$output = $report->getListFromArray($phpr_a);

	$p->assign('create_new', $pLang['create_new']);
	$p->assign('header', $pLang['pHeader']);
	$p->assign('output', $output);
	$p->display(RAIDER_TEMPLATE_PATH.'permissions.tpl');
} else if($task == 'details') {
	$i = 0;
	$j = 0;

	// get data for all values of checkboxes from database
	$sql["SELECT"] = "*";
	$sql["FROM"] = "permissions";
	$sql["WHERE"] = "group_id={$id}";
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	// variable to store check defaults
	$check_defaults = array();
	while($data = $db_raid->fetch()) {
		if($data['permission_value'] == 1)
			$check_defaults[$data['permission_name']] = 'checked ';
	}

	// generate permissions details listing
	$d_output = '<form method="POST" action="index.php?option='.$option.'&task=add&id='.$id.'">
					<table width="100%" cellpadding="0" cellspacing="0" border="0">';

	// setup headers
	foreach($sets as $key=>$value) {
		if($i % 2 == 0)
			$d_output .= '<tr>';

		$d_output .= '<td width="48%" valign="top">';

		$d_output .= '<table width="100%" cellpadding="3" cellspacing="1" border="0" class="dataOutline">';
		$d_output .= '<tr class="listHeader" style="font-weight:bold">';
		$d_output .= '<td colspan="2">'.$pLang['pSet_'.$key].'</td>';
		$d_output .= '</tr>';

		foreach($value as $key2=>$value2) {
			// setup items
			$d_output .= '<tr class="'.$class.'">';
			$d_output .= '<td width="60%" class="name_class" style="font-weight:normal">
							<img onmouseover="ajax_showTooltip(\'help.php?topic='.$value2.'\',this); return false"  onmouseout="ajax_hideTooltip();" style="cursor:pointer" src="templates/'.$pConfig['template'].'/images/icons/icon_help.png" border="0"> '.$pLang['pSet_'.$value2].'</td>';
			$d_output .= '<td width="50%" class="field_class"><input type="checkbox" '.$check_defaults[$value2].'name="'.$value2.'"></td>';
			$d_output .= '</tr>';
		}

		$d_output .= '</table><br></td>';

		if($i % 2 == 0)
			$d_output .= '<td width="2%" valign="top">&nbsp;</td>';

		if($i % 2 == 1)
			$d_output .= '</tr>';

		$i++;
	}

	$d_output .= '</table><div style="text-align:right"><input type="submit" name="submit" value="'.$pLang['update'].'" class="mainoption"></div></form>';

	$p->assign('header', $pLang['pdHeader']);
	$p->assign('output', $d_output);
	$p->display(RAIDER_TEMPLATE_PATH.'permissions_details.tpl');
} else if($task == 'add') {
// delete all old values
	$sql["DELETE"] = "permissions";
	$sql["WHERE"] = "group_id = {$id}";
	$db_raid->set_query('delete', $sql, __FILE__, __LINE__);

	foreach($_POST as $key => $value) {
		if($value == 'on') 	{
			$sql["INSERT"] = "permissions";
			$sql["VALUES"] = array(
								'permission_name'=>$key,
								'group_id'=>$id,
								'permission_value'=>1
							);
			$db_raid->set_query('insert', $sql, __FILE__, __LINE__);
		}
	}

	pRedirect('index.php?option='.$option);
} else {
	printError($pLang['invalidOption']);
}
?>