<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// load footer?
$load_footer = 1;

// verify permissions
if(!$pMain->checkPerm('edit_roles')) {
	pRedirect('index.php?option=com_login&task=login');
}

// report for output
include(RAIDER_CLASS_PATH.'report'.DIRECTORY_SEPARATOR.'report.php');
$report = &new ReportList;

if(empty($task) || $task == '') {
	// setup the output
	$roles = array();

	$sql["SELECT"] = "*";
	$sql["FROM"] = "role";
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	while($data = $db_raid->fetch()) {
		// admin options
		$admin = '<a href="index.php?option='.$option.'&task=edit&id='.$data['role_id'].'">'.setIcon(_TEMPLATE_, 'icon_edit.png', $pLang['edit']).'</a> ';

		// setup array for data output
		array_push($roles,
			array(
				'name' => $data['role_name'],
				'body_color' => '<font color="'.$data['body_color'].'">'.$data['body_color'].'</font>',
				'header_color' => '<font color="'.$data['header_color'].'">'.$data['header_color'].'</font>',
				'font_color' => '<font color="'.$data['font_color'].'">'.$data['font_color'].'</font>',
				'admin' => $admin,
				'checkbox' => $admin.' <input type="checkbox" name="select[]" value="'.$data['role_id'].'"',
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

	$report->addOutputColumn('header_color', $pLang['header_color'], 'null', 'center');
	$report->addOutputColumn('body_color', $pLang['body_color'], 'null', 'center');
	$report->addOutputColumn('font_color', $pLang['font_color'], 'null', 'center');
	$report->addOutputColumn('checkbox', '<input type="checkbox" onClick="invertAll(this, this.form);" />', 'null', 'right');

	// put data into variable for output
	$roles = $report->getListFromArray($roles);

	$p->assign(
		array(
			// headers
			'header' => $pLang['roleHeader'],

			// other
			'create_new' => $pLang['create_new'],

			// data
			'output' => $roles,
		)
	);

	$p->display(RAIDER_TEMPLATE_PATH.'roles.tpl');
} else if($task == 'new' || $task == 'edit') {
	// no caching for this
	$p->caching = false;

	// localizations
	$p->assign(
		array(
			// errors
			'nameError' => $pLang['roleName_error'],
			'headerError' => $pLang['roleHeader_error'],
			'bodyError' => $pLang['roleBody_error'],
			'fontError' => $pLang['roleFont_error'],

			// text
			'header' => $pLang['roleCreate_header'],
			'nameText' => $pLang['roleName_text'],
			'headerText' => $pLang['roleHeader_text'],
			'bodyText' => $pLang['roleBody_text'],
			'fontText' => $pLang['roleFont_text'],

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
			$sql["SELECT"] = "*";
			$sql["FROM"] = "role";
			$sql["WHERE"] = "role_id={$id}";
			$db_raid->set_query('select', $sql, __FILE__, __LINE__);
			$p->assign($db_raid->fetch());
		}

		// register our validators
		SmartyValidate::register_validator('name', 'role_name', 'notEmpty', false, false, 'trim');
		SmartyValidate::register_validator('header', 'header_color', 'notEmpty', false, false, 'trim');
		SmartyValidate::register_validator('body', 'body_color', 'notEmpty', false, false, 'trim');
		SmartyValidate::register_validator('font', 'font_color', 'notEmpty', false, false, 'trim');

		// display form
		$p->display(RAIDER_TEMPLATE_PATH.'roles_form.tpl');
	} else {
		// validate after a POST
		SmartyValidate::connect($p);

		if(SmartyValidate::is_valid($_POST)) {
			// updating information so clear cache
			$p->clear_cache(RAIDER_TEMPLATE_PATH.'roles.tpl');

			// no errors, done with SmartyValidate
			SmartyValidate::disconnect();

			// update/insert into database
			$sql['VALUES']=array(
				'role_name'=>$_POST['role_name'],
				'header_color'=>$_POST['header_color'],
				'body_color'=>$_POST['body_color'],
				'font_color'=>$_POST['font_color']
			);
			if($task == 'new') {
				// setup variables not submitted by form
				$sql["INSERT"] = "role";
				$db_raid->set_query('insert', $sql, __FILE__, __LINE__);
			} else {
				$sql["UPDATE"] = "role";
				$sql["WHERE"] = "role_id={$id}";
				$db_raid->set_query('update', $sql, __FILE__, __LINE__);
			}
			pRedirect('index.php?option='.$option);
		} else {
			// error, redraw the form
			$p->assign($_POST);
			$p->display(RAIDER_TEMPLATE_PATH.'roles_form.tpl');
		}
	}
} else if($task == 'delete') {
	for($i = 0; $i < count($_POST['select']); $i++) {
		$sql['DELETE'] = 'role';
		$sql['WHERE'] = 'role_id='.intval($_POST['select'][$i]);
		$db_raid->set_query('delete', $sql, __FILE__, __LINE__);
	}

	pRedirect('index.php?option='.$option);
} else {
	printError($pLang['invalidOption']);
}
?>