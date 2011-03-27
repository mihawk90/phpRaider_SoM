<?php
// no direct access
defined('_VALID_SETUP') or die('Restricted Access');

// introduction file
if($task == '') {
	if(empty($_POST)) {
		// setup defaults (crude)
		$_POST['pConfig_db_server'] = 'localhost';
		$_POST['pConfig_db_name'] = 'phpraider';
		$_POST['pConfig_db_prefix'] = 'phpraider_';

		$p->assign($_POST);

		// new form, we (re)set the session data
		SmartyValidate::connect($p, true);

		// register our validators
		SmartyValidate::register_validator('hostname', 'pConfig_db_server', 'notEmpty', false, false, 'trim');
		SmartyValidate::register_validator('name', 'pConfig_db_name', 'notEmpty', false, false, 'trim');
		SmartyValidate::register_validator('username', 'pConfig_db_user', 'notEmpty', false, false, 'trim');
		SmartyValidate::register_validator('prefix', 'pConfig_db_prefix', 'notEmpty', false, false, 'trim');

		// display form
		$p->display($option.'.tpl');
	} else {
		// validate after a POST
		SmartyValidate::connect($p);

		$error = 0;

		// verify that database connection succeeds
		$link = @mysql_connect($_POST['pConfig_db_server'], $_POST['pConfig_db_user'], $_POST['pConfig_db_pass']);

		if(!$link) {
			$error = 1;
			$error_msg = '<div class="errorBody">Unable to connect to database!<br>'.mysql_error().' ('.mysql_errno().')</div>';
		} else {
			if(!mysql_select_db($_POST['pConfig_db_name'])) {
				$error = 1;
				$error_msg = "<div class=errorBody>Unable to select database {$_POST['pConfig_db_name']}. Verify that it exists!</div>";
			} else {
				if (function_exists('mysql_get_server_info')) {
					$mysqlVersion = mysql_get_server_info($link);
				} else {
					$result = @mysql_query('SELECT version()');
					$mysqlVersion = @mysql_result($result,0);
					@mysql_free_result($result);
				}
				if ($mysqlVersion) {
					$myVersion = explode('.',$mysqlVersion);
					if ($myVersion[0]<4) {
						$error = 1;
						$error_msg = "<div class=errorBody>The database server at {$_POST['pConfig_db_server']} is version: {$mysqlVersion}, needs to be at least 4.1.</div>";
					}
					elseif ($myVersion[0]==4 && $myVersion[1]<1) {
						$error =1;
						$error_msg = "<div class=errorBody>The database server at {$_POST['pConfig_db_server']} is version: {$mysqlVersion}, needs to be at least 4.1.</div>";
					}
				}
			}
		}

		if($error)
			$p->assign($_POST);

		if ($link) {
			mysql_close($link);
		}

		if(SmartyValidate::is_valid($_POST) && $error == 0) {
			// no errors, done with SmartyValidate
			SmartyValidate::disconnect();

			foreach($_POST as $key => $value)
				$_SESSION[$key] = $value;

			// make sure pConfig_db_pers is saved also.
			if (isset($_POST['pConfig_db_pers'])) {
				$_SESSION['pConfig_db_pers'] = 1;
			} else {
				$_SESSION['pConfig_db_pers'] = 0;
			}

			if (isset($_POST['pConfig_db_newlink'])) {
				$_SESSION['pConfig_db_newlink'] = 1;
			} else {
				$_SESSION['pConfig_db_newlink'] = 0;
			}

			header('Location: install.php?option='.$next_option);
			exit;
		} else {
			// error, redraw the form
			$p->assign('error',$error_msg);
			$p->assign('next_option',$next_option);
			$p->display($option.'.tpl');
		}
	}
} else {
	printError($pLang['invalidOption']);
}
?>