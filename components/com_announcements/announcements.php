<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// load footer?
$load_footer = 1;

// report for output
include(RAIDER_CLASS_PATH.'report/report.php');
$report = &new ReportList;

if(empty($task) || $task == '') {
	// verify permissions
	if(!$pMain->checkPerm('edit_announcements_any') && !$pMain->checkPerm('edit_announcements_own')) {
		pRedirect('index.php?option=com_login&task=login');
	}

	// output announcements list
	$sql['SELECT'] = 'a.*, p.username, UNIX_TIMESTAMP(a.announcement_timestamp) AS time';
	$sql['FROM'] = 'announcements a';
	$sql['JOIN'] = array('TYPE'=>'LEFT','TABLE'=>'profile p','CONDITION'=>'a.profile_id = p.profile_id');
	$sql['WHERE'] = 'a.announcement_id > 0';
	$sql['SORT'] = 'announcement_timestamp DESC';
	if(!$pMain->checkPerm('edit_announcements_any')) {
		$sql['WHERE'] .= ' AND p.profile_id='.$pMain->getProfileID();
	}

	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	// array for data
	$phpr_a = array();

	while($data = $db_raid->fetch()) {
		// setup date and time
		$date = newDate($pConfig['date_format'],$data['time'],$pConfig['timezone'] + $pConfig['dst']);
		$time = newDate($pConfig['time_format'],$data['time'],$pConfig['timezone'] + $pConfig['dst']);

		// strip message down to basics
		$message = formatText($data['announcement_msg'],'_NOHTML_',25);

		// admin options
		$admin = '<a href="index.php?option='.$option.'&amp;task=edit&amp;id='.$data['announcement_id'].'">'.setIcon('_TEMPLATE_', 'icon_edit.png', $pLang['edit']).'</a> ';

		// setup array for data output
		array_push($phpr_a,
			array(
				'title'=>$data['announcement_title'],
				'msg'=>$message,
				'date'=>$date,
				'time'=>$time,
				'by'=>$data['username'],
				'checkbox'=>$admin.' <input type="checkbox" name="select[]" value="'.$data['announcement_id'].'">',
			)
		);
	}

	// report setup
	setupOutput();

	// paging and sorting
	$report->showRecordCount(true);
	$report->allowPaging(true,$_SERVER['PHP_SELF'].'?option='.$option.'&amp;Base=');
	$report->setListRange((empty($_GET['Base'])?'0':$_GET['Base']), $pConfig['report_max']);
	$report->allowLink('ALLOW_HOVER_INDEX','',array());
	$report->allowSort(true,$_GET['Sort'],$_GET['SortDescending'],'index.php?option='.$option);

	// setup column headers
	$report->addOutputColumn('title',$pLang['title'],'null','center');
	$report->addOutputColumn('msg',$pLang['message'],'null','center');
	$report->addOutputColumn('date',$pLang['date'],'null','center');
	$report->addOutputColumn('time',$pLang['time'],'null','center');
	$report->addOutputColumn('by',$pLang['posted_by'],'null','center');
	$report->addOutputColumn('checkbox','<input type="checkbox" onClick="invertAll(this,this.form);" />','null','right');

	// put data into variable for output
	$output = $report->getListFromArray($phpr_a);

	$p->assign('header',$pLang['aHeader']);
	$p->assign('output',$output);
	$p->display(RAIDER_TEMPLATE_PATH.'announcements.tpl');
} else if($task == 'new' || $task == 'edit') {
	// no caching for this
	$p->caching = false;

	// verify permissions
	if($task == 'new') {
		if(!$pMain->checkPerm('edit_announcements_any') && !$pMain->checkPerm('edit_announcements_own')) {
			pRedirect('index.php?option=com_login&task=login');
		}
	} else {
		if(!$pMain->checkPerm('edit_announcements_any')) {
			if(!$pMain->checkPerm('edit_announcements_own') || $pMain->getProfileID() != getProfileFromTable('announcements','announcement_id',$id)) {
				pRedirect('index.php?option=com_login&task=login');
			}
		}
	}


	// localizations
	$p->assign(
		array(
			'header' => $pLang['aCreate_header'],

			// errors
			'messageError' => $pLang['aMessage_error'],
			'titleError' => $pLang['aTitle_error'],

			// text
			'messageText' => $pLang['aMessage_text'],
			'titleText' => $pLang['aTitle_text'],

			// buttons
			'reset' => $pLang['reset'],
			'submit' => $pLang['submit']
		)
	);

	// assign task
	if($task == 'edit')
		$p->assign('task',$task.'&id='.$id);
	else
		$p->assign('task',$task);

	if(empty($_POST)) {
		// new form,we (re)set the session data
		SmartyValidate::connect($p,true);

		// assign old values if it's an edit
		if($task == 'edit') {
			$sql['SELECT'] = '*';
			$sql['FROM'] = 'announcements';
			$sql['WHERE'] = 'announcement_id='.$id;
			$db_raid->set_query('select', $sql, __FILE__, __LINE__);
			$p->assign($db_raid->fetch());
		}

		// register our validators
		SmartyValidate::register_validator('title','announcement_title','notEmpty',false,false,'trim');
		SmartyValidate::register_validator('message','announcement_msg','notEmpty',false,false,'trim');

		// display form
		$p->display(RAIDER_TEMPLATE_PATH.'announcements_form.tpl');
	} else {
		// validate after a POST
		SmartyValidate::connect($p);

		if(SmartyValidate::is_valid($_POST)) {
			// updating information so clear cache
			$p->clear_cache(RAIDER_TEMPLATE_PATH.'announcements.tpl');

			// no errors,done with SmartyValidate
			SmartyValidate::disconnect();

			// update/insert into database
			$sql['VALUES'] = array(
				'announcement_title'=>$_POST['announcement_title'],
				'announcement_msg'=>$_POST['announcement_msg']
			);
			if($task == 'new') {
				// setup variables not submitted by form
				$sql['VALUES']['profile_id'] = $pMain->getProfileID();
				$sql['INSERT'] = 'announcements';
				$db_raid->set_query('insert', $sql, __FILE__, __LINE__);
			} else {
				$sql['UPDATE'] = 'announcements';
				$sql['WHERE'] = 'announcement_id='.$id;
				$db_raid->set_query('update', $sql, __FILE__, __LINE__);
			}
			pRedirect('index.php?option='.$option);
		} else {
			// error,redraw the form
			$p->assign($_POST);
			$p->display(RAIDER_TEMPLATE_PATH.'announcements_form.tpl');
		}
	}
} else if($task == 'delete') {
// verify permissions
	for($i = 0; $i < count($_POST['select']); $i++) {
		if($pMain->checkPerm('edit_announcements_any') || ($pMain->checkPerm('edit_announcements_own') && $pMain->getProfileID() == getProfileFromTable('announcements','announcement_id',intval($_POST['select'][$i])))) {
			$sql['DELETE'] = 'announcements';
			$sql['WHERE'] = 'announcement_id='.intval($_POST['select'][$i]);
			$db_raid->set_query('delete', $sql, __FILE__, __LINE__);
		}
	}
	pRedirect('index.php?option='.$option);
} else {
	printError($pLang['invalidOption']);
}
?>