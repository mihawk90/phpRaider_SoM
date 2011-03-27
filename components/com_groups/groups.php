<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// load footer?
$load_footer = 1;

// verify permissions
if(!$pMain->checkPerm('edit_groups')) {
	pRedirect('index.php?option=com_login&task=login');
}

// report for output
include(RAIDER_CLASS_PATH.'report'.DIRECTORY_SEPARATOR.'report.php');
$report = &new ReportList;

if(empty($task) || $task == '') {
	// output announcements list
	$sql['SELECT'] = 'g.group_id, g.group_name,	COUNT(p.profile_id) AS count';
	$sql['FROM'] = 'groups g';
	$sql['JOIN'] = array(
						array('TYPE'=>'LEFT','TABLE'=>'profile p','CONDITION'=>'g.group_id=p.group_id')
	);
	$sql['GROUPBY'] = 'g.group_id';
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	// array for data
	$phpr_a = array();

	while($data = $db_raid->fetch()) {
		// admin options
		$admin = '<a href="index.php?option='.$option.'&task=edit&id='.$data['group_id'].'">'.setIcon(_TEMPLATE_, 'icon_edit.png', $pLang['edit']).'</a> ';

		// setup array for data output
		array_push($phpr_a,
			array(
				'name'=>$data['group_name'],
				'members'=>'<a href="index.php?option='.$option.'&task=details&id='.$data['group_id'].'"><strong>'.$data['count'].'</strong></a>',
				'checkbox'=>$admin.' <input type="checkbox" name="select[]" value="'.$data['group_id'].'">',
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
	$report->addOutputColumn('name', $pLang['name'], 'null', 'center');
	$report->addOutputColumn('members', $pLang['members'], 'null', 'center');
	$report->addOutputColumn('checkbox', '<input type="checkbox" onClick="invertAll(this, this.form);" />', 'null', 'right');

	// put data into variable for output
	$output = $report->getListFromArray($phpr_a);

	$p->assign('create_new', $pLang['create_new']);
	$p->assign('header', $pLang['grHeader']);
	$p->assign('output', $output);
	$p->display(RAIDER_TEMPLATE_PATH.'groups.tpl');
} else if($task == 'new' || $task == 'edit') {
	// no caching for this
	$p->caching = false;

	// localizations
	$p->assign(
		array(
			// errors
			'nameError' => $pLang['grName_error'],

			// text
			'header' => $pLang['grCreate_header'],
			'nameText' => $pLang['grName_text'],

			// buttons
			'reset' => $pLang['reset'],
			'submit' => $pLang['submit']
		)
	);

	// assign task
	if($task == 'edit')
		$p->assign('task', $task.'&id='.$id);
	else
		$p->assign('task' , $task);

	if(empty($_POST)) {
		// new form, we (re)set the session data
		SmartyValidate::connect($p, true);

		// assign old values if it's an edit
		if($task == 'edit') {
			$sql['SELECT'] = '*';
			$sql['FROM'] = 'groups';
			$sql['WHERE'] = 'group_id='.$id;
			$db_raid->set_query('select', $sql, __FILE__, __LINE__);
			$p->assign($db_raid->fetch());
		}

		// register our validators
		SmartyValidate::register_validator('name', 'group_name', 'notEmpty', false, false, 'trim');

		// display form
		$p->display(RAIDER_TEMPLATE_PATH.'groups_form.tpl');
	} else {
		// validate after a POST
		SmartyValidate::connect($p);

		if(SmartyValidate::is_valid($_POST)) {
			// updating information so clear cache
			$p->clear_cache(RAIDER_TEMPLATE_PATH.'groups.tpl');

			// no errors, done with SmartyValidate
			SmartyValidate::disconnect();

			// update/insert into database
			$sql['VALUES']=array('group_name'=>$_POST['group_name']);
			if($task == 'new') {
				// setup variables not submitted by form
				$sql['INSERT'] = 'groups';
				$db_raid->set_query('insert', $sql, __FILE__, __LINE__);
			} else {
				$sql['UPDATE'] = 'groups';
				$sql['WHERE'] = 'group_id='.$id;
				$db_raid->set_query('update', $sql, __FILE__, __LINE__);
			}
			pRedirect('index.php?option='.$option);
		} else {
			// error, redraw the form
			$p->assign($_POST);
			$p->display(RAIDER_TEMPLATE_PATH.'groups_form.tpl');
		}
	}
} else if($task == 'delete') {
	// verify permissions
	for($i = 0; $i < count($_POST['select']); $i++) {
		$sql['DELETE'] = 'groups';
		$sql['WHERE'] = 'group_id='.intval($_POST['select'][$i]);
		$db_raid->set_query('delete', $sql, __FILE__, __LINE__);

		// delete permission set
		$sql['DELETE'] = 'permissions';
		$sql['WHERE'] = 'group_id='.intval($_POST['select'][$i]);
		$db_raid->set_query('delete', $sql, __FILE__, __LINE__);
	}

	pRedirect('index.php?option='.$option);
} else if($task == 'details') {
	// array for data
	$phpr_d = array();

	$sql['SELECT'] = '*';
	$sql['FROM'] = 'profile';
	$sql['WHERE'] = 'group_id='.$id;
	$sql['SORT'] = 'username';
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	while($data = $db_raid->fetch())
	{
		// setup array for data output
		array_push($phpr_d,
			array(
				'g_username'=>'<a href="index.php?option=com_users&id='.$data['profile_id'].'">'.$data['username'].'</a>',
				'g_email'=>$data['user_email'],
				'g_joindate'=>newDate($pConfig['date_format'], $data['join_date'], $pConfig['timezone'] + $pConfig['dst']).', '.newDate($pConfig['time_format'], $data['join_date'], $pConfig['timezone'] + $pConfig['dst']),
				'g_checkbox'=>'<input type="checkbox" name="select[]" value="'.$data['profile_id'].'">'
			)
		);
	}

	// report setup
	setupOutput();

	// paging and sorting
	$report->showRecordCount(true);
	$report->allowPaging(true, $_SERVER['PHP_SELF'].'?option='.$option.'&amp;task=details&id='.$_GET['id'].'&amp;Base=');
	$report->setListRange((empty($_GET['Base'])?'0':$_GET['Base']), $pConfig['report_max']);
	$report->allowLink(ALLOW_HOVER_INDEX, '', array());
	$report->allowSort(true, $_GET['Sort'], $_GET['SortDescending'], 'index.php?option='.$option.'&task=details&id='.$id);

	// setup column headers
	$report->addOutputColumn('g_username', $pLang['username'], 'null','center');
	$report->addOutputColumn('g_email', $pLang['email'],'null', 'center');
	$report->addOutputColumn('g_joindate', $pLang['join_date'], 'null', 'center');
	$report->addOutputColumn('g_checkbox', '<input type="checkbox" onClick="invertAll(this, this.form);" />', 'null', 'center');

	// setup "remove" button
	$remove_button = $pLang['with_selected'].'<input type="image" src="templates/'.$pConfig['template'].'/images/icons/icon_delete.png"
						onClick="return display_confirm(\''.$pLang['confirm_delete'].'\')">';

	// setup the "search for user" box
	$user_find = '<input type="text" name="username" class="post">
				<input type="submit" name="submituser" value="'.$pLang['grAdd'].'" class="mainoption">
				<input type="submit" name="usersubmit" value="'.$pLang['grFind'].'" onClick="window.open(\'index.php?option='.$option.'&task=search\', \'_phpraidsearch\', \'HEIGHT=250,resizable=yes,WIDTH=400\');return false;" class="liteoption">';

	// put data into variable for output
	$d_output = $report->getListFromArray($phpr_d).'<br><div style="text-align:right">'.$remove_button.'</div>';

	// parse output
	$p->assign(
		array(
			'gdHeader'=>$pLang['gdHeader'],
			'gdData'=>$d_output,
			'gdFind'=>$user_find,
			'group_id'=>$id
		)
	);

	$p->display(RAIDER_TEMPLATE_PATH.'groups_details.tpl');
} else if($task == 'search') {
	// no need to display header information
	ob_end_clean();

	$user_search = '<!-- courtesy of phpBB ;) DO NOT REMOVE -->
						<script language="javascript" type="text/javascript">
						<!--
						function refresh_username(selected_username)
						{
								opener.document.forms[\'post\'].username.value = selected_username;
								opener.focus();
								window.close();
						}
						//-->
						</script>';

	if(!isset($_POST['submit']))
	{
		// they haven't attempted a query yet
		$user_search .= '<form method="post" name="search" action="index.php?option=com_groups&task=search">
						<table width="100%" border="0" cellspacing="0" cellpadding="10">
							<tr>
								<td><table width="100%" class="dataOutline" cellpadding="4" cellspacing="1" border="0">
									<tr class="row2">
										<td height="25">'.$pLang['find_user'].'</td>
									</tr>

									<tr class="row1">
										<td valign="top" class="row1"><br />
										<input type="text" name="search_username" value="" class="post" />&nbsp;
										<input type="submit" name="submit" value="'.$pLang['search'].'" class="liteoption" /><br />'.$phprlang['users_wildcard'].'<br />
										<br /><a href="javascript:window.close();">'.$pLang['close_window'].'</a></td>
									</tr>
								</table></td>
							</tr>
						</table>
						</form>';
	} else {
		// check the database for matches
		$search_username = str_replace('*', '%', $_POST['search_username']);
		$sql['SELECT'] = '*';
		$sql['FROM'] = 'profile';
		$sql['WHERE'] = 'username LIKE '.$db_raid->quote_smart($search_username);
		$result = $db_raid->set_query('select', $sql, __FILE__, __LINE__);

		$options = '';
		if($db_raid->sql_numrows($result) == 0) {
			$options = '<option value="">'.$pLang['no_matches'].'</option>';
		} else {
			while($data = $db_raid->sql_fetchrow($result)) {
				$options .= '<option value="'.$data['username'].'">'.$data['username'].'</option>';
			}
		}

		$user_search .= '<form method="POST" name="search" action="index.php?option=com_groups&task=search">
						<table width="100%" border="0" cellspacing="0" cellpadding="10">
							<tr>
								<td><table width="100%" class="dataOutline" cellpadding="4" cellspacing="1" border="0">
									<tr class="row2">
										<td height="25">'.$pLang['find_user'].'</td>
									</tr>

									<tr>
										<td valign="top" class="row1"><br />
										<input type="text" name="search_username" value="" class="post" />&nbsp;
										<input type="submit" name="submit" value="'.$pLang['search'].'" class="liteoption" /><br />'.$phprlang['users_wildcard'].'<br />
										<br /><a href="javascript:window.close();">'.$pLang['close_window'].'</a></td>
									</tr>

									<tr>
										<td align="top" class="row1"><br><select name="username_list" class="post">'.$options.'</select>
										<input type="submit" class="liteoption" onClick="refresh_username(this.form.username_list.options[this.form.username_list.selectedIndex].value);
										return false;" name="use" value="'.$pLang['select'].'" />
								</table></td>
							</tr>
						</table>
						</form>';

	}

	// asign variables
	$p->assign('user_search', $user_search);

	// output
	$p->display(RAIDER_TEMPLATE_PATH.'search.tpl');

	// no need to display footer either
	exit;
} else if($task == 'add') {
	$profile_id = getProfileID($_POST['username']);

	if(empty($profile_id)) {
		pRedirect("index.php?option=".$option."&task=details&id=".$id."&error=USER_NOT_FOUND");
	}

	$sql['UPDATE'] = 'profile';
	$sql['VALUES'] = array('group_id'=>$id);
	$sql['WHERE'] = 'profile_id='.$profile_id;
	$db_raid->set_query('update', $sql, __FILE__, __LINE__);

	pRedirect("index.php?option=".$option."&task=details&id=".$id);
} else if($task == 'delete_user') {
	for($i = 0; $i < count($_POST['select']); $i++) {
		$sql['UPDATE'] = 'profile';
		$sql['VALUES'] = array('group_id'=>0);
		$sql['WHERE'] = 'profile_id='.intval($_POST['select'][$i]);
		$db_raid->set_query('update', $sql, __FILE__, __LINE__);
	}

	pRedirect('index.php?option='.$option.'&task=details&id='.$id);
} else {
	printError($pLang['invalidOption']);
}
?>