<?php
/***************************************************************************
 *                             vb.functions.php
 *                            -------------------
 *   begin                : September 29, 2007
 *   developer            : Espen Carlsen
 *   based on             : phpbb3.functions.php
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// check profile
// Specific to vBulletin authentication only. Checks to see if a profile exists
// for vBulletin user and if not, creates one.
function pVerify($user) {
	global $db_raid, $pConfig;

	$userdata = array();

	if ($user['userid'] > 0) {
		// verify profile exists in local db
		$sql['SELECT'] = '*';
		$sql['FROM'] = 'profile';
		$sql['WHERE'] = 'profile_id='.intval($user['userid']);
		$result = $db_raid->set_query('select', $sql, __FILE__, __LINE__);
		unset($sql);

		// if nothing returns we need to create profile
		// otherwise they have a profile so let's set their ID
		// we'll just use the vBulletin user id as the profile ID to simplify things
		if(($db_raid->sql_numrows($result) == 0) && $user['userid'] > 0) {
			// are they a super user?
			$sql['SELECT'] = '*';
			$sql['FROM'] = 'profile';
			$sql['WHERE'] = 'profile_id>0';
			$check = $db_raid->set_query('select', $sql, __FILE__, __LINE__);

			// nothing returned, create profile
			unset($sql);
			$sql['INSERT'] = 'profile';
			$sql['VALUES'] = array(
							'profile_id'=>$user['userid'],
							'user_email'=>$user['email'],
							'password'=>'',
							'group_id'=>(($db_raid->sql_numrows($check) == 0)?1:$pConfig['default_group']),
							'username'=>$user['username'],
							'timezone'=>(empty($user['timezoneoffset'])?0:intval($user['timezoneoffset'])*100),
							'dst'=>(empty($user['dstonoff'])?0:intval($user['dstonoff'])),
							'join_date'=>time());
			$db_raid->set_query('insert', $sql, __FILE__, __LINE__);
			$userdata['group_id'] = $sql['VALUES']['group_id'];
		} else {
			// profile exists set group
			$data = $db_raid->sql_fetchrow($result);
			$userdata['group_id'] = $data['group_id'];
		}
		$userdata['profile_id'] = $user['userid'];
		$userdata['session_logged_in'] = 1;
		$userdata['username'] = $user['username'];
		$userdata['user_email'] = $user['email'];
		$userdata['timezone'] = (empty($user['timezoneoffset'])?'0':intval($user['timezoneoffset'])*100);
		$userdata['dst'] = (empty($user['dstonoff'])?'':$user['dstonoff']);
	}
	return $userdata;
}

// login function for vBulletin
function pLogin()
{
	global $pConfig_auth;

	// redirect to phpbb3's login form, with a return to this page.
	header("Location: " . $pConfig_auth['vBulletin_url']);
	exit;
}

// logout function for vBulletin
function pLogout()
{
	global $vbulletin,$pConfig_auth;

	// unset the session and remove all cookies
	foreach($_SESSION as $key=>$value)
		unset($_SESSION[$key]);

	// vBulletin session kill
	include_once($pConfig_auth['vBulletin_path'].'includes'.DIRECTORY_SEPARATOR.'functions_login.php');
	process_logout();
}

// Hack checking
if (isset($_GET['pConfig_auth[vBulletin_path]']) || isset($_POST['pConfig_auth[vBulletin_path]'])) {
	// add logging when availible here.
	die('Hack attempt.');
} else {
	// setup vBulletin user integration
	define('VB_AREA', 'Forum');
	define('CWD', $pConfig_auth['vBulletin_path']);
	include_once($pConfig_auth['vBulletin_path'].'includes'.DIRECTORY_SEPARATOR.'init.php');

	// Get vBulletin userinfo
	$user = $vbulletin->userinfo;
	$pMain = new Mainframe(pVerify($user));
}
?>