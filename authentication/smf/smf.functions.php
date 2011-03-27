<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// phpBB specific
function pVerify($user) {
	global $db_raid, $pConfig;

	$userdata = array();

	if ($user['user']['is_logged']) {
		$sql['SELECT'] = '*';
		$sql['FROM'] = 'profile';
		$sql['WHERE'] = 'username='.$db_raid->quote_smart($user['user']['username']);
		$result = $db_raid->set_query('select', $sql, __FILE__, __LINE__);

		// if nothing returns we need to create profile
		// otherwise they have a profile so let's set their ID
		// we'll just use the SMF user id as the profile ID to simplify things
		if(($db_raid->sql_numrows($result) == 0)) {
			// are they a super user?
			$sql['SELECT'] = '*';
			$sql['FROM'] = 'profile';
			$sql['WHERE'] = 'profile_id>0';
			$check = $db_raid->set_query('select', $sql, __FILE__, __LINE__);

			$sql['INSERT'] = 'profile';
			$sql['VALUES'] = array(
						'profile_id'=>$user['user']['id'],
						'user_email'=>$user['user']['email'],
						'password'=>'',
						'group_id'=>(($db_raid->sql_numrows($check) == 0)?1:$pConfig['default_group']),
						'username'=>$user['user']['username'],
						'join_date'=>time());
			$db_raid->set_query('insert', $sql, __FILE__, __LINE__);

			// set their group id
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
		$userdata['profile_id'] = $user['user']['id'];
		$userdata['session_logged_in'] = 1;
		$userdata['username'] = $user['user']['username'];
		$userdata['user_email'] = $user['user']['email'];
	}
	return $userdata;
}

function pLogin() {
	global $pConfig_auth, $pConfig;

	// clear menu
	unset($_SESSION['SMARTYMENU']);

	// setup redirect after login
	$_SESSION['login_url'] = $pConfig['site_url'];

	// handle redirection because we're not using login_form
	pRedirect($pConfig_auth['smf_url'].'index.php?action=login');
}

function pLogout() {
	global $userdata, $pConfig_auth, $pConfig, $db_prefix, $sourcedir, $ID_MEMBER,
			$user_info, $user_settings, $context, $modSettings;

	require_once($sourcedir . '/Subs-Auth.php');

	if (isset($_SESSION['pack_ftp']))
		$_SESSION['pack_ftp'] = null;

	// Just ensure they aren't a guest!
	if (!$user_info['is_guest'])
	{
		if (isset($modSettings['integrate_logout']) && function_exists($modSettings['integrate_logout']))
			call_user_func($modSettings['integrate_logout'], $user_settings['memberName']);

		// If you log out, you aren't online anymore :P.
		db_query("
			DELETE FROM {$db_prefix}log_online
			WHERE ID_MEMBER = $ID_MEMBER
			LIMIT 1", __FILE__, __LINE__);
	}

	$_SESSION['log_time'] = 0;

	// Empty the cookie! (set it in the past, and for ID_MEMBER = 0)
	setLoginCookie(-3600, 0);

	pRedirect($pConfig['site_url']);
}

// Hack checking
if (isset($_GET['pConfig_auth[smf_path]']) || isset($_POST['pConfig_auth[smf_path]'])) {
	// add logging when availible here.
	die('Hack attempt.');
} else {
	// setup SMF information
	include($pConfig_auth['smf_path'].DIRECTORY_SEPARATOR.'SSI.php');

	// stop the hacking attempts
	if (!defined('SMF'))
		define('SMF', 'SSI');

	// if we're using SMF < 2.0, then SMF escapes the $_POST variables, so we have to unescape.
	if (!empty($_POST)) {
		if (empty($modSettings['integrate_magic_quotes']) && version_compare($modSettings['smfVersion'],"2.0 a","<")) {
			$_POST = stripslashes__recursive($_POST);
		}
	}

	$pMain = new Mainframe(pVerify($context));
}
?>