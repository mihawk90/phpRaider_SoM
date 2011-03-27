<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// load footer?
$load_footer = 1;

// verify permissions
if(!$pMain->checkPerm('edit_definitions')) {
	pRedirect('index.php?option=com_login&task=login');
}

// setup mode if necessary
isset($_GET['mode']) ? $mode = trim(htmlspecialchars($_GET['mode'])) : $mode = '';
$p->assign('mode', $mode);

// report for output
include(RAIDER_CLASS_PATH.'report'.DIRECTORY_SEPARATOR.'report.php');
$report = &new ReportList;

if(empty($task) || $task == '') {
	// setup the output
	$classes = array();
	$races = array();
	$definitions = array();

	$sql['SELECT'] = '*';
	$sql['FROM'] = 'race';
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	while($data = $db_raid->fetch()) {
		// admin options
		$admin = '<a href="index.php?option='.$option.'&amp;task=edit&amp;mode=race&amp;id='.$data['race_id'].'">'.
					setIcon('_TEMPLATE_', 'icon_edit.png', $pLang['edit']).'</a> ';

		// setup array for data output
		array_push($races,
			array(
				'name' => "<a href=\"javascript:void(0)\" onmouseover=\"ajax_showTooltip('index.php?option=com_definitions&amp;task=ajax&amp;mode=race&amp;id={$data['race_id']}',this); return false\"
							 onmouseout=\"ajax_hideTooltip()\">{$data['race_name']}</a>",
				'admin' => $admin,
				'checkbox' => $admin.' <input type="checkbox" name="select[]" value="'.$data['race_id'].'">',
			)
		);
	}

	unset($sql);
	$sql['SELECT'] = '*';
	$sql['FROM'] = 'class';
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	while($data = $db_raid->fetch()) {
		// admin options
		$admin = '<a href="index.php?option='.$option.'&amp;task=edit&amp;mode=class&amp;id='.$data['class_id'].'">'.setIcon('_TEMPLATE_', 'icon_edit.png', $pLang['edit']).'</a> ';

		// setup array for data output
		array_push($classes,
			array(
				'name' => "<a href=\"javascript:void(0)\" onmouseover=\"ajax_showTooltip('index.php?option=com_definitions&amp;task=ajax&amp;mode=class&amp;id={$data['class_id']}',this); return false\"
							 onmouseout=\"ajax_hideTooltip()\"><font color=\"{$data['class_color']}\">{$data['class_name']}</font></a>",
				'checkbox' => $admin.' <input type="checkbox" name="select[]" value="'.$data['class_id'].'">',
			)
		);
	}

	// report setup
	setupOutput();

	// paging and sorting
	$report->showRecordCount(true);
	$report->allowPaging(true, $_SERVER['PHP_SELF'].'?mode=view&Base=');
	$report->setListRange((empty($_GET['Base'])?'0':$_GET['Base']), $pConfig['report_max']);
	$report->allowLink(ALLOW_HOVER_INDEX, '', array());
	$report->allowSort(true, $_GET['Sort'], $_GET['SortDescending'], 'index.php?option='.$option);

	// setup column headers
	$report->addOutputColumn('name', $pLang['name'], 'null', 'left');
	$report->addOutputColumn('checkbox', '<input type="checkbox" onClick="invertAll(this, this.form);" />', 'null', 'right');

	// put data into variable for output
	$races = $report->getListFromArray($races);
	$classes = $report->getListFromArray($classes);

	$p->assign(
		array(
			// headers
			'classes_header' => $pLang['deClass_header'],
			'races_header' => $pLang['deRace_header'],
			'upload_new' => $pLang['deUpload_new'],

			// other
			'create_new' => $pLang['create_new'],
			'upload' => $pLang['upload'],

			// data
			'classes' => $classes,
			'races' => $races,
		)
	);

	$p->display(RAIDER_TEMPLATE_PATH.'definitions.tpl');
} else if($task == 'new' || $task == 'edit') {
	// no caching for this
	$p->caching = false;

	// localizations
	$p->assign(
		array(
			// error
			'raceNameError' => $pLang['deRaceName_error'],
			'classNameError' => $pLang['deClassName_error'],
			'colorError' => $pLang['deColor_error'],

			// text
			'raceHeader' => $pLang['deRace_header'],
			'classHeader' => $pLang['deClass_header'],
			'raceNameText' => $pLang['deRaceName_text'],
			'classNameText' => $pLang['deClassName_text'],
			'colorText' => $pLang['deColor_text'],
			'restrictionsText' => $pLang['deRestrictions_text'],

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

	// set up restriction lists
	unset($sql);
	$sql['SELECT'] = '*';
	switch($mode) {
		case 'class':
			$sql['FROM'] = 'race';
			$type = 'race';
			break;
		case 'race':
			$sql['FROM'] = 'class';
			$type = 'class';
			break;
	}

	// setup defaults
	if($task == 'new') {
		if(isset($_POST['restrictions'])) {
			foreach($_POST['restrictions'] as $key => $value) {
				$selected[$value] = 1;
			}
		}
	} else {
		$sql2['SELECT'] = $type.'_id as id';
		$sql2['FROM'] = 'definitions';
		$sql2['WHERE'] = $mode.'_id='.intval($id);
		$result = $db_raid->set_query('select', $sql2, __FILE__, __LINE__);

		while($data = $db_raid->sql_fetchrow($result)) {
			$selected[$data['id']] = 1;
		}
	}

	// clear restrictions
	$rest_option = '';

	$result = $db_raid->set_query('select', $sql, __FILE__, __LINE__);
	while($data = $db_raid->sql_fetchrow($result)) {
		isset($selected[$data[$type.'_id']]) ? $select = ' selected' : $select = '';
		$rest_option .= '<option'.$select.' value="'.$data[$type.'_id'].'">'.$data[$type.'_name'].'</option>';
	}

	// assign restrictions to template
	$p->assign('rest_option', $rest_option);

	if(empty($_POST)) {
		// new form, we (re)set the session data
		SmartyValidate::connect($p, true);

		// assign old values if it's an edit
		if($task == 'edit') {
			unset($sql);
			$sql['SELECT'] = '*';
			$sql['FROM'] = $mode;
			$sql['WHERE'] = $mode.'_id='.intval($id);
			$result = $db_raid->set_query('select', $sql, __FILE__, __LINE__);
			$old_data = $db_raid->sql_fetchrow($result);
			$p->assign('name', $old_data[$mode.'_name']);

			if($mode == 'class')
				$p->assign('class_color', $old_data['class_color']);
		}

		// register our validators
		SmartyValidate::register_validator('name', 'name', 'notEmpty', false, false, 'trim');

		// display form
		if($mode == 'class') {
			SmartyValidate::register_validator('color', 'class_color', 'notEmpty', false, false, 'trim');
			$p->display(RAIDER_TEMPLATE_PATH.'definitions_form_class.tpl');
		} else {
			$p->display(RAIDER_TEMPLATE_PATH.'definitions_form_race.tpl');
		}
	} else {
		// validate after a POST
		SmartyValidate::connect($p);

		if(SmartyValidate::is_valid($_POST)) {
			// updating information so clear cache
			$p->clear_cache(RAIDER_TEMPLATE_PATH.'definitions.tpl');

			// no errors, done with SmartyValidate
			SmartyValidate::disconnect();

			// update/insert into database
			if($task == 'new') {
				if($mode == 'class') {
					unset($sql);
					$sql['INSERT'] = $mode;
					$sql['VALUES'] = array(
										'class_name' => $_POST['name'],
										'class_color' => $_POST['class_color']
					);
				} else {
					unset($sql);
					$sql['INSERT'] = $mode;
					$sql['VALUES'] = array(
										'race_name' => $_POST['name']
					);
				}

				$db_raid->set_query('insert', $sql, __FILE__, __LINE__);
				$temp_id = $db_raid->sql_nextid($result);
			} else {
				unset($sql);
				if($mode == 'class') {
					$sql['UPDATE'] = $mode;
					$sql['VALUES'] = array(
										'class_name' => $_POST['name'],
										'class_color' => $_POST['class_color']
					);
					$sql['WHERE'] = 'class_id='.intval($id);
				} else {
					$sql['UPDATE'] = $mode;
					$sql['VALUES'] = array(
										'race_name' => $_POST['name']
					);
					$sql['WHERE'] = 'race_id='.intval($id);
				}

				$db_raid->set_query('update', $sql, __FILE__, __LINE__);

				// delete all previous definitions
				unset($sql);
				$sql['DELETE'] = 'definitions';
				$sql['WHERE'] = $mode.'_id='.intval($id);
				$db_raid->set_query('delete', $sql, __FILE__, __LINE__);
				$temp_id = $id;
			}
			// setup the definitions
			foreach($_POST['restrictions'] as $value) {
				unset($sql);
				$sql['INSERT'] = 'definitions';

				if($mode == 'race') {
					$class_id = $value;
					$race_id = $temp_id;
				} else {
					$class_id = $temp_id;
					$race_id = $value;
				}
				$sql['VALUES'] = array(
									'class_id' => $class_id,
									'race_id' => $race_id
				);

				$db_raid->set_query('insert', $sql, __FILE__, __LINE__);
			}
			pRedirect('index.php?option='.$option);
		} else {
			// error, redraw the form
			$p->assign($_POST);

			if($mode == 'class') {
				$p->display(RAIDER_TEMPLATE_PATH.'definitions_form_class.tpl');
			} else {
				$p->display(RAIDER_TEMPLATE_PATH.'definitions_form_race.tpl');
			}
		}
	}
} else if($task == 'delete') {
	for($i = 0; $i < count($_POST['select']); $i++) {
		unset($sql);
		$sql['DELETE'] = $mode;
		$sql['WHERE'] = $mode.'_id='.intval($_POST['select'][$i]);
		$db_raid->set_query('delete', $sql, __FILE__, __LINE__);

		$sql['DELETE'] = 'definitions';
		$db_raid->set_query('delete', $sql, __FILE__, __LINE__);
	}

	pRedirect('index.php?option='.$option);
} else if($task == 'ajax') {
	ob_end_clean();

	$tooltip = '';

	$sql['FROM'] = array('definitions d');
	if($_GET['mode'] == 'race') {
		$a = 'race';
		$b = 'class';
	} else {
		$a = 'class';
		$b = 'race';
	}
	$sql['SELECT'] = $b.'_name name';
	array_push($sql['FROM'],$b.' b');
	$sql['WHERE'] = 'd.'.$b.'_id = b.'.$b.'_id';
	$sql['WHERE'] .= " AND d.{$a}_id={$id}";

	$db_raid->set_query('select', $sql, __FILE__, __LINE__);
	while($data = $db_raid->fetch()) {
		$tooltip .= "{$data['name']}<br>";
	}

	echo $tooltip;
	exit;
} else {
	printError($pLang['invalidOption']);
}
?>