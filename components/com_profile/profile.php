<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// load footer?
$load_footer = 1;

if(empty($task) || $task == '') {
	// verify user is logged in
	if(!$pMain->getLogged()) {
		pRedirect('index.php?option=com_login');
	}

	// no caching for this
	$p->caching = false;

	// localizations
	$p->assign(
		array(
			'header' => $pLang['paCreate_header'],

			// errors
			'emailError' => $pLang['paEmail_error'],
			'enterPasswordText' => $pLang['paEnterPassword_error'],
			'newPasswordText' => $pLang['paNewPassword_error'],
			'confirmPasswordText' => $pLang['paConfirmPassword_error'],

			// text
			'emailText' => $pLang['paEmail_text'],
			'enterPasswordText' => $pLang['paEnterPassword_text'],
			'newPasswordText' => $pLang['paNewPassword_text'],
			'confirmPasswordText' => $pLang['paConfirmPassword_text'],

			// buttons
			'reset' => $pLang['reset'],
			'submit' => $pLang['submit']
		)
	);

	if(empty($_POST)) {
		// new form, we (re)set the session data
		SmartyValidate::connect($p, true);

		// assign old values if it's an edit
		$sql["SELECT"] = "*";
		$sql["FROM"] = "profile";
		$sql["WHERE"] = "profile_id = ".$pMain->getProfileID();
		$db_raid->set_query('select', $sql, __FILE__, __LINE__);
		$p->assign($db_raid->fetch());

		// register our validators
		SmartyValidate::register_validator('email', 'user_email', 'isEmail', false, false, 'trim');

		// display form
		$p->display(RAIDER_TEMPLATE_PATH.'profile.tpl');
	} else {
		// validate after a POST
		SmartyValidate::connect($p);

		// only works with phpRaider authentication
		if($pConfig['authentication'] == 'phpraider') {
			// do a few custom checks
			$form_error = 0;

			// verify password entered matches db
			$sql["SELECT"] = "password";
			$sql["FROM"] = "profile";
			$sql["WHERE"] = "profile_id = ".$pMain->getProfileID();
			$db_raid->set_query('select', $sql, __FILE__, __LINE__);
			$data = $db_raid->fetch();

			if((md5($_POST['enter_password']) != $data['password'])) {
				$form_error = 1;
				$p->assign('enterPasswordError', '<span class=formError>'.$pLang['paEnterPassword_error'].'</span');
			}

			// make sure (new) passwords match
			if($form_error == 0 && !empty($_POST['new_password'])) {
				if($_POST['new_password'] != $_POST['confirm_password']) {
					$form_error = 1;
					$p->assign('confirmPasswordError', '<span class=formError>'.$pLang['paConfirmPassword_error'].'</span>');
				}
			}
		}

		if(SmartyValidate::is_valid($_POST) && $form_error == 0) {
			// updating information so clear cache
			$p->clear_cache(RAIDER_TEMPLATE_PATH.'profile.tpl');

			// no errors, done with SmartyValidate
			SmartyValidate::disconnect();

			// update database
			$sql["UPDATE"] = "profile";
			$sql["VALUES"] = array('user_email' => $_POST['user_email']);
			$sql["WHERE"] = "profile_id = ".$pMain->getProfileID();
			$db_raid->set_query('update', $sql, __FILE__, __LINE__);

			if(!empty($_POST['new_password'])) {
				$sql["UPDATE"] = "profile";
				$sql["VALUES"] = array('password' => md5($_POST['new_password']));
				$sql["WHERE"] = "profile_id = ".$pMain->getProfileID();
				$db_raid->set_query('update', $sql, __FILE__, __LINE__);
			}

			pRedirect('index.php?option='.$option);
		} else {
			// error, redraw the form
			$p->assign($_POST);
			$p->display(RAIDER_TEMPLATE_PATH.'profile.tpl');
		}
	}
} else {
	printError($pLang['invalidOption']);
}
?>