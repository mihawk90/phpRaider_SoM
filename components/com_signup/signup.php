<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// load footer?
$load_footer = 0;

// verify permissions
if(!$pMain->checkPerm('allow_signup')) {
	die("Invalid access attempt");
}

// get raid information
$sql["SELECT"] = "*";
$sql["FROM"] = "raid";
$sql["WHERE"] = "`raid_id`={$id}";
$db_raid->set_query('select', $sql, __FILE__, __LINE__);
$data = $db_raid->fetch();

$character_id = intval($_POST['character']);
$char_owner = getProfileFromTable('character', 'character_id', $character_id);
if ($pMain->getProfileID()==$char_owner) {
	$owner = $pMain;
} else {
	$owner = getMainframeFromProfileID($char_owner);
	if ($pMain->checkPerm('edit_subscriptions_any')) {
		$owner->setPerm('edit_subscriptions_any');
	}
}

$checkSignup = checkSignup($data, $owner, 0);
unset($owner);

if($checkSignup == 0) {
	if (!empty($_POST['comments'])) {
		$comments = strip_tags($_POST['comments']);
	} elseif ($pMain->getProfileID() != getProfileFromTable('character', 'character_id', $character_id)) {
		$comments = sprintf($pLang['siSignedUpByComment'],$pMain->getUser());
	} else {
		$comments = '';
	}
	$role_id = intval($_POST['signup_role']);
	$signup_type = $_POST['signup_type'];

	// check initial queue status
	if($signup_type == 'queue')
		$queue = 1;
	else
		$queue = 0;

	// check intial cancel status
	if($signup_type == 'cancel')
		$cancel = 1;
	else
		$cancel = 0;

	// check number of signups that are approved only
	$sql["SELECT"] = "COUNT(*)";
	$sql["FROM"] = "signups";
	$sql["WHERE"] = "`raid_id`={$id}
					AND `role_id`={$role_id}
					AND `cancel`=0
					AND `queue`=0
				";
	$count = $db_raid->get_count($sql);

	// check role restriction
	$sql["SELECT"] = "raid_limit";
	$sql["FROM"] = "raid_limits";
	$sql["WHERE"] = "`raid_id`={$id}
					AND `role_id`={$role_id}
				";
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);
	$limit = $db_raid->fetch();

	// get class id
	$sql["SELECT"] = "class_id";
	$sql["FROM"] = "character";
	$sql["WHERE"] = "`character_id`={$character_id}";
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	$class_id = $db_raid->fetch();
	$class_id = $class_id['class_id'];

	if(empty($count))
		$count = '0';

	// are they over limit and wanting to be approved
	if(($count >= $limit['raid_limit']) && $signup_type == 'approve')
		$queue = 1;

	// is auto queueing enabled?
	if($pConfig['auto_queue'] && $signup_type != 'cancel' && !($pMain->getProfileID() == getProfileFromTable('raid', 'raid_id', $id) || $pMain->checkPerm('edit_subscriptions_any') || ($pMain->checkPerm('edit_subscriptions_own')) && ($pMain->getProfileID() == getProfileFromTable('character', 'character_id', $character_id))))
		$queue = 1;

	// place in signupss
	$sql["INSERT"] = "signups";
	$sql["VALUES"] = array(
						'raid_id'=>$id,
						'character_id'=>$character_id,
						'cancel'=>$cancel,
						'queue'=>$queue,
						'profile_id'=>$char_owner,
						'comments'=>$comments,
						'timestamp'=>time(),
						'class_id'=>$class_id,
						'role_id'=>$role_id
					);
	$db_raid->set_query('insert', $sql, __FILE__, __LINE__);
}
if (!empty($_POST['redirect'])) {
	pRedirect($_POST['redirect']);
} else {
	pRedirect('index.php?yearID='.newDate('Y',$data['start_time'],0).'&monthID='.newDate('m',$data['start_time'],0));
}
?>
