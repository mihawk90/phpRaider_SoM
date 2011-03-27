<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// load footer?
$load_footer = 1;

// verify permissions
if(!$pMain->checkPerm('edit_genders')) {
	pRedirect('index.php?option=com_login&task=login');
}

// report for output
include(RAIDER_CLASS_PATH.'report'.DIRECTORY_SEPARATOR.'report.php');
$report = &new ReportList;

if(empty($task) || $task == '') {
	// output announcements list
	$sql['SELECT'] = '*';
	$sql['FROM'] = 'gender';
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	// array for data
	$phpr_a = array();

	while($data = $db_raid->fetch()) {
		// admin options
		$admin = '<a href="index.php?option='.$option.'&task=edit&id='.$data['gender_id'].'">'.setIcon(_TEMPLATE_, 'icon_edit.png', $pLang['edit']).'</a> ';

		// setup array for data output
		array_push($phpr_a,
			array(
				'name' => $data['gender_name'],
				'checkbox' => $admin.' <input type="checkbox" name="select[]" value="'.$data['gender_id'].'">',
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
	$report->addOutputColumn('checkbox', '<input type="checkbox" onClick="invertAll(this, this.form);" />', 'null', 'right');

	// put data into variable for output
	$output = $report->getListFromArray($phpr_a);

	$p->assign('create_new', $pLang['create_new']);
	$p->assign('header', $pLang['gHeader']);
	$p->assign('output', $output);
	$p->display(RAIDER_TEMPLATE_PATH.'genders.tpl');
} else if($task == 'new' || $task == 'edit') {
	// no caching for this
	$p->caching = false;

	// localizations
	$p->assign(
		array(
			// errors
			'nameError' => $pLang['gName_error'],

			// text
			'header' => $pLang['gCreate_header'],
			'nameText' => $pLang['gName_text'],

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
			$sql['FROM'] = 'gender';
			$sql['WHERE'] = 'gender_id='.$id;
			$db_raid->set_query('select', $sql, __FILE__, __LINE__);
			$p->assign($db_raid->fetch());
		}

		// register our validators
		SmartyValidate::register_validator('name', 'gender_name', 'notEmpty', false, false, 'trim');

		// display form
		$p->display(RAIDER_TEMPLATE_PATH.'genders_form.tpl');
	} else {
		// validate after a POST
		SmartyValidate::connect($p);

		if(SmartyValidate::is_valid($_POST)) {
			// updating information so clear cache
			$p->clear_cache(RAIDER_TEMPLATE_PATH.'genders.tpl');

			// no errors, done with SmartyValidate
			SmartyValidate::disconnect();

			// update/insert into database
			$sql['VALUES']=array('gender_name'=>$_POST['gender_name']);
			if($task == 'new') {
				// setup variables not submitted by form
				$sql['INSERT'] = 'gender';
				$db_raid->set_query('insert', $sql, __FILE__, __LINE__);
			} else {
				$sql['UPDATE'] = 'gender';
				$sql['WHERE'] = 'gender_id='.$id;
				$db_raid->set_query('update', $sql, __FILE__, __LINE__);
			}
			pRedirect('index.php?option='.$option);
		} else {
			// error, redraw the form
			$p->assign($_POST);
			$p->display(RAIDER_TEMPLATE_PATH.'genders_form.tpl');
		}
	}
} else if($task == 'delete') {
	for($i = 0; $i < count($_POST['select']); $i++) {
		$sql['DELETE'] = 'gender';
		$sql['WHERE'] = 'gender_id='.intval($_POST['select'][$i]);
		$db_raid->set_query('delete', $sql, __FILE__, __LINE__);
	}

	pRedirect('index.php?option='.$option);
} else {
	printError($pLang['invalidOption']);
}
?>