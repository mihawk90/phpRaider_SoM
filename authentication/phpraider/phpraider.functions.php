<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

function pLogin() {
	global $db_raid, $pConfig;

	if(isset($_POST['username'])) {
		$username = strtolower($_POST['username']);
		$password = md5($_POST['password']);
		$autologin = isset($_POST['autologin']);
	} elseif(!empty($_COOKIE['username']) && !empty($_COOKIE['password'])) {
		$username = strtolower($_COOKIE['username']);
		$password = $_COOKIE['password'];
		$autologin = true;
	} else {
		pLogout();
	}

	$sql['SELECT'] = '*';
	$sql['FROM'] = 'profile';
	$sql['WHERE'] = 'username ='.$db_raid->quote_smart($username).' AND password='.$db_raid->quote_smart($password);
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	if($db_raid->sql_numrows() > 0)	{
		$data = $db_raid->fetch();
		// login successful
		if($autologin) {
			// they want automatic logins so set the cookie
			// set to expire in one month
			setcookie('username', $data['username'], time() + 2629743);
			setcookie('password', $data['password'], time() + 2629743);
		}

		// set user variables
		$_SESSION['profile_id'] = $data['profile_id'];
		$_SESSION['base_path'] = dirname(__FILE__);
		$_SESSION['session_logged_in'] = 1;
		$_SESSION['initiated'] = 1;
		$_SESSION['username'] = $data['username'];
		$_SESSION['user_email'] = $data['user_email'];
		$_SESSION['group_id'] = $data['group_id'];
		$_SESSION['timezone'] = $data['timezone'];
		$_SESSION['dst'] = $data['dst'];

		return 1;
	}

	return 0;
}

// return values are as follows
// 0 - registration passed
// 1 - registration failed (user taken)
// 2 - registration failed (email taken)
// anything else - registration failed (unknown reason)
function pRegister($_POST) {
	global $db_raid, $pConfig;

	// verify they aren't logged in
	pLogout();

	// check if username exists
	$sql['SELECT'] = '*';
	$sql['FROM'] = 'profile';
	$sql['WHERE'] = 'username = '.$db_raid->quote_smart($_POST['username']);
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	if($db_raid->sql_numrows($result) > 0) {
		return 1;
	}

	// check if email exists
	$sql['SELECT'] = '*';
	$sql['FROM'] = 'profile';
	$sql['WHERE'] = 'user_email = '.$db_raid->quote_smart($_POST['email']);
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	if($db_raid->sql_numrows($result) > 0) {
		return 2;
	}

	$sql['INSERT'] = 'profile';
	$sql['VALUES'] = array(
						'username'=>$_POST['username'],
						'password'=>md5($_POST['password']),
						'user_email'=>$_POST['email'],
						'group_id'=>$pConfig['default_group'],
						'join_date'=>time()
					);

	$db_raid->set_query('insert', $sql, __FILE__, __LINE__);

	return 0;
}

function pLogout() {
	// unset the session and remove all cookies
	foreach($_SESSION as $key=>$value)
		unset($_SESSION[$key]);

	setcookie('username');
	setcookie('password');
	setcookie('base_path');
	session_destroy();
}

// set session defaults
if (!empty($_SESSION['name'])) {
	// Make sure we're not taking over a phpraid session
	if (strtolower($_SESSION['name']) == 'phpraid') {
		unset($_SESSION['initiated']);
	}
}
if(!isset($_SESSION['initiated'])) {
	if(!empty($_COOKIE['username']) && !empty($_COOKIE['password'])) {
		pLogin();
	} else {
		$_SESSION['profile_id'] = -1;
		$_SESSION['session_logged_in'] = 0;
		$_SESSION['group_id'] = -1;
		$_SESSION['username'] = 'anonymous';
		$_SESSION['user_email'] = '';
		$_SESSION['timezone'] = '';
		$_SESSION['dst'] = '';
		$_SESSION['base_path'] = dirname(__FILE__);
	}
} else {
	// verify that session is for this install only
	if(!isset($_SESSION['base_path']) || ($_SESSION['base_path'] != dirname(__FILE__))) {
		pLogout();
	}
}
?>