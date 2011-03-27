<?php
/***************************************************************************
 *                                 auth_phpbb3.php
 *                            -------------------
 *   begin                : June 6, 2007
 *   copyright            : (C) 2005 Kyle Spraggs - mod for phpBB3 keldrak@gmail.com
 *   email                : spiffyjr@gmail.com
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
// Specific to phpBB authentication only. Checks to see if a profile exists
// for phpBB user and if not, creates one.
function pVerify($user) {
	global $db_raid, $pConfig, $pConfig_auth;

	$userdata = array();

	// Check that it's not an anonymous user or a bot that's authenticated by phpBB3
	if ($user->data['user_id'] != '1' && $user->data['is_registered'] == 1 && $user->data['username'] != 'Anonymous' && empty($user->data['is_bot'])) {
		$sql['SELECT'] = '*';
		$sql['FROM'] = 'profile';
		if (empty($pConfig_auth['phpbb_bridge_on_id'])) {
			$sql['WHERE'] = 'user_email = '.$db_raid->quote_smart(utf8_decode($user->data['user_email']));
		} else {
			$sql['WHERE'] = 'profile_id='.intval($user->data['user_id']);
		}

		$result = $db_raid->set_query('select', $sql, __FILE__, __LINE__);
		unset($sql);

		// if nothing returns we need to create profile
		// otherwise they have a profile so let's set their ID
		// we'll just use the phpBB user id as the profile ID to simplify things
		if(($db_raid->sql_numrows($result) == 0)) {
			// are they a super user?
			$sql['SELECT'] = '*';
			$sql['FROM'] = 'profile';
			$sql['WHERE'] = 'profile_id>0';

			$check = $db_raid->set_query('select', $sql, __FILE__, __LINE__);

			// nothing returned, create profile
			$sql['INSERT'] = 'profile';
			$sql['VALUES'] = array(
								'profile_id'=>$user->data['user_id'],
								'user_email'=>utf8_decode($user->data['user_email']),
								'password'=>'',
								'group_id'=>(($db_raid->sql_numrows($check) == 0)?1:$pConfig['default_group']),
								'username'=>utf8_decode($user->data['username']),
								'join_date'=>time());
			$db_raid->set_query('insert', $sql, __FILE__, __LINE__);
			$userdata['group_id'] = $sql['VALUES']['group_id'];
		} else {
			// profile exists set group
			$data = $db_raid->sql_fetchrow($result);
			$userdata['group_id'] = $data['group_id'];
		}
		// setup session information
		$userdata['profile_id'] = $user->data['user_id'];
		$userdata['session_logged_in'] = 1;
		$userdata['username'] = utf8_decode($user->data['username']);
		$userdata['user_email'] = utf8_decode($user->data['user_email']);
		$userdata['timezone'] = '';
		$userdata['dst'] = '';
	}
	return $userdata;
}

// login function for phpBB3
function pLogin()
{
	global $pConfig_auth;

	// redirect to phpbb3's login form, with a return to this page.
	header("Location: " . $pConfig_auth['phpbb_url'].'/ucp.php?mode=login&redirect='.$_SERVER['PHP_SELF']);
	exit;
}

// logout function for phpBB
function pLogout()
{
	global $user;
// unset the session and remove all cookies
	foreach($_SESSION as $key=>$value)
		unset($_SESSION[$key]);

//    phpbb3 session kill
	$user->session_kill();
}

// define our auth type
if( isset( $_GET["phpbb_root_path"] ) || isset( $_POST["phpbb_root_path"]) || isset($_GET['pConfig_auth[phpbb_path]']) || isset($_POST['pConfig_auth[phpbb_path]']) ) {
	log_hack();
	die('Hack attempt.');
} else {
	$phpbb_root_path = $pConfig_auth['phpbb_path'];
	$phpEx = substr(strrchr(__FILE__, '.'), 1);

	// setup phpBB user integration
	define('IN_PHPBB', true);

	include_once($pConfig_auth['phpbb_path'].'common.php');

	global $user_group_table;
	$user_group_table = $table_prefix . "user_group";

	// Start session management - phpBB3
	$user->session_begin();
	$auth->acl($user->data);
	$user->setup();

	// remove the phpbb3 template variable.
	unset($template);
	$db_raid->sql_query('SET NAMES DEFAULT');
	$db_raid->sql_query('SET SESSION sql_mode=DEFAULT');

	$pMain = new Mainframe(pVerify($user));
}
?>
