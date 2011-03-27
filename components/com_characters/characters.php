<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// load footer?
$load_footer = 1;

// verify permissions
if(!$pMain->checkPerm('edit_characters_own') && !$pMain->checkPerm('edit_characters_any')) {
	pRedirect('index.php?option=com_login&amp;task=login');
}

// report for output
include(RAIDER_CLASS_PATH.'report'.DIRECTORY_SEPARATOR.'report.php');
$report = &new ReportList;

if(empty($task) || $task == '') {
	$char_data = getCharacterData($pMain, $pMain->getProfileID());

	// report setup
	setupOutput();

	// paging and sorting
	$report->showRecordCount(true);
	$report->allowPaging(true, $_SERVER['PHP_SELF'].'?option='.$option.'&amp;Base=');
	$report->setListRange((empty($_GET['Base'])?'0':$_GET['Base']), $pConfig['report_max']);
	$report->allowLink('ALLOW_HOVER_INDEX', '', array());
	$report->allowSort(true, $_GET['Sort'], $_GET['SortDescending'], 'index.php?option='.$option);

	// setup column headers
	$report->addOutputColumn('name', $pLang['name'], 'null', 'left', null, '__NOLINK__');
	$report->addOutputColumn('race', $pLang['race'], 'null', 'center', null, '__NOLINK__');
	$report->addOutputColumn('class', $pLang['class'], 'null', 'center', null, '__NOLINK__');
	$report->addOutputColumn('level', $pLang['level'], 'null', 'center', null, '__NOLINK__');
	$report->addOutputColumn('guild', $pLang['guild'], 'null', 'center', null, '__NOLINK__');

	// setup attribute columns
	$sql['SELECT'] = 'att_name, att_icon, att_show';
	$sql['FROM'] = 'attribute';
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);
	while($data = $db_raid->fetch()) {
		if($data['att_show']) {
			$icon = '<img src="games/'.$pConfig['game'].'/images/attributes/'.urlencode($data['att_icon']).'" onMouseover="ddrivetip(\''.$data['att_name'].'\')"; onMouseout="hideddrivetip()" height="18" width="18">';
			$report->addOutputColumn($data['att_name'], $icon, 'null', 'center');
		}
	}

	$report->addOutputColumn('checkbox', '<input type="checkbox" onClick="invertAll(this, this.form);" />', 'null', 'right');

	// put data into variable for output
	$output = $report->getListFromArray($char_data);

	$p->assign('create_new', $pLang['create_new']);
	$p->assign('header', $pLang['chHeader']);
	$p->assign('output', $output);
	$p->display(RAIDER_TEMPLATE_PATH.'characters.tpl');
} else if($task == 'new' || $task == 'edit') {
	// verify permissions
	if($task == 'new') {
		if(!$pMain->checkPerm('edit_characters_any') && !$pMain->checkPerm('edit_characters_own')) {
			pRedirect('index.php?option=com_login&amp;task=login');
		}
	} else {
		if(!$pMain->checkPerm('edit_characters_any')) {
			if(!$pMain->checkPerm('edit_characters_own') || $pMain->getProfileID() != getProfileFromTable('character', 'character_id', $id)) {
				pRedirect('index.php?option=com_login&amp;task=login');
			}
		}
	}

	// no caching for this
	$p->caching = false;

	// localizations
	$p->assign(
		array(
			// errors
			'nameError' => $pLang['chName_error'],
			'raceError' => $pLang['chRace_error'],
			'classError' => $pLang['chClass_error'],

			// text
			'header' => $pLang['chCreate_header'],
			'attribute_header' => $pLang['chAttribute_header'],
			'nameText' => $pLang['chName_text'],
			'raceText' => $pLang['chRace_text'],
			'classText' => $pLang['chClass_text'],
			'genderText' => $pLang['chGender_text'],
			'guildText' => $pLang['chGuild_text'],
			'levelText' => $pLang['chLevel_text'],

			// errors
			'nameError' => $pLang['chName_error'],
			'levelError' => $pLang['chLevel_error'],

			// buttons
			'reset' => $pLang['reset'],
			'submit' => $pLang['submit']
		)
	);

	// assign task
	if($task == 'edit')
		$p->assign('task', $task.'&amp;id='.$id);
	else
		$p->assign('task' , $task);

	// set default values
	if($task == 'edit' && empty($_POST)) {
		unset($sql);
		$sql['SELECT'] = '*';
		$sql['FROM'] = 'character';
		$sql['WHERE'] = 'character_id='. $id;
		$db_raid->set_query('select', $sql, __FILE__, __LINE__);
		$old_data = $db_raid->fetch();
		$p->assign($old_data);

		$race_default = $old_data['race_id'];
		$class_default = $old_data['class_id'];
		$guild_default = $old_data['guild_id'];
		$gender_default = $old_data['gender_id'];
	} else {
		if (!empty($_POST['class_id'])) {
			$class_default = intval($_POST['class_id']);
		} else {
			$class_default = 0;
		}
		$race_default = intval($_POST['race_id']);
		$class_default = intval($_POST['class_id']);
		$guild_default = intval($_POST['guild_id']);
		$gender_default = intval($_POST['gender_id']);
	}

	// setup guilds
	$pGuilds = getData('guild');

	$guilds = '<option value="0">'.$pLang['empty'].'</option>';
	// do empty check
	if(!empty($pGuilds)) {
		foreach($pGuilds as $key => $value) {
			if($key == $guild_default) {
				$selected = ' selected';
			} else {
				$selected = '';
			}
			$guilds .= '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		}
	}

	$p->assign('guilds', $guilds);

	// setup genders
	$pGenders = getData('gender');

	$genders = '';

	if(empty($pGenders)) {
		$genders .= '<option value="0">'.$pLang['empty'].'</option>';
	} else {
		foreach($pGenders as $key => $value) {
			if($key == $gender_default) {
				$selected = ' selected';
			} else {
				$selected = '';
			}
			$genders .= '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		}
	}

	$p->assign('genders', $genders);

	// setup races
	$pRaces = getData('race');
	$pClasses = getData('class');

	// setup races
	$races = '<option value="">'.$pLang['default_race'].'</option>';
	foreach($pRaces as $key => $value) {
		if($key == $race_default) {
			$selected = ' selected';
		} else {
			$selected = '';
		}
		$races .= '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
	}

	// get restrictions from db and setup javascript
	$scripts = "\n<script language=\"javascript\" type=\"text/javascript\">\n
				var list = new Array();
				var divName = 'subClass';
				var divNumber = 'subNumber';
				var formName = 'character_new';\n\n

				list[0] = new Array(
					'".$pLang['default_class']."',
					'0',
					'false');\n\n";
	$sql['SELECT'] = 'd.race_id, d.class_id, c.class_name, r.race_name';
	$sql['FROM'] = array('definitions d', 'class c', 'race r');
	$sql['WHERE'] = 'd.race_id = r.race_id
					AND d.class_id = c.class_id';
	$sql['SORT'] = 'd.race_id, c.class_name';
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);
	while($data = $db_raid->fetch()) {
		if (!isset($temp[$data['race_id']]['name'])) {
			$temp[$data['race_id']]['name'] = $data['race_name'];
		}
		$temp[$data['race_id']]['classes'][$data['class_id']] = $data['class_name'];
	}

	// finish script setup
	if(!empty($temp)) {
		$count = 1;
		foreach($temp as $race_id => $array) {
			$scripts .= "list[$race_id] = new Array(\n";
			foreach($array['classes'] as $class_id => $class_name) {
				if($class_id == $class_default)
					$selected = 'true';
				else
					$selected = 'false';

				$scripts .= "'$class_name',\n'$class_id',\n'$selected',\n";
			}
			$scripts = substr($scripts, 0, strlen($scripts) - 2).");\n\n";
			$count++;
		}
	}

	$scripts .= "</script>";

	$p->assign('scripts', $scripts);
	$p->assign('races', $races);

	// allow dynamic adding of class forms
	if($pConfig['multi_class']) {
		$p->assign('addClassText', '<a href="javascript:;" onclick="addEvent();">'.$pLang['chAddClass_text'].'</a>');
	}

	if(empty($_POST)) {
		// new form, we (re)set the session data
		SmartyValidate::connect($p, true);

		$a_data = array();

		// assign old values if it's an edit
		if($task == 'edit') {
			// setup attributes
			unset($sql);
			$sql['SELECT'] = 'IFNULL(att_value,\'\') as att_value, att_name, a.attribute_id, a.att_type';
			$sql['FROM'] = 'attribute a';
			$sql['JOIN'] = array('TYPE'=>'LEFT','TABLE'=>'attribute_data d','CONDITION'=>'a.attribute_id=d.attribute_id AND d.character_id='.$id);
			$db_raid->set_query('select', $sql, __FILE__, __LINE__);

			while($data = $db_raid->fetch()) {
				array_push($a_data,
					array(
						'text' => $data['att_name'],
						'name' => 'Attribute'.$data['attribute_id'],
						'field' => '<input type="text" name="Attribute'.$data['attribute_id'].'" class="post" value="'.$data['att_value'].'">',
						'errortext' => sprintf($pLang['atNumeric_error_text'],$data['att_name'])
					)
				);
				if ($data['att_type'] == 'numeric') {
					SmartyValidate::register_validator('Attribute'.$data['attribute_id'], 'Attribute'.$data['attribute_id'], 'isNumber', true, false, 'trim');
				} else {
					SmartyValidate::register_validator('Attribute'.$data['attribute_id'], 'Attribute'.$data['attribute_id'], 'dummyValid', true, false, 'trim,strip_tags,trim');
				}
			}
		} else {
			// setup attributes
			unset($sql);
			$sql['SELECT'] = '*';
			$sql['FROM'] = 'attribute';
			$db_raid->set_query('select', $sql, __FILE__, __LINE__);
			while($data = $db_raid->fetch()) {
				array_push($a_data,
					array(
						'text' => $data['att_name'],
						'name' => 'Attribute'.$data['attribute_id'],
						'field' => '<input type="text" name="Attribute'.$data['attribute_id'].'" class="post">',
						'errortext' => sprintf($pLang['atNumeric_error_text'],$data['att_name'])
					)
				);
				if ($data['att_type'] == 'numeric') {
					SmartyValidate::register_validator('Attribute'.$data['attribute_id'], 'Attribute'.$data['attribute_id'], 'isNumber', true, false, 'trim');
				} else {
					SmartyValidate::register_validator('Attribute'.$data['attribute_id'], 'Attribute'.$data['attribute_id'], 'dummyValid', true, false, 'trim,strip_tags,trim');
				}
			}
		}

		$p->assign('a_data', $a_data);
		$p->display(RAIDER_TEMPLATE_PATH.'characters_form_attributes.tpl');

		// register our validators
		SmartyValidate::register_validator('race', 'race_id', 'isInt', false, false, 'trim');
		SmartyValidate::register_validator('class', 'class_id', 'isInt', false, false, 'trim');
		SmartyValidate::register_validator('name', 'char_name', 'notEmpty', false, false, 'trim,strip_tags,trim');
		SmartyValidate::register_validator('level', 'char_level', 'isInt', false, false, 'trim');
		if (!empty($pConfig['min_level']) && !empty($pConfig['max_level'])) {
			SmartyValidate::register_validator('level', sprintf('char_level:%u:%u',intval($pConfig['min_level']),intval($pConfig['max_level'])), 'isRange');
		}

		// display form
		$p->display(RAIDER_TEMPLATE_PATH.'characters_form.tpl');
	} else {
		// validate after a POST
		SmartyValidate::connect($p);

		if(!empty($_POST['char_name'])) {
			$exists = 0;

			if($task == 'new') {
				// check if character name exists
				unset($sql);
				$sql['SELECT'] = 'COUNT(*) AS count';
				$sql['FROM'] = 'character';
				$sql['WHERE'] = 'char_name = '.$db_raid->quote_smart($_POST['char_name']);
				$db_raid->set_query('select', $sql, __FILE__, __LINE__);
				$data = $db_raid->fetch();

				if($data['count'] > 0) {
					$exists = 1;
					$p->assign('char_exists', '<span class=formError>'.$pLang['chExists_error'].'</span');
				}
			}
		}

		if(SmartyValidate::is_valid($_POST) && $exists == 0) {
			// updating information so clear cache
			$p->clear_cache(RAIDER_TEMPLATE_PATH.'characters.tpl');

			// no errors, done with SmartyValidate
			SmartyValidate::disconnect();
			// update/insert into database
			if($task == 'new') {
				// setup variables not submitted by form
				$sql['INSERT'] = 'character';
				$sql['VALUES'] = array(
									'char_name' => $_POST['char_name'],
									'race_id' => $_POST['race_id'],
									'class_id' => $_POST['class_id'],
									'guild_id' => $_POST['guild_id'],
									'gender_id' => $_POST['gender_id'],
									'char_level' => $_POST['char_level'],
									'profile_id' => $pMain->getProfileID()
								);
				$db_raid->set_query('insert', $sql, __FILE__, __LINE__);
				$char_id = $db_raid->sql_nextid();

				if($pConfig['multi_class']) {
					// setup multiclasses
					$i = 1;

					while(!empty($_POST['subClass'.$i])) {
						$sql['INSERT'] = 'subclass';
						$sql['VALUES'] = array(
											'character_id' => $char_id,
											'class_id' => $_POST['subClass'.$i]
										);
						$db_raid->set_query('insert', $sql, __FILE__, __LINE__);
						$i++;
					}
				}
			} else {
				$sql['UPDATE'] = 'character';
				$sql['VALUES'] = array(
									'char_name' => $_POST['char_name'],
									'race_id' => $_POST['race_id'],
									'class_id' => $_POST['class_id'],
									'guild_id' => $_POST['guild_id'],
									'gender_id' => $_POST['gender_id'],
									'char_level' => $_POST['char_level']
								);
				$sql['WHERE'] = 'character_id='.$id;
				$db_raid->set_query('update', $sql, __FILE__, __LINE__);
				$char_id = $id;

				// update multiclass
				if($pConfig['multi_class']) {
					$sql['DELETE'] = 'subclass';
					$sql['WHERE'] = 'character_id='.$id;
					$db_raid->set_query('delete', $sql, __FILE__, __LINE__);

					$i = 1;

					while(!empty($_POST['subClass'.$i])) {
						$sql['INSERT'] = 'subclass';
						$sql['VALUES'] = array(
											'character_id' => $char_id,
											'class_id' => $_POST['subClass'.$i]
										);
						$db_raid->set_query('insert', $sql, __FILE__, __LINE__);
						$i++;
					}
				}

				$sql['DELETE'] = 'attribute_data';
				$sql['WHERE'] = 'character_id='.$id;
				$db_raid->set_query('delete', $sql, __FILE__, __LINE__);
			}

			// setup attribute data
			foreach($_POST as $key=>$value) {
				if (preg_match("/^attribute(\d+)$/si", $key, $key_matches)) {
					$key = $key_matches[1];

					if ($value!='') {
						$sql['INSERT'] = 'attribute_data';
						$sql['VALUES'] = array(
											'character_id' => $char_id,
											'attribute_id' => $key,
											'att_value' => $value
										);
						$db_raid->set_query('insert', $sql, __FILE__, __LINE__);
					}
				}
			}

			pRedirect('index.php?option='.$option);
		} else {
			// error, redraw the form
			$a_data = array();

			// attributes
			unset($sql);
			$sql['SELECT'] = '*';
			$sql['FROM'] = 'attribute';
			$db_raid->set_query('select', $sql, __FILE__, __LINE__);

			while($data = $db_raid->fetch()) {
				array_push($a_data,
					array(
						'text' => $data['att_name'],
						'name' => 'Attribute'.$data['attribute_id'],
						'field' => '<input type="text" name="Attribute'.$data['attribute_id'].'" class="post" value="'.$_POST['Attribute'.$data['attribute_id']].'">',
						'errortext' => sprintf($pLang['atNumeric_error_text'],$data['att_name'])
					)
				);
			}

			$p->assign('a_data', $a_data);
			$p->display(RAIDER_TEMPLATE_PATH.'characters_form_attributes.tpl');

			$p->assign($_POST);
			$p->display(RAIDER_TEMPLATE_PATH.'characters_form.tpl');
		}
	}
} else if($task == 'delete') {
	for($i = 0; $i < count($_POST['select']); $i++) {
		// verify permissions
		if($pMain->checkPerm('edit_characters_any') || ($pMain->checkPerm('edit_characters_own') && checkOwn('character', intval($_POST['select'][$i]), $pMain->getProfileID()))) {
			$sql['DELETE'] = 'character';
			$sql['WHERE'] = 'character_id='.intval($_POST['select'][$i]);
			$db_raid->set_query('delete', $sql, __FILE__, __LINE__);

			$sql['DELETE'] = 'subclass';
			$sql['WHERE'] = 'character_id='.intval($_POST['select'][$i]);
			$db_raid->set_query('delete', $sql, __FILE__, __LINE__);

			$sql['DELETE'] = 'signups';
			$sql['WHERE'] = 'character_id='.intval($_POST['select'][$i]);
			$db_raid->set_query('delete', $sql, __FILE__, __LINE__);
		}
	}

	pRedirect('index.php?option='.$option);
} else {
	printError($pLang['invalidOption']);
}
?>