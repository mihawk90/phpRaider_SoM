<?php
/***************************************************************************
 *                           joomla.functions.php
 *                           --------------------
 *   begin                : October 24, 2007
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

function pVerify($user) {
	global $db_raid, $pConfig;

	$userdata = array();

	if (isset($user) && $user->id>0) {
	$sql['SELECT'] = '*';
	$sql['FROM'] = 'profile';
	$sql['WHERE'] = 'profile_id='.intval($user->id);
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);
	unset($sql);

	// if nothing returns we need to create profile
	// otherwise they have a profile so let's set their ID
	// we'll just use the Joomla user id as the profile ID to simplify things
	if($db_raid->sql_numrows() == 0) {
		// are they a super user?
		$sql['SELECT'] = '*';
		$sql['FROM'] = 'profile';
		$sql['WHERE'] = 'profile_id>0';

		$check = $db_raid->set_query('select', $sql, __FILE__, __LINE__);
		unset($sql);

		// nothing returned, create profile
		$sql['INSERT'] = 'profile';
		$sql['VALUES'] = array(
							'profile_id'=>intval($user->id),
							'user_email'=>$user->email,
							'password'=>'',
							'group_id'=>(($db_raid->sql_numrows($check) == 0)?1:$pConfig['default_group']),
							'username'=>$user->username,
							'join_date'=>time());
		$db_raid->set_query('insert', $sql, __FILE__, __LINE__);
			$userdata['group_id'] = $sql['VALUES']['group_id'];
			$userdata['timezone'] = 0;
			$userdata['dst'] = 0;
	} else {
		// profile exists set group
		$data = $db_raid->fetch();
			$userdata['group_id'] = $data['group_id'];
			$userdata['timezone'] = $data['timezone'];
			$userdata['dst'] = $data['dst'];
	}
		$userdata['profile_id'] = $user->id;
		$userdata['session_logged_in'] = 1;
		$userdata['username'] = $user->username;
		$userdata['user_email'] = $user->email;

	}
	return $userdata;
}

function pLogin() {
	global $pConfig,$pConfig_auth;

	// handle redirection because we're not using login_form
	pRedirect($pConfig_auth['register_url']);
	exit;
}

function pLogout() {
	// unset the session and remove all cookies
	global $mainframe, $dbf;

	$mainframe->logout();
	foreach($_SESSION as $key=>$value)
		unset($_SESSION[$key]);

	setcookie('username');
	setcookie('password');
	session_destroy();
}

// Hack checking
if (isset($_GET['pConfig_auth[joomla_path]']) || isset($_POST['pConfig_auth[joomla_path]'])) {
	// add logging when availible here.
	die('Hack attempt.');
} else {
	// Set flag that this is a parent file
	define( '_VALID_MOS', 1 );
	require_once($pConfig_auth['joomla_path'].'configuration.php');
	require_once($pConfig_auth['joomla_path'].'includes'.DIRECTORY_SEPARATOR.'joomla.php');

	$mainframe = new mosMainFrame( $database, '', $pConfig_auth['joomla_path'] );
	$mainframe->initSession();
	$ses = $mainframe->get('_session');

	// Make sure we don't have an anonymous user.
	if (isset($ses) && $ses->guest == 0) {
		$userdata = $mainframe->getUser();
		$pMain = new Mainframe(pVerify($userdata));
	} else {
		$pMain = new Mainframe();
	}
}
?>