<?php
if(empty($option) || $option == 'com_frontpage' || $option == '') {
	$a_data = array();

	// displays announcement by calling {$plugin_announcement} in templates
	$sql['SELECT'] = 'a.*, IFNULL(p.username,\'phpRaider System\') as announcement_poster, UNIX_TIMESTAMP(a.announcement_timestamp) AS time';
	$sql['FROM'] = 'announcements a';
	$sql['JOIN'] = array('TYPE'=>'LEFT','TABLE'=>'profile p','CONDITION'=>'a.profile_id = p.profile_id');
	$sql['WHERE'] = 'a.announcement_id > 0';
	$sql['SORT'] = 'announcement_timestamp DESC';
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	while($data = $db_raid->fetch()) {
		$time = newDate($pConfig['time_format'], $data['time'], $pConfig['timezone'] + $pConfig['dst']);
		$date = newDate($pConfig['date_format'], $data['time'], $pConfig['timezone'] + $pConfig['dst']);

		// show links if they're an administrator so they can edit announcements
		if($pMain->checkPerm('edit_announcements_any') || ($pMain->checkPerm('edit_announcements_own') && $data['profile_id'] == $pMain->getProfileID())) {
			$actions = '<a href="index.php?option=com_announcements&amp;task=edit&amp;id='.$data['announcement_id'].'"><img src="templates/'.$pConfig['template'].
						'/images/icons/icon_edit.png" border="0" onMouseover="ddrivetip(\''.$pLang['edit'].'\');" onMouseout="hideddrivetip()" alt="'.$pLang['edit'].'"></a> ';
		} else {
			$actions = '';
		}

		array_push($a_data,
			array(
				'author'=>$data['announcement_poster'],
				'date'=>$date,
				'time'=>$time,
				'message'=>$data['announcement_msg'],
				'title'=>$data['announcement_title'],
				'actions'=>$actions
			)
		);
	}

	if(!empty($a_data)) {
		$p->assign('a_data', $a_data);
		$p->display(RAIDER_TEMPLATE_PATH.'plugins/plugin_announcements/announcements.tpl');
	}
}
?>