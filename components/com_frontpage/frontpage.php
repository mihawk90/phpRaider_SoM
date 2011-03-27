<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// load footer?
$load_footer = 1;

if(empty($task) || $task == '') {
	// show calendar
	if($pMain->checkPerm('view_raids') || $pConfig['allow_anonymous']) {
		// setup calendar class
		require_once(RAIDER_INCLUDE_PATH.'calendar.php');

		$yearID = (isset($_GET['yearID'])?intval($_GET['yearID']):date("Y", time()));
		$monthID = (isset($_GET['monthID'])?intval($_GET['monthID']):date("n", time()));
		$dayID = (isset($_GET['dayID'])?intval($_GET['dayID']):false);

		$cal = new prCalendar($yearID, $monthID, $dayID);

		// calculate times
		$lower = gmmktime('0','0','0',$monthID, '1', $yearID);
		$upper = gmmktime('0','0','0',$monthID + 1, '1', $yearID);

		$sql['SELECT'] = '*';
		$sql['FROM'] = 'raid';
		$sql['WHERE'] = 'raid_id > 0 AND start_time >= '.$lower.' AND start_time <= '.$upper; // only show raids from a month ago and up
		$sql['SORT'] = 'start_time ASC';
		$result = $db_raid->set_query('select', $sql, __FILE__, __LINE__);

		while($data = $db_raid->sql_fetchrow($result)) {
			// check if raid is old and update the tables if it is.
			if($data['expired'] == 0) {
				isExpired($data,true);
			}

			$tooltip = 'index.php?option=com_frontpage&amp;task=ajax&amp;id='.$data['raid_id'];
			$icon = setIcon('_RAID_', $data['icon_name'], $tooltip, 1);

			// edit icon if admin
			if($pMain->getLogged()) {
				if(($pMain->checkPerm('edit_raids_any')) || ($pMain->checkPerm('edit_raids_own') && ($pMain->getProfileID() == getProfileFromTable('raid', 'raid_id', $data['raid_id'])))) {
					$admin = ' <a href="index.php?option=com_raids&amp;task=edit&amp;id='.$data['raid_id'].'">'.setIcon('_TEMPLATE_', 'icon_edit.png', $pLang['edit']).'</a> ';
				} else {
					$admin = '';
				}
			}

			$p->assign(
				array (
					'raid_link' => 'index.php?option=com_view&amp;id='.$data['raid_id'],
					'info' => checkSignup($data, $pMain, 1).$admin,
					'checkbox' => '',
					'raid_icon' => $icon
				)
			);

			// setup the look of the calendar event
			$calendar_output = $p->fetch(RAIDER_TEMPLATE_PATH.'event.tpl');
			$year = newDate("Y", $data['start_time'], 0);
			$month = newDate("n", $data['start_time'], 0);
			$day = newDate("j", $data['start_time'], 0);
			$cal->addEventContent($year, $month, $day, $calendar_output);
		}

		$p->assign('calendar',$cal->fetch(RAIDER_TEMPLATE_PATH.'calendar.tpl'));
	}
} elseif($task == 'ajax') {
	ob_end_clean();

	// basic data
	$sql['SELECT'] = '*';
	$sql['FROM'] = 'raid';
	$sql['WHERE'] = 'raid_id='.$id;
	$result = $db_raid->set_query('select', $sql, __FILE__, __LINE__);
	$data = $db_raid->fetch();

	// tooltip data
	$sql['SELECT'] = 'COUNT(*)';
	$sql['FROM'] = 'signups';
	$sql['WHERE'] = 'raid_id='.$id.'
					AND queue = 0
					AND cancel = 0';
	$signup_total = $db_raid->get_count($sql);

	$sql['SELECT'] = 'COUNT(*)';
	$sql['FROM'] = 'signups';
	$sql['WHERE'] = 'raid_id='.$id.'
					AND queue = 1
					AND cancel = 0';
	$queue_count_total = $db_raid->get_count($sql);

	$max = $data['maximum'];

	// setup event variables for templating
	$tooltip = "<strong>{$data['location']}</strong><br>"
	. $pLang['invite'].': '.newDate($pConfig['time_format'], $data['invite_time'], 0).'<br>'
	. $pLang['start'].': '.newDate($pConfig['time_format'], $data['start_time'], 0);

	if($pConfig['disable_freeze'] == 0) {
		$tooltip .= '<br>'.$pLang['freezes'].': '.newDate($pConfig['time_format'], $data['start_time']-($data['freeze_time']*3600), 0);
	}

	// add role information
	$tooltip .= '<br><br>';

	// get the roles
	$sql['SELECT'] = '*';
	$sql['FROM'] = array('role r', 'raid_limits l');
	$sql['WHERE'] = 'r.role_id = l.role_id
					AND l.raid_id = '.$data['raid_id'];
	$result_roles = $db_raid->set_query('select', $sql, __FILE__, __LINE__);

	$tooltip .= "<strong>{$pLang['signups']}</strong><br>";

	while($data_roles = $db_raid->sql_fetchrow($result_roles)) {
		// signed up
		$sql['SELECT'] = 'COUNT(*)';
		$sql['FROM'] = 'signups s';
		$sql['WHERE'] = 's.role_id='.$data_roles['role_id'].'
						AND s.cancel=0
						AND s.queue=0
						AND s.raid_id='.$id;
		$result_roles_counts = $db_raid->set_query('select', $sql, __FILE__, __LINE__);
		$data_roles_counts = $db_raid->sql_fetchrow($result_roles_counts);
		$role_count = $data_roles_counts[0];

		// queued
		$sql['SELECT'] = 'COUNT(*)';
		$sql['FROM'] = 'signups s';
		$sql['WHERE'] = 's.role_id='.$data_roles['role_id'].'
						AND s.queue=1
						AND s.raid_id='.$data['raid_id'];
		$result_roles_counts = $db_raid->set_query('select', $sql, __FILE__, __LINE__);
		$data_roles_counts = $db_raid->sql_fetchrow($result_roles_counts);
		$queue_count = $data_roles_counts[0];

		$tooltip .= "{$data_roles['role_name']}: {$role_count}/{$data_roles['raid_limit']} ({$queue_count})<br>";
	}

	$tooltip .= "<br>{$pLang['total']}: {$signup_total}/{$max} ({$queue_count_total})<br>";

	// level requirements
	$tooltip .= "<br><strong>{$pLang['restrictions']}</strong>";
	$tooltip .= "<br>{$pLang['minimum_level']}: {$data['minimum_level']}";
	$tooltip .= "<br>{$pLang['maximum_level']}: {$data['maximum_level']}";

	echo $tooltip;
	exit;
}

$p->display(RAIDER_TEMPLATE_PATH.'frontpage.tpl');
?>