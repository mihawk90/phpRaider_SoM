<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// load footer?
$load_footer = 1;

if(empty($task) || $task == '') {
	// no caching for this
	$p->caching = false;

	// localizations
	$p->assign(
		array(
			// errors
			'usernameError' => $pLang['reName_error'],
			'passwordError' => $pLang['rePass_error'],
			'password2Error' => $pLang['rePass2_error'],
			'emailError' => $pLang['reEmail_error'],

			// text
			'header' => $pLang['reHeader'],
			'usernameText' => $pLang['reUsername_text'],
			'emailText' => $pLang['reEmail_text'],
			'passwordText' => $pLang['rePassword_text'],
			'password2Text' => $pLang['rePassword2_text'],

			// buttons
			'reset' => $pLang['reset'],
			'submit' => $pLang['submit']
		)
	);

	if(empty($_POST)) {
		// new form, we (re)set the session data
		SmartyValidate::connect($p, true);

		// register our validators
		SmartyValidate::register_validator('username', 'username', 'notEmpty', false, false, 'trim,strip_tags,trim');
		SmartyValidate::register_validator('password', 'password', 'notEmpty', false, false, 'trim');
		SmartyValidate::register_validator('match', 'password:password2', 'isEqual');
		SmartyValidate::register_validator('email', 'email', 'isEmail', false, false, 'trim');

		// display form
		$p->display(RAIDER_TEMPLATE_PATH.'register.tpl');
	} else {
		// validate after a POST
		SmartyValidate::connect($p);

		if(SmartyValidate::is_valid($_POST)) {
			// updating information so clear cache
			$p->clear_cache(RAIDER_TEMPLATE_PATH.'register.tpl');

			// no errors, done with SmartyValidate
			SmartyValidate::disconnect();

			$didRegister = pRegister($_POST);

			// add registration
			if($didRegister == 0) {
				// localizations
				$p->assign(
					array(
						'header' => $pLang['reComplete_header'],
						'message' => $pLang['reComplete_message'],
					)
				);
			} else if($didRegister == 1) { // username taken
				// localizations
				$p->assign(
					array(
						'header' => $pLang['reError_header'],
						'message' => $pLang['reUsername_message'],
					)
				);
			} else if($didRegister == 2) { // email taken
				// localizations
				$p->assign(
					array(
						'header' => $pLang['reError_header'],
						'message' => $pLang['reEmail_message'],
					)
				);
			} else { // unknown error
				// localizations
				$p->assign(
					array(
						'header' => $pLang['reError_header'],
						'message' => $pLang['reUnknown_message'],
					)
				);
			}

			$p->display(RAIDER_TEMPLATE_PATH.'register_complete.tpl');
		} else {
			// error, redraw the form
			$p->assign($_POST);
			$p->display(RAIDER_TEMPLATE_PATH.'register.tpl');
		}
	}
} else {
	printError($pLang['invalidOption']);
}
?>
