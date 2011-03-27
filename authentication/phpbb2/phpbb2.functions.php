<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// phpBB specific
function pVerify($user) {
	global $db_raid, $pConfig, $pConfig_auth;

	$userdata = array();

	// set session defaults
	if ($userdata['user_id'] != '-1') {
		// verify profile exists in local db
		$sql['SELECT'] = '*';
		$sql['FROM'] = 'profile';
		if (empty($pConfig_auth['phpbb_bridge_on_id'])) {
			$sql['WHERE'] = 'user_email='.$db_raid->quote_smart($user['user_email']);
		} else {
			$sql['WHERE'] = 'profile_id='.intval($user['user_id']);
		}

		$db_raid->set_query('select', $sql, __FILE__, __LINE__);

		// if nothing returns we need to create profile
		// otherwise they have a profile so let's set their ID
		// we'll just use the phpBB user id as the profile ID to simplify things
		if(($db_raid->sql_numrows($result) == 0) && $user['username'] != 'Anonymous') {
			// are they a super user?
			$sql['SELECT'] = '*';
			$sql['FROM'] = 'profile';
			$sql['WHERE'] = 'profile_id>0';

			$db_raid->set_query('select', $sql, __FILE__, __LINE__);

			// nothing returned, create profile
			$sql['INSERT'] = 'profile';
			$sql['VALUES'] = array(
								'profile_id'=>$user['user_id'],
								'user_email'=>$user['user_email'],
								'password'=>'',
								'group_id'=>(($db_raid->sql_numrows($check) == 0)?1:$pConfig['default_group']),
								'username'=>$user['username'],
								'join_date'=>time());
			$db_raid->set_query('insert', $sql, __FILE__, __LINE__);
			$userdata['group_id'] = $sql['VALUES']['group_id'];
			$userdata['timezone'] = 0;
			$userdata['dst'] = 0;
		} else {
			// profile exists set group
			$data = $db_raid->sql_fetchrow($result);
			$userdata['group_id'] = $data['group_id'];
			$userdata['timezone'] = $data['timezone'];
			$userdata['dst'] = $data['dst'];
		}

		// setup session information
		$userdata['profile_id'] = $user['user_id'];
		$userdata['session_logged_in'] = 1;
		$userdata['username'] = $user['username'];
		$userdata['email'] = $user['user_email'];
	}
	return $userdata;
}

function pLogin() {
	global $pConfig_auth;

	// clear menu
	unset($_SESSION['SMARTYMENU']);

	// handle redirection because we're not using login_form
	pRedirect($pConfig_auth['phpbb_url'].'login.php');
	exit;
}

function pLogout() {
	global $userdata, $pConfig_auth;

	// unset the session and remove all cookies
	foreach($_SESSION as $key=>$value)
		unset($_SESSION[$key]);

	// select phpBB db
	include($pConfig_auth['phpbb_path'].'config.php');
	mysql_select_db($dbname);
	session_end($userdata['session_id'], $userdata['user_id']);
	pRedirect('index.php');
}
if( isset($_GET['pConfig_auth[phpbb_path]']) || isset($_POST['pConfig_auth[phpbb_path]']) ) {
	// add logging when availible here.
	die('Hack attempt.');
} else {
	// setup phpBB information
	define("IN_PHPBB", 1);

	$phpbb_root_path = $pConfig_auth['phpbb_path'];
	include($phpbb_root_path.'config.php');
	include($phpbb_root_path.'extension.inc');
	include($phpbb_root_path.'common.'.$phpEx);

	// phpBB user information
	global $user_group_table;
	$user_group_table = $table_prefix."user_group";
	$userdata = session_pagestart($user_ip, PAGE_INDEX);
	unset($sql);

	$pMain = new Mainframe(pVerify($userdata));
}
?>
