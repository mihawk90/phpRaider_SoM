<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// load footer?
$load_footer = 1;

if(empty($task) || $task == 'login') {
	// do not use phpRaider's login form
	// just run the login function which should handle redirection or form input
	if($pConfig_auth['use_login'] == 0) {
		pLogin();
		include(RAIDER_BASE_PATH.'footer.php');
		exit;
	}

	// localizations
	$p->assign(
		array(
			// errors
			'usernameError' => $pLang['lUsername_error'],
			'passwordError' => $pLang['lPassword_error'],

			// text
			'lHeader' => $pLang['lHeader'],
			'rememberText' => $pLang['lRemember_text'],
			'usernameText' => $pLang['lUsername_text'],
			'passwordText' => $pLang['lPassword_text'],

			// buttons
			'reset' => $pLang['reset'],
			'submit' => $pLang['submit']
		)
	);

	// check for invalid login
	if(isset($_GET['invalid'])) {
		$p->assign('invalid_login', $pLang['invalid_login']);
	}

	// assign task
	$p->assign('task', $task);

	if(empty($_POST)) {
		// new form, we (re)set the session data
		SmartyValidate::connect($p, true);

		// register our validators
		SmartyValidate::register_validator('username', 'username', 'notEmpty', false, false, 'trim');
		SmartyValidate::register_validator('password', 'password', 'notEmpty', false, false, 'trim');

		// display form
		$p->display(RAIDER_TEMPLATE_PATH.'login_form.tpl');
	} else {
		// validate after a POST
		SmartyValidate::connect($p);

		if(SmartyValidate::is_valid($_POST)) {
			// check login
			if(pLogin()) {
				pRedirect('index.php');
			} else {
				pRedirect('index.php?option=com_login&task=login&invalid=true');
			}

			// no errors, done with SmartyValidate
			SmartyValidate::disconnect();
		} else {
			// error, redraw the form
			$p->assign($_POST);
			$p->display(RAIDER_TEMPLATE_PATH.'login_form.tpl');
		}
	}
} else if($task == 'logout') {
	pLogout();
	pRedirect('index.php');
} else {
	printError($pLang['invalidOption']);
}
?>