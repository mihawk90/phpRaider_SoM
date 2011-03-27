<?php
error_reporting('NONE');

// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// load footer?
$load_footer = 1;

// report for output
include(RAIDER_CLASS_PATH.'report'.DIRECTORY_SEPARATOR.'report.php');
$report = &new ReportList;

// verify permissions
if(!$pMain->checkPerm('edit_raids_any') && !$pMain->checkPerm('edit_raids_own')) {
	pRedirect('index.php?option=com_login&task=login');
}

if(empty($task) || $task == '') {
	if(!$pMain->checkPerm('edit_raids_any')) {
		$profile = $pMain->getProfileID();
	}

	$new = getRaidList(0, $profile);
	$old = getRaidList(1, $profile);

	// report setup
	setupOutput();

	// paging and sorting
	$report->showRecordCount(true);
	$report->allowPaging(true, $_SERVER['PHP_SELF'].'?option='.$option.'&amp;Base=');
	$report->setListRange((empty($_GET['Base'])?'0':$_GET['Base']), $pConfig['report_max']);
	$report->allowLink(ALLOW_HOVER_INDEX, '', array());
	$report->allowSort(true, $_GET['Sort'], $_GET['SortDescending'], 'index.php?option='.$option);

	// setup column headers
	$report->addOutputColumn('info', '', null, 'left');
	$report->addOutputColumn('location', $pLang['location'], null, 'center');
	$report->addOutputColumn('date', $pLang['date'], null, 'center' );
	$report->addOutputColumn('invite_time', $pLang['invite_time'], null, 'center');
	$report->addOutputColumn('start_time', $pLang['start_time'], null, 'center');
	$report->addOutputColumn('officer', $pLang['officer'], null, 'center');
	$report->addOutputColumn('checkbox', '<input type="checkbox" onClick="invertAll(this, this.form);" />', 'null', 'right');

	// put data into variable for output
	$new = $report->getListFromArray($new);
	$old = $report->getListFromArray($old);

	$p->assign(
		array(
			'new_header' => $pLang['raNew_header'],
			'old_header' => $pLang['raOld_header'],
			'new' => $new,
			'old' => $old
		)
	);
	$p->display(RAIDER_TEMPLATE_PATH.'raids.tpl');
} else if($task == 'new' || $task == 'edit') {
	if($pMain->checkPerm('edit_raids_any') || (($task == 'new' && $pMain->checkPerm('edit_raids_own')) || ($task == 'edit' && $pMain->checkPerm('edit_raids_own') && ($pMain->getProfileID() == getProfileFromTable('raid', 'raid_id', $id ))))) {
		// First day of the week
		$p->assign('firstDayOfWeek', (empty($pConfig['first_day_of_week'])?0:$pConfig['first_day_of_week']));

		if (isset($_GET['template'])) {
			$template = intval($_GET['template']);
		}

		// localizations
		$p->assign(
			array(
				'generic_header' => $pLang['raGeneric_header'],
				'limits_header' => $pLang['raLimits_header'],

				// errors
				'locationError' => $pLang['raLocation_error'],
				'dateError' => $pLang['raDate_error'],
				'descriptionError' => $pLang['raDescription_error'],
				'minError' => $pLang['raMin_error'],
				'maxError' => $pLang['raMax_error'],
				'maximumError' => $pLang['raMaximum_error'],

				// text
				'templateText' => $pLang['raTemplate_text'],
				'locationText' => $pLang['raLocation_text'],
				'dateText' => $pLang['raDate_text'],
				'inviteText' => $pLang['raInvite_text'],
				'startText' => $pLang['raStart_text'],
				'freezeText' => $pLang['raFreeze_text'],
				'descriptionText' => $pLang['raDescription_text'],
				'iconText' => $pLang['raIcon_text'],
				'minText' => $pLang['raMin_text'],
				'maxText' => $pLang['raMax_text'],
				'raidersText' => $pLang['raRaiders_text'],
				'showIconText' => $pLang['raShowIcon_text'],
				'showNameText' => $pLang['raShowName_text'],
				'saveText' => $pLang['raSave_text'],

				// buttons
				'reset' => $pLang['reset'],
				'submit' => $pLang['submit']
			)
		);

		// valid icons
		if($dh = opendir(RAIDER_GAME_PATH."images".DIRECTORY_SEPARATOR."raids".DIRECTORY_SEPARATOR)) {
			while(false != ($filename = readdir($dh))) {
				$files[] = $filename;
			}
			closedir($dh);

			sort($files);
			array_shift($files);
			array_shift($files);
		}

		// assign task
		if($task == 'edit')
			$p->assign('task', "edit&amp;id={$id}");
		else
			$p->assign('task', "new");

		if(empty($_POST)) {
			// new form, we (re)set the session data
			SmartyValidate::connect($p, true);

			// set old values (if needed)
			unset($sql);
			if(!empty($template)) {
				$sql['SELECT'] = '*';
				$sql['FROM'] = 'raid_templates';
				$sql['WHERE'] = 'raid_template_id='.$template;
				$db_raid->set_query('select', $sql, __FILE__, __LINE__);

				$old_data = $db_raid->fetch();
			} else if($task == 'edit') {
				$sql['SELECT'] = '*';
				$sql['FROM'] = 'raid';
				$sql['WHERE'] = 'raid_id='.$id;
				$db_raid->set_query('select', $sql, __FILE__, __LINE__);

				$old_data = $db_raid->fetch();
			} else {
				$old_data = array(
						'minimum_level' => $pConfig['min_level'],
						'maximum_level' => $pConfig['max_level'],
						'maximum' => $pConfig['min_raiders']
				);
			}
			$p->assign($old_data);

			if(isset($old_data['invite_time']) && isset($old_data['start_time'])) {
				// setup time defaults
				if ($task =='edit') {
					$p->assign('invite_time_hour', gmdate("H", $old_data['invite_time']));
					$p->assign('invite_time_minute', gmdate("i", $old_data['invite_time']));
					$p->assign('start_time_hour', gmdate("H", $old_data['start_time']));
					$p->assign('start_time_minute', gmdate("i", $old_data['start_time']));
					$p->assign('date', gmdate("m/d/Y", $old_data['start_time']));
				} else {
					$p->assign('start_time_hour', substr($old_data['start_time'],0,2));
					$p->assign('start_time_minute', substr($old_data['start_time'],-2));

					if (preg_match('/^(\d{2})(\d{2})$/',$old_data['invite_time'],$matches)) {
						$p->assign('invite_time_hour', $matches[1]);
						$p->assign('invite_time_minute', $matches[2]);
					}
					if (preg_match('/^(\d{2})(\d{2})$/',$old_data['start_time'],$matches)) {
						$p->assign('start_time_hour', $matches[1]);
						$p->assign('start_time_minute', $matches[2]);
					}
				}
			}

			// template options
			unset($sql);
			$sql['SELECT'] = '*';
			$sql['FROM'] = 'raid_templates';
			$sql['SORT'] = 'location';
			$db_raid->set_query('select', $sql, __FILE__, __LINE__);

			$template_options = '<option value="">'.$pLang['select_one'].'</option>';
			while($data = $db_raid->fetch()) {
				$template_options .= "<option value=\"index.php?option=com_raids&amp;task=new&amp;template={$data['raid_template_id']}\">{$data['raid_template_id']} - {$data['location']}</option>\n";
			}

			$p->assign('templates', $template_options);

			// icons
			if (is_array($files)) {
				$icons = '';

				foreach($files as $value) {
					if($value == $old_data['icon_name'])
						$selected = ' selected';
					else
						$selected = '';

					$icons .= '<option value="'.$value.'"'.$selected.'>'.str_replace(strchr($value, '.'), "", $value).'</option>';
				}

				$p->assign('icons', $icons);

				unset($files);
			} else {
				printError($pLang['raIconError']);
			}

			// get role data
			unset($sql);
			if(isset($template)) {
				$sql['SELECT'] = 'r.role_id, r.role_name, l.raid_limit';
				$sql['JOIN'] = array(
									array('TYPE'=>'LEFT','TABLE'=>'raid_templates_limits l','CONDITION'=>'r.role_id=l.role_id AND l.raid_id='.$template)
								);
			} else if($task == 'new') {
				$sql['SELECT'] = 'r.role_id, r.role_name';
			} else if($task == 'edit') {
				$sql['SELECT'] = 'r.role_id, r.role_name, l.raid_limit';
				$sql['JOIN'] = array(
									array('TYPE'=>'LEFT','TABLE'=>'raid_limits l','CONDITION'=>'r.role_id=l.role_id AND l.raid_id='.$id)
				);
			}
			$sql['FROM'] = 'role r';
			$sql['SORT'] = 'r.role_name';

			$db_raid->set_query('select', $sql, __FILE__, __LINE__);

			// role limit array
			$l_data = array();

			while($data = $db_raid->fetch()) {
				if(isset($template) || $task == 'edit')
					$default = $data['raid_limit'];
				else
					$default = '0';

				array_push($l_data,
					array(
						'text' => $data['role_name'],
						'name' => $data['role_id'].'_limit',
						'field' => "<input name=\"{$data['role_id']}_limit\" style=\"width:30px\" type=\"text\" class=\"post\" value=\"{$default}\">",
						'errortext' => sprintf($pLang['atNumeric_error_text'],$data['att_name']))
				);
				SmartyValidate::register_validator($data['role_id'].'_limit', $data['role_id'].'_limit', 'isNumber', true, false, 'trim');
			}

			// assign limits
			$p->assign('l_data', $l_data);

			// register our validators
			SmartyValidate::register_validator('location', 'location', 'notEmpty', false, false, 'trim');
			SmartyValidate::register_validator('date', 'date', 'isDate', false, false, 'trim');
			SmartyValidate::register_validator('description', 'description', 'notEmpty', false, false, 'trim');
			SmartyValidate::register_validator('minimum_level', 'minimum_level', 'isInt', false, false, 'trim');
			SmartyValidate::register_validator('maximum_level', 'maximum_level', 'isInt', false, false, 'trim');
			SmartyValidate::register_validator('maximum', 'maximum', 'isInt', false, false, 'trim');
			if (!empty($pConfig['min_raiders']) && !empty($pConfig['max_raiders'])) {
				SmartyValidate::register_validator('maximum', sprintf('maximum:%u:%u',intval($pConfig['min_raiders']),intval($pConfig['max_raiders'])), 'isRange');
			}
			if (!empty($pConfig['min_level']) && !empty($pConfig['max_level'])) {
				SmartyValidate::register_validator('minimum_level', sprintf('minimum_level:%u:%u',intval($pConfig['min_level']),intval($pConfig['max_level'])), 'isRange');
				SmartyValidate::register_validator('maximum_level', sprintf('maximum_level:%u:%u',intval($pConfig['min_level']),intval($pConfig['max_level'])), 'isRange');
			}

			// display form
			$p->display(RAIDER_TEMPLATE_PATH.'raids_form_limits.tpl');
			$p->display(RAIDER_TEMPLATE_PATH.'raids_form.tpl');
		} else {
			// validate after a POST
			SmartyValidate::connect($p);

			if(SmartyValidate::is_valid($_POST)) {
				// updating information so clear cache
				$p->clear_cache(RAIDER_TEMPLATE_PATH.'raids.tpl');

				// no errors, done with SmartyValidate
				SmartyValidate::disconnect();

				// setup the times
				$date = explode("/", $_POST['date']);

				$start_time = gmmktime($_POST['start_time_hour'], $_POST['start_time_minute'], '0', $date[0], $date[1], $date[2]);
				$invite_time = gmmktime($_POST['invite_time_hour'], $_POST['invite_time_minute'], '0', $date[0], $date[1], $date[2]);

				// check for template
				if(isset($_POST['save'])) {
					// save template information
					$sql['INSERT'] = 'raid_templates';
					$sql['VALUES'] = array(
										'location'=>$_POST['location'],
										'maximum'=>$_POST['maximum'],
										'description'=>$_POST['description'],
										'start_time'=>$_POST['start_time_hour'].$_POST['start_time_minute'],
										'invite_time'=>$_POST['invite_time_hour'].$_POST['invite_time_minute'],
										'icon_name'=>$_POST['icon_name'],
										'freeze_time'=>$_POST['freeze_time'],
										'maximum_level'=>$_POST['maximum_level'],
										'minimum_level'=>$_POST['minimum_level']
									);
					$db_raid->set_query('insert', $sql, __FILE__, __LINE__);

					// save role template information
					$raid_id = $db_raid->sql_nextid();

					// role limits
					$pRoles = getIds('role');

					foreach($pRoles as $value) {
						if(is_numeric($_POST[$value.'_limit']) && $_POST[$value.'_limit'] >= 0) {
							$limit = $_POST[$value.'_limit'];

							$sql['INSERT'] = 'raid_templates_limits';
							$sql['VALUES'] = array(
												'raid_id' => $raid_id,
												'role_id' => $value,
												'raid_limit' => $limit
											);
							$db_raid->set_query('insert', $sql, __FILE__, __LINE__);
						}
					}
				}

				// update/insert into database
				if($task == 'new') {
					// new submission
					$sql['INSERT'] = 'raid';
					$sql['VALUES'] = array(
										'expired' => 0,
										'location' => $_POST['location'],
										'raid_leader' => $pMain->getUser(),
										'invite_time' => $invite_time,
										'maximum' => $_POST['maximum'],
										'description' => $_POST['description'],
										'start_time' => $start_time,
										'freeze_time' => $_POST['freeze_time'],
										'maximum_level' => $_POST['maximum_level'],
										'minimum_level' => $_POST['minimum_level'],
										'icon_name' => $_POST['icon_name'],
										'profile_id' => $pMain->getProfileID()
									);
					$db_raid->set_query('insert', $sql, __FILE__, __LINE__);
					$raid_id = $db_raid->sql_nextid();

					// role limits
					$pRoles = getIds('role');

					foreach($pRoles as $value) {
						if(is_numeric($_POST[$value.'_limit']) && $_POST[$value.'_limit'] >= 0){
							$limit = $_POST[$value.'_limit'];

							$sql['INSERT'] = 'raid_limits';
							$sql['VALUES'] = array(
											'raid_id' => $raid_id,
											'role_id' => $value,
											'raid_limit' => $limit
										);
							$db_raid->set_query('insert', $sql, __FILE__, __LINE__);
						}
					}
				} else {
					// edit
					$sql['UPDATE'] = 'raid';
					$sql['VALUES'] = array(
										'expired' => 0,
										'location' => $_POST['location'],
										'invite_time' => $invite_time,
										'maximum' => $_POST['maximum'],
										'description' => $_POST['description'],
										'start_time' => $start_time,
										'freeze_time' => $_POST['freeze_time'],
										'maximum_level' => $_POST['maximum_level'],
										'minimum_level' => $_POST['minimum_level'],
										'icon_name' => $_POST['icon_name']
									);
					$sql['VALUES']['expired'] = ((isExpired($sql['VALUES']))?1:0);
					$sql['WHERE'] = 'raid_id='.$id;
					$db_raid->set_query('update', $sql, __FILE__, __LINE__);

					// remove old class limits
					$sql['DELETE'] = 'raid_limits';
					$sql['WHERE'] = 'raid_id='.$id;
					$db_raid->set_query('delete', $sql, __FILE__, __LINE__);

					// new class limits
					$pRoles = getIds('role');

					foreach($pRoles as $value) {
						if(is_numeric($_POST[$value.'_limit']) && $_POST[$value.'_limit'] >= 0) {
							$limit = $_POST[$value.'_limit'];

							$sql['INSERT'] = 'raid_limits';
							$sql['VALUES'] = array('raid_id'=>$id, 'role_id'=>$value, 'raid_limit'=>$limit);
							$db_raid->set_query('insert', $sql, __FILE__, __LINE__);
						}
					}
				}
				pRedirect('index.php?option='.$option);
			} else {
				// error, redraw the form
				$p->assign($_POST);

				// role limits
				unset($sql);
				$sql['SELECT'] = 'role_id, role_name';
				$sql['FROM'] = 'role';
				$sql['SORT'] = 'role_name';

				$db_raid->set_query('select', $sql, __FILE__, __LINE__);

				$l_data = array();

				while($data = $db_raid->fetch()) {
					array_push($l_data,
						array(
							'text' => $data['role_name'],
							'name' => $data['role_id'].'_limit',
							'field' => '<input name="'.$data['role_id'].'_limit" style="width:30px" type="text" class="post" value="'.$_POST[$data['role_id'].'_limit'].'">',
							'errortext' => sprintf($pLang['atNumeric_error_text'],$data['att_name'])
						)
					);
				}

				// icons
				$icons = '';
				foreach($files as $value) {
					if($_POST['icon_name'] == $value)
						$icons .= '<option value="'.$value.'" selected>'.$value.'</option>';
					else
						$icons .= '<option value="'.$value.'">'.$value.'</option>';
				}
				$p->assign('icons', $icons);

				// assign the data
				$p->assign('l_data', $l_data);

				// redraw
				$p->display(RAIDER_TEMPLATE_PATH.'raids_form_limits.tpl');
				$p->display(RAIDER_TEMPLATE_PATH.'raids_form.tpl');
			}
		}
	} else {
		pRedirect('index.php?option=com_login&task=login');
	}
} else if($task == 'delete') {
	for($i = 0; $i < count($_POST['select']); $i++) {
		// verify permissions
		if($pMain->checkPerm('edit_raids_any') || ($pMain->checkPerm('edit_raids_own') && ($pMain->getProfileID() == getProfileFromTable('raid', 'raid_id', $_POST['select'][$i])))) {
			// raid
			$sql['DELETE'] = 'raid';
			$sql['WHERE'] = 'raid_id='.intval($_POST['select'][$i]);
			$db_raid->set_query('delete', $sql, __FILE__, __LINE__);

			// signups
			$sql['DELETE'] = 'signups';
			$sql['WHERE'] = 'raid_id='.intval($_POST['select'][$i]);
			$db_raid->set_query('delete', $sql, __FILE__, __LINE__);

			// limits
			$sql['DELETE'] = 'raid_limits';
			$sql['WHERE'] = 'raid_id='.intval($_POST['select'][$i]);
			$db_raid->set_query('delete', $sql, __FILE__, __LINE__);
		} else {
			pRedirect('index.php?option=com_login&task=login');
		}
	}
	pRedirect('index.php?option='.$option);
} else {
	printError($pLang['invalidOption']);
}
?>