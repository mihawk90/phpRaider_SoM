<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// load footer?
$load_footer = 1;

// no caching for this
$p->caching = false;

// localizations
$p->assign(
	array(
		'header' => $pLang['lpHeader'],

		// errors
		'emailError' => $pLang['lpEmail_error'],

		// text
		'emailText' => $pLang['lpEmail_text'],
		'success' => $pLang['lpSentSuccess'],

		// buttons
		'reset' => $pLang['reset'],
		'submit' => $pLang['submit']
	)
);

if(empty($_POST)) {
	// new form, we (re)set the session data
	SmartyValidate::connect($p, true);

	// register validators
	SmartyValidate::register_validator('email', 'user_email', 'isEmail', false, false, 'trim');

	// display form
	$p->display(RAIDER_TEMPLATE_PATH.'password.tpl');
} else {
	// validate after a POST
	SmartyValidate::connect($p);

	$form_error = 0;

	if(SmartyValidate::is_valid($_POST)) {
		$sql['SELECT'] = '*';
		$sql['FROM'] = 'profile';
		$sql['WHERE'] = 'user_email='.$db_raid->quote_smart($_POST['user_email']);
		$result = $db_raid->set_query('select', $sql, __FILE__, __LINE__);

		if($db_raid->sql_numrows() == 0) {
			$p->assign('error', '<br><div class=formError>'.$pLang['lpEmailNotFound_error'].'</div>');
			$form_error = 1;
		}
	} else {
		$form_error = 1;
	}

	if($form_error == 0) {
		// updating information so clear cache
		$p->clear_cache(RAIDER_TEMPLATE_PATH.'password.tpl');

		// no errors, done with SmartyValidate
		SmartyValidate::disconnect();

		// generate new password
		$new_password = generate_password();

		// get user information
		$data = $db_raid->sql_fetchrow($result);

		// setup mailer
		$subject = $pLang['lpSentSubject'];
		$body = sprintf($pLang['lpSentBody'], $pConfig['site_url'], $data['username'], $new_password, $pConfig['site_url']);

		// send out email
		pMailer($_POST['user_email'], $pConfig['admin_email'], $subject, $body);

		// update user password
		$sql['UPDATE'] = 'profile';
		$sql['VALUES'] = array('password'=>md5($new_password));
		$sql['WHERE'] = 'profile_id='.$db_raid->quote_smart($data['profile_id']);
		$db_raid->set_query('update', $sql, __FILE__, __LINE__);

		// localizations
		$p->assign(
			array(
				'header' => $pLang['lpSentHeader'],

				// text
				'emailText' => $pLang['lpEmail_text'],
				'sent' => $pLang['lpSentSuccess'],
			)
		);

		$p->display(RAIDER_TEMPLATE_PATH.'password_sent.tpl');
	} else {
		// error, redraw the form
		$p->assign($_POST);
		$p->display(RAIDER_TEMPLATE_PATH.'password.tpl');
	}
}
?>