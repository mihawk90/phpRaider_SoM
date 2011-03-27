<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// load footer?
$load_footer = 1;

if(!$pMain->checkPerm('view_raids') && !$pConfig['allow_anonymous'])
	pRedirect('index.php?option=com_login&task=login');

if(empty($task) || $task == '') {
	// generic raid details
	$sql['SELECT'] = '*';
	$sql['FROM'] = 'raid';
	$sql['WHERE'] = 'raid_id='.$id;
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);
	$pRaid = $db_raid->fetch();
	$isRaidOwner = (($pRaid['profile_id']==$pMain->getProfileID())?true:false);

	unset($sql);
	$sql['SELECT'] = 'COUNT(*)';
	$sql['FROM'] = 'signups';
	$sql['WHERE'] = 'raid_id='.$id.'
					AND cancel=0
					AND queue=0';
	$approved_count = $db_raid->get_count($sql);

	$sql['SELECT'] = 'COUNT(*)';
	$sql['FROM'] = 'signups';
	$sql['WHERE'] = 'raid_id='.$id.'
					AND cancel=0
					AND queue=1';
	$queued_count = $db_raid->get_count($sql);

	$sql['SELECT'] = 'COUNT(*)';
	$sql['FROM'] = 'signups';
	$sql['WHERE'] = 'raid_id='.$id.'
					AND cancel=1
					AND queue=0';
	$cancelled_count = $db_raid->get_count($sql);
	$total_signed = $approved_count + $queued_count + $cancelled_count;

	$time_until = timeDiff(strtotime(newDate("m/d/y h:ia", $pRaid['start_time'] + $pConfig['timezone'],0)), true);

	$approved_count = $approved_count.'/'.$total_signed.' ('.number_format(((empty($total_signed))?0:($approved_count/$total_signed*100)),2).'%)';
	$queued_count = $queued_count.'/'.$total_signed.' ('.number_format(((empty($total_signed))?0:($queued_count/$total_signed*100)),2).'%)';
	$cancelled_count = $cancelled_count.'/'.$total_signed.' ('.number_format(((empty($total_signed))?0:($cancelled_count/$total_signed*100)),2).'%)';
	$max_count = ($approved_count + $queued_count).'/'.$pRaid['maximum'].' ('.number_format(((empty($pRaid['maximum']))?0:(($approved_count + $queued_count)/$pRaid['maximum']*100)),2).'%)';

	// signup icon
	if($pMain->checkPerm('allow_signup')) {
		$signupIcon = checkSignup($pRaid, $pMain, 1,'index.php?option=com_view&amp;id='.$id);
	} else {
		$signupIcon = '<a href="index.php?option=com_login">'.$pLang['viSignupLogin'].'</a>';
	}

	// setup raid information (description, times, etc)
	$p->assign(
		array(
			// text
			'header' => sprintf($pLang['viHeader'], $pRaid['location']),
			'approved' => $pLang['viApproved'],
			'queued' => $pLang['viQueued'],
			'cancelled' => $pLang['viCancelled'],
			'description' => $pRaid['description'],
			'leader' => $pRaid['raid_leader'],
			'invite_time' => newDate($pConfig['date_format'].' @ '.$pConfig['time_format'], $pRaid['invite_time'], 0),
			'start_time' => newDate($pConfig['date_format'].' @ '.$pConfig['time_format'], $pRaid['start_time'], 0),
			'maximum' => $pRaid['maximum'],
			'min_level' => $pRaid['minimum_level'],
			'max_level' => $pRaid['maximum_level'],
			'minLevelText' => $pLang['viMinLevel'],
			'maxLevelText' => $pLang['viMaxLevel'],
			'leaderText' => $pLang['viLeader'],
			'inviteText' => $pLang['viInviteTime'],
			'startText' => $pLang['viStartTime'],
			'timeUntil' => $time_until,
			'approvedCount' => $approved_count,
			'queuedCount' => $queued_count,
			'cancelledCount' => $cancelled_count,
			'maxCount' => $max_count,
			'maxText' => $pLang['viMax_count'],
			'approvedText' => $pLang['viApproved_count'],
			'queuedText' => $pLang['viQueued_count'],
			'cancelledText' => $pLang['viCancelled_count'],
			'signupIcon' => $signupIcon,
			'signupText' => $pLang['viSignupText']
		)
	);

	// Raidowner and people with the 'edit_subscription_any' are allowed to signup other people to the raid.
	if(!isExpired($pRaid) && ($isRaidOwner || $pMain->checkPerm('edit_subscriptions_any'))) {
		$icon = '<a href="javascript:popupForm(\'signup'.$id.'Other\');" onMouseover="ddrivetip(\''.$pLang['viSignupOtherText'].'\');" onMouseout="hideddrivetip()"><img src="templates/'.$pConfig['template'].'/images/icons/icon_signup.png" border="0" alt="'.$pLang['viSignupOtherText'].'"></a>'.signupForm($pRaid, $pMain, 'index.php?option=com_view&amp;id='.$id, false, true);
		$p->assign(
			array(
				'signupOtherIcon' => $icon,
				'signupOtherText' => $pLang['viSignupOtherText']
			)
		);
	}

	// get signups for each role from database and store in array to be parsed
	$order = ((empty($_GET['sort']))?'timestamp':$_GET['sort']);
	$approved = getSignup($id, 0, 0, 4, $order);
	$cancel = getSignup($id, 1, 0, 1, $order);
	$queue = getSignup($id, 0, 1, 4, $order);

	// assign the variables and display the information
	$p->assign('approved_signups', $approved);
	$p->assign('cancelled_signups', $cancel);
	$p->assign('queued_signups', $queue);
	$p->display(RAIDER_TEMPLATE_PATH.'view.tpl');
} else {
	$char_id = ((empty($_GET['char_id']))?((empty($_POST['character']))?false:intval($_POST['character'])):intval($_GET['char_id']));

	$sql['SELECT'] = '*';
	$sql['FROM'] = 'raid';
	$sql['WHERE'] = 'raid_id='.$id;
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);
	$pRaid = $db_raid->fetch();
	$isRaidOwner = (($pRaid['profile']==$pMain->getProfileID())?true:false);
	$isExpired = isExpired($pRaid,true);

	// Limit the isCharacterOwner to only check when the user is not:
	// Raid owner of has the right to edit any subscription
	if(!($isRaidOwner && $pMain->checkPerm('edit_subscriptions_any'))) {
		$isCharOwner = (($char_id)?checkOwn('character', $char_id, $pMain->getProfileID()):false);
	}
	if ($task == 'manage') {
		$type = ((empty($_GET['type']))?'':$_GET['type']);
		if (!isset($_POST['character_id']) && $char_id>0) {
			$_POST['character_id']['0'] = $char_id;
		}

		$where = 'raid_id='.$id.' AND character_id=%d';
		switch($type) {
			case 'approve':
				$query_type='update';
				$sql['VALUES'] = array('queue'=>0, 'cancel'=>0);
				break;
			case 'cancel':
				$query_type='update';
				$sql['VALUES'] = array('queue'=>0, 'cancel'=>1);
				break;
			case 'queue':
				$query_type='update';
				$sql['VALUES'] = array('queue'=>1, 'cancel'=>0);
				break;
			case 'remove':
				$query_type='delete';
				break;
			default:
				unset($sql);
				break;
		}
		if (!empty($query_type)) {
			$sql[strtoupper($query_type)] = 'signups';
			for($i = 0; $i < count($_POST['character_id']); $i++) {
				$char_id = intval($_POST['character_id'][$i]);
				// Validate rights for each character.
				// Running the query only if:
				// - Person is the raidleader
				// - Person has the 'edit_subscription_any' right
				// - Person is the character owner and the type is 'cancel' or 'queue'
				// - Person is the character owner and has the right 'edit_subscription_own' AND the type is 'remove'
				// - Person is the character owner and has the right 'edit_subscription_own' or auto_queue is off AND the type is 'approve'
				if(!$isExpired && (
					$isRaidOwner ||
					$pMain->checkPerm('edit_subscriptions_any') ||
					($isCharOwner && ($type == 'cancel' || $type == 'queue' ||
					($type == 'remove' && $pMain->checkPerm('edit_subscriptions_own') ||
					($type == 'approve' && ($pConfig['auto_queue'] == 0 || $pMain->checkPerm('edit_subscriptions_own')))))))) {
						$sql['WHERE'] = sprintf($where,$char_id);
						$db_raid->set_query($query_type, $sql, __FILE__, __LINE__);
				}
			}
		}
		pRedirect('index.php?option=com_view&id='.$id);
	} else if($task == 'comment') {
		// verify that the profile owns the character or that the logged in person has the right 'edit_subscriptions_any'
		if(!$isExpired && ($isRaidOwner || $pMain->checkPerm('edit_subscriptions_any') || $isCharOwner)) {
			ob_clean();
			if(empty($_POST)) {
				die('invalid access attempt');
			} else {
				// update comment
				$sql['UPDATE'] = 'signups';
				$sql['VALUES'] = array('comments' => strip_tags($_POST['comments']));
				$sql['WHERE'] = "character_id = {$char_id}
								AND raid_id = {$id}";
				$db_raid->set_query('update', $sql, __FILE__, __LINE__);
			}
		}
		pRedirect('index.php?option=com_view&id='.$id);
	} else if ($task == 'change') {
		$old_char_id = intval($_GET['old_char_id']);
		$role_id = intval($_POST['signup_role']);
		$changeOwn = 0;

		if (!$char_id == $old_char_id) {
			// If it's not a raid admin, the user has to own both characters
			$isCharOwner = $isCharOwner && checkOwn('character', $old_char_id, $pMain->getProfileID());
			$owner = getProfileFromTable('character','character_id',$old_char_id);
			$isSameOwner = checkOwn('character',$char_id,$owner);
		} else {
			$isSameOwner = true;
		}

		// Make sure only raid admins can change the the signups allways.
		// Normal users can only change when they're available. Not after they're confirmed.
		if ($isCharOwner && !($isRaidOwner || $pMain->checkPerm('edit_subscriptions_any'))) {
			$sql['SELECT'] = 'COUNT(*)';
			$sql['FROM'] = 'signups';
			$sql['WHERE'] = 'cancel=0 AND queue=1 AND raid_id='.$id.' AND character_id='.$old_char_id;
			$changeOwn = $db_raid->get_count($sql);
		}

		// check subscription permissions
		if(!$isExpired && ($isRaidOwner || $pMain->checkPerm('edit_subscriptions_any') || ($isCharOwner && ($changeOwn || $pMain->checkPerm('edit_subscriptions_own'))))) {
			if ($isSameOwner) {
				$sql['UPDATE'] = 'signups';
				$sql['VALUES'] = array('character_id' => $char_id,'role_id'=>$role_id);
				$sql['WHERE'] = 'character_id = '.$old_char_id.' AND raid_id = '.$id;
				$db_raid->set_query('update', $sql, __FILE__, __LINE__);
			}
		}
		pRedirect('index.php?option=com_view&id='.$id);
	} else {
		echo "Invalid option specified";
		exit;
	}
}
?>