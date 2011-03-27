<?php
// no direct access
defined( '_VALID_RAID') or die( 'Restricted Access');

// load footer?
$load_footer = 1;

// verify permissions
if( !$pMain->checkPerm( 'edit_attributes')) {
	pRedirect( 'index.php?option=com_login&task=login');
}

// report for output
include(RAIDER_CLASS_PATH.'report'.DIRECTORY_SEPARATOR.'report.php');
$report = &new ReportList;

if(empty($task) || $task == '') {
	// output announcements list
	$sql['SELECT'] = '*';
	$sql['FROM'] = 'attribute';
	$sql['WHERE'] = 'attribute_id>0';
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	// array for data
	$phpr_a = array();

	while($data = $db_raid->fetch()) {
		// admin options
		$admin = '<a href="index.php?option='.$option.'&amp;task=edit&amp;id='.$data['attribute_id'].'">'.setIcon('_TEMPLATE_', 'icon_edit.png', $pLang['edit']).'</a>';

		// setup clicking to toggle
		$show = '<a href="index.php?option=com_attributes&amp;task=show&amp;id='.$data['attribute_id'].'">';
		$hover = '<a href="index.php?option=com_attributes&amp;task=hover&amp;id='.$data['attribute_id'].'">';
		$data['att_show'] ? $show .= '<img src="templates/'.$pConfig['template'].'/images/icons/icon_tick.png" border="0">' :
							$show .= '<img src="templates/'.$pConfig['template'].'/images/icons/icon_cancel.png" border="0">';
		$data['att_hover'] ? $hover .= '<img src="templates/'.$pConfig['template'].'/images/icons/icon_tick.png" border="0">' :
							$hover .= '<img src="templates/'.$pConfig['template'].'/images/icons/icon_cancel.png" border="0">';
		$show .= '</a>';
		$hover .= '</a>';

		// setup the attribute icons (beautiful!)
		$icon = '<img src="games/'.$pConfig['game'].'/images/attributes/'.urlencode($data['att_icon']).'" onMouseover="ddrivetip(\''.$data['att_icon'].'\')"; onMouseout="hideddrivetip()">';

		// setup array for data output
		array_push($phpr_a,
			array(
				'name' => $data['att_name'],
				'icon' => $icon,
				'type' => $data['att_type'],
				'min' => $data['att_min'],
				'max' => $data['att_max'],
				'show' => $show,
				'hover' => $hover,
				'checkbox' => $admin.' <input type="checkbox" name="select[]" value="'.$data['attribute_id'].'">',
			)
		);
	}

	// report setup
	setupOutput();

	// paging and sorting
	$report->showRecordCount( true);
	$report->allowPaging( true, $_SERVER['PHP_SELF'].'?option='.$option.'&amp;Base=');
	$report->setListRange((empty($_GET['Base'])?'0':$_GET['Base']), $pConfig['report_max']);
	$report->allowLink( 'ALLOW_HOVER_INDEX', '', array());
	$report->allowSort( true, (empty($_GET['Sort'])?'':$_GET['Sort']), (empty($_GET['SortDescending'])?'':$_GET['SortDescending']), 'index.php?option='.$option);

	// setup column headers
	$report->addOutputColumn( 'name', $pLang['name'], 'null', 'left');
	$report->addOutputColumn( 'icon', $pLang['icon'], 'null', 'center');
	$report->addOutputColumn( 'type', $pLang['type'], 'null', 'center');
	$report->addOutputColumn( 'min', $pLang['minimum'], 'null', 'center');
	$report->addOutputColumn( 'max', $pLang['maximum'], 'null', 'center');
	$report->addOutputColumn( 'show', $pLang['show'], 'null', 'center');
	$report->addOutputColumn( 'hover', $pLang['hover'], 'null', 'center');
	$report->addOutputColumn( 'checkbox', '<input type="checkbox" onClick="invertAll(this, this.form);" />', 'null', 'right');

	// put data into variable for output
	$output = $report->getListFromArray( $phpr_a);

	$p->assign( 'create_new', $pLang['create_new']);
	$p->assign( 'header', $pLang['atHeader']);
	$p->assign( 'output', $output);
	$p->display( RAIDER_TEMPLATE_PATH.'attributes.tpl');
} else if($task == 'new' || $task == 'edit') {
	// no caching for this
	$p->caching = false;

	// localizations
	$p->assign(
		array(
			// errors
			'nameError' => $pLang['atName_error'],
			'minError' => $pLang['atMin_error'],
			'maxError' => $pLang['atMax_error'],

			// text
			'header' => $pLang['atCreate_header'],
			'nameText' => $pLang['atName_text'],
			'iconText' => $pLang['atIcon_text'],
			'typeText' => $pLang['atType_text'],
			'minText' => $pLang['atMin_text'],
			'maxText' => $pLang['atMax_text'],
			'showText' => $pLang['atShow_text'],
			'hoverText' => $pLang['atHover_text'],
			'numericText' => $pLang['atNumeric_text'],
			'textText' => $pLang['atText_text'],

			// buttons
			'reset' => $pLang['reset'],
			'submit' => $pLang['submit']
		)
	);

	// assign task
	if( $task == 'edit')
		$p->assign( 'task', $task.'&id='.$id);
	else
		$p->assign( 'task' , $task);

	if( empty( $_POST)) {
		// new form, we (re)set the session data
		SmartyValidate::connect( $p, true);

		// assign old values if it's an edit
		if( $task == 'edit') {
			$sql['SELECT'] = '*';
			$sql['FROM'] = 'attribute';
			$sql['WHERE'] = 'attribute_id='.$id;
			$db_raid->set_query('select', $sql, __FILE__, __LINE__);
			$p->assign($db_raid->fetch());
		}

		// setup attribute icons
		if ($dh = opendir(RAIDER_GAME_PATH.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'attributes')) {
			while(false != ($filename = readdir($dh))) {
				if (is_file(RAIDER_GAME_PATH.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'attributes'.DIRECTORY_SEPARATOR.$filename)) {
					$files[] = $filename;
				}
			}

			sort($files);
			//array_shift($files);
			//array_shift($files);

			// clear variable just in case
			$attributes = '';

			foreach( $files as $key => $value) {
				$attributes .= '<option value="'.$value.'">'.$value.'</option>';
			}
		} else {
			// assign empty option
			if( empty( $attributes)) {
				$attributes = '<option value="">'.$pLang['no_icons'].'</option>';
			}
		}

		// assign to template
		$p->assign('attributes', $attributes);

		// register our validators
		SmartyValidate::register_validator('name', 'att_name', 'notEmpty', false, false, 'trim');

		// display form
		$p->display(RAIDER_TEMPLATE_PATH.'attributes_form.tpl');
	} else {
		// validate after a POST
		SmartyValidate::connect( $p);

		if( SmartyValidate::is_valid($_POST)) {
			// updating information so clear cache
			$p->clear_cache(RAIDER_TEMPLATE_PATH.'attributes.tpl');

			// no errors, done with SmartyValidate
			SmartyValidate::disconnect();

			// update/insert into database
			$sql['VALUES']=array(
				'att_show'=>((!isset($_POST['att_show']))?0:(($_POST['att_show'] == 'on')?1:$_POST['att_show'])),
				'att_hover'=>((!isset($_POST['att_hover']))?0:(($_POST['att_hover'] == 'on')?1:$_POST['att_hover'])),
				'att_icon'=>$_POST['att_icon'],
				'att_name'=>$_POST['att_name'],
				'att_type'=>$_POST['att_type']
			);
			if($task == 'new') {
				$sql['INSERT'] = 'attribute';
				$db_raid->set_query('insert', $sql, __FILE__, __LINE__);
			} else {
				$sql['UPDATE'] = 'attribute';
				$sql['WHERE'] = 'attribute_id='.$id;
				$db_raid->set_query('update', $sql, __FILE__, __LINE__);
			}
			pRedirect('index.php?option='.$option);
		} else {
			// error, redraw the form
			$p->assign($_POST);
			$p->display(RAIDER_TEMPLATE_PATH.'attributes_form.tpl');
		}
	}
} else if( $task == 'delete') {
	for( $i = 0; $i < count( $_POST['select']); $i++) {
		$sql['DELETE'] = 'attribute';
		$sql['WHERE'] = 'attribute_id='.intval($_POST['select'][$i]);
		$db_raid->set_query('delete', $sql, __FILE__, __LINE__);

		$sql['DELETE'] = 'attribute_data';
		$sql['WHERE'] = 'attribute_id='.intval($_POST['select'][$i]);
		$db_raid->set_query('delete', $sql, __FILE__, __LINE__);
	}

	pRedirect( 'index.php?option='.$option);
} else if ( $task == 'show') {
	$sql['SELECT'] = 'att_show';
	$sql['FROM'] = 'attribute';
	$sql['WHERE'] = 'attribute_id='.$id;
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);
	$temp = $db_raid->fetch();

	$temp['att_show'] ? $temp = 0 : $temp = 1;

	$sql['UPDATE'] = 'attribute';
	$sql['VALUES'] = array(
						'att_show'=>$temp,
						'attribute_id'=>$id
					);
	$db_raid->set_query('update', $sql, __FILE__, __LINE__);

	pRedirect( 'index.php?option='.$option);
} else if ( $task == 'hover') {
	$sql['SELECT'] = 'att_hover';
	$sql['FROM'] = 'attribute';
	$sql['WHERE'] = 'attribute_id='.$id;
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);
	$temp = $db_raid->fetch();

	$temp['att_hover'] ? $temp = 0 : $temp = 1;

	$sql['UPDATE'] = 'attribute';
	$sql['VALUES'] = array(
						'att_hover'=>$temp,
						'attribute_id'=>$id
					);
	$db_raid->set_query('update', $sql, __FILE__, __LINE__);

	pRedirect( 'index.php?option='.$option);
} else {
	printError( $pLang['invalidOption']);
}
?>