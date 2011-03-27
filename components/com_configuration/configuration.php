<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// load footer?
$load_footer = 1;

// verify permissions
if(!$pMain->checkPerm('edit_configuration')) {
	pRedirect('index.php?option=com_login&task=login');
}

if(empty($task) || $task == '') {
	// no caching for this
	$p->caching = false;

	if(empty($_POST)) {
		// setup default values
		$sql["SELECT"] = "*";
		$sql["FROM"] = "config";
		$db_raid->set_query('select', $sql, __FILE__, __LINE__);

		while($data = $db_raid->fetch()) {
			$p->assign('pConfig_'.$data['name'], $data['value']);
		}
	}

	// connect to server to check for updates
	if (function_exists('curl_init')) {
		// create a new cURL resource
		$url = curl_init();

		// set URL and other appropriate options
		curl_setopt($url, CURLOPT_URL, 'http://www.phpraider.com/updatecheck/1xx.txt');
		curl_setopt($url, CURLOPT_RETURNTRANSFER, 1);

		// Check if we got a good page back
		if (curl_getinfo($url, CURLINFO_HTTP_CODE) == 200) {
			// grab URL
			$remote = curl_exec($url);
			$remote = explode('.', $remote);
		}
		// close cURL resource, and free up system resources
		curl_close($url);
	} else {
		$url = 'http://www.phpraider.com/updatecheck/1xx.txt';
		$remote = @file_get_contents($url);
		if ($remote == false) {
			unset($remote);
		} else {
			$remote = explode('.', $remote);
		}
	}

	// check local
	include(RAIDER_BASE_PATH.'version.php');
	$local = explode('.', $version);

	$update = 0;
	// do the check
	if (isset($remote)) {
		if($local[0] < $remote[0]) {
			$update = 1;
		} else {
			if($local[1] < $remote[1]) {
				$update = 1;
			} else {
				if($local[2] < $remote[2]) {
					$update = 1;
				}
			}
		}

		// setup message
		if($update) {
			$update = sprintf($pLang['coUpdate'], 'http://www.phpraider.com/index.php?action=tpmod;dl=0',
			$remote[0], $remote[1], $remote[2], $local[0], $local[1], $local[2]);
		} else {
			$update = $pLang['coNoUpdate'];
		}
	} else {
		$update = sprintf($pLang['coUpdateUndetermined'], 'http://www.phpraider.com/index.php?action=tpmod;dl=0');
	}

	$p->assign('update_check', $update);

	// setup game list
	if(is_dir(RAIDER_GAME_BASE_PATH)) {
		$dh = opendir(RAIDER_GAME_BASE_PATH);

		$files = array();
		while(false != ($filename = readdir($dh))) {
			if (preg_match("/^[0-9a-z]+$/si", $filename) == 1) {
				if (is_dir(RAIDER_GAME_BASE_PATH.$filename)) {
					$files[] = $filename;
				}
			}
		}

		sort($files);

		$games = '<option value="">'.$pLang['none'].'</option>';

		foreach($files as $value) {
			if($value == $pConfig['game'])
				$selected = ' selected';
			else
				$selected = '';

			$games .= '<option value="'.$value.'"'.$selected.'>'.$value.'</option>';
		}

		$p->assign('games', $games);
	}

	// setup language list
	$dh = opendir(RAIDER_LANGUAGE_PATH);
	$files = array();
	while(false != ($filename = readdir($dh))) {
		if (!is_dir(RAIDER_LANGUAGE_PATH.$filename)) {
			if (preg_match("/^([0-9a-z]+).php$/si", $filename, $language) == 1) {
				$files[] = $language[1];
			}
		}
	}
	sort($files);

	$language = '';
	foreach($files as $value) {
		if($value == $pConfig['language'])
			$selected = ' selected';
		else
			$selected = '';

		if (!empty($value)) {
			$language .= '<option value="'.$value.'"'.$selected.'>'.$value.'</option>';
		}
	}

	$p->assign('language', $language);

	// setup template list
	$files = array();
	$dh = opendir(RAIDER_TEMPLATE_BASE_PATH);
	while(false != ($filename = readdir($dh))) {
		if (is_dir(RAIDER_TEMPLATE_BASE_PATH.$filename)) {
			if (preg_match("/^[^\.]/si", $filename) == 1) {
				$files[] = $filename;
			}
		}
	}

	sort($files);

	$templates = '';
	foreach($files as $value) {
		if($value == $pConfig['template'])
			$selected = ' selected';
		else
			$selected = '';

		$templates .= '<option value="'.$value.'"'.$selected.'>'.$value.'</option>';
	}

	$p->assign('templates', $templates);

	unset($files);

	// First Day of the Week
	$firstdayofweek = '';
	if (empty($pConfig['first_day_of_week']))
		$pConfig['first_day_of_week'] = 0;

	foreach($pLang['coSiDay'] as $value=>$text){
		if($value == $pConfig['first_day_of_week'])
			$selected = ' selected';
		else
			$selected = '';
		$firstdayofweek .= '<option value="'.$value.'"'.$selected.'>'.$text.'</option>';
	}

	$p->assign('firstdayofweek', $firstdayofweek);

	// setup auth list
	$dh = opendir(RAIDER_BASE_PATH.'authentication');
	while(false != ($filename = readdir($dh))) {
		$files[] = $filename;
	}

	sort($files);
	array_shift($files);
	array_shift($files);

	$auth = '';

	foreach($files as $value) {
		if($value == $pConfig['authentication'])
			$selected = ' selected';
		else
			$selected = '';

		$auth .= '<option value="'.$value.'"'.$selected.'>'.$value.'</option>';
	}

	$p->assign('auth', $auth);

	unset($files);

	$group = '';

	// setup groups
	$sql["SELECT"] = "*";
	$sql["FROM"] = "groups";
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	$group = '<option value="0"> '.$pLang['coDefault'].'</option>';

	while($data = $db_raid->fetch()) {
		if($pConfig['default_group'] == $data['group_id'])
			$selected = 'selected';
		else
			$selected = '';
		$group .= '<option value="'.$data['group_id'].'"'.$selected.'>'.$data['group_name'].'</option>';
	}

	$p->assign('group', $group);

	// timezones
	$timezoneOption = '';
	for($i = -12; $i <= 12; $i = $i + 0.5) {
		if($i == 12 || $i == 11 || $i == 10 || $i == 9.5 || $i == 9 || $i == 8 || $i == 7 || $i == 6.5 || $i == 6 ||
		   $i == 5.5 || $i == 5 || $i == 4.5 || $i == 4 || $i == 3.5 || $i == 3 || $i == 2 || $i == 1 || $i == 0 ||
		   $i == -12 || $i == -11 || $i == -10 || $i == -9 || $i == -8 || $i == -7 || $i == -6 || $i == -5 || $i == -4 ||
		   $i == -3.5 || $i == -3 || $i == -2|| $i == -1) {

			if($i < 0)
				$format = '- '.abs($i).' '.$pLang['hours'];
			elseif($i > 0)
				$format = '+ '.$i.' '.$pLang['hours'];
			else
				$format = '0';

			if($pConfig['timezone'] != ($i * 100))
				$timezoneOption .= '<option value="'.($i * 100).'">'.$format.'</option>';
			else
				$timezoneOption .= '<option value="'.($i * 100).'" SELECTED>'.$format.'</option>';
		}
	}

	$p->assign('timezone', $timezoneOption);

	$p->assign('siCurrentTime', newDate($pConfig['time_format'], time(), 0));
	$p->assign('siSetTime', newDate($pConfig['time_format'], time(), $pConfig['timezone'] + $pConfig['dst']));
	$p->assign('siLocalText', $pLang['coLocal_text']);

	// localizations
	$p->assign(
		array(
			// headers
			'database_header' => $pLang['coDB_header'],
			'game_header' => $pLang['coGame_header'],
			'misc_header' => $pLang['coMisc_header'],
			'site_header' => $pLang['coSite_header'],
			'raid_header' => $pLang['coRaid_header'],
			'update_header' => $pLang['coUpdate_header'],

			// form text
			'dbServer_text' => $pLang['coServer_text'],
			'dbName_text' => $pLang['coDBName_text'],
			'dbUser_text' => $pLang['coDBUser_text'],
			'dbPass_text' => $pLang['coDBPass_text'],
			'dbPersistent_text' => $pLang['coDBPers_text'],
			'dbPrefix_text' => $pLang['coDBPrefix_text'],

			'gaGame_text' => $pLang['coGame_text'],
			'gaMaxLvl_text' => $pLang['coGAMaxLvL_text'],
			'gaMinLvl_text' => $pLang['coGAMinLvl_text'],
			'gaMinRaiders_text' => $pLang['coGAMinRaiders_text'],
			'gaMaxRaiders_text' => $pLang['coGAMaxRaiders_text'],
			'gaMultiClass_text' => $pLang['coGAMultiClass_text'],

			'siLanguage_text' => $pLang['coSiLanguage_text'],
			'siTemplate_text' => $pLang['coSiTemplate_text'],
			'siAuth_text' => $pLang['coSiAuth_text'],
			'siFirstDayOfWeek_text' => $pLang['coSiFirstDayOfWeek_text'],
			'siDateFormat_text' => $pLang['coSiDateFormat_text'],
			'siTimeFormat_text' => $pLang['coSiTimeFormat_text'],
			'siTimezone_text' => $pLang['coSiTimezone_text'],
			'siDst_text' => $pLang['coSiDst_text'],
			'siRegister_text' => $pLang['coSiRegister_text'],
			'siAdmin_text' => $pLang['coSiAdmin_text'],
			'siAdminEmail_text' => $pLang['coSiAdminEmail_text'],
			'siURL_text' => $pLang['coSiUrl_text'],
			'siConfigureAuth' => $pLang['coSiAuthConfigure_text'],

			'miAnon_text' => $pLang['coMiAnon_text'],
			'miQueue_text' => $pLang['coMiQueue_text'],
			'miDebug_text' => $pLang['coMiDebug_text'],
			'miDefaultGroup_text' => $pLang['coMiDefaultGroup_text'],
			'miDisable_text' => $pLang['coMiDisable_text'],
			'miFreeze_text' => $pLang['coMiFreeze_text'],
			'miReport_text' => $pLang['coMiReport_text'],

			// form errors

			// buttons
			'reset' => $pLang['reset'],
			'submit' => $pLang['submit']
		)
	);

	if(!defined("DISALLOW_GAME_INSTALL"))
		$p->assign('installGame_text','<a href="index.php?option=com_configuration&amp;task=install_game">'.$pLang['coGameInstall_text'].'</a>');

	if(empty($_POST)) {
		// new form, we (re)set the session data
		SmartyValidate::connect($p, true);

		// register our validators
		SmartyValidate::register_validator('server', 'pConfig_db_server', 'notEmpty', false, false, 'trim');
		SmartyValidate::register_validator('name', 'pConfig_db_name', 'notEmpty', false, false, 'trim');
		SmartyValidate::register_validator('user', 'pConfig_db_user', 'notEmpty', false, false, 'trim');
		SmartyValidate::register_validator('prefix', 'pConfig_db_prefix', 'notEmpty', false, false, 'trim');
		SmartyValidate::register_validator('min_level', 'pConfig_min_level', 'notEmpty', false, false, 'trim');
		SmartyValidate::register_validator('max_level', 'pConfig_max_level', 'notEmpty', false, false, 'trim');
		SmartyValidate::register_validator('min_raiders', 'pConfig_min_raiders', 'notEmpty', false, false, 'trim');
		SmartyValidate::register_validator('max_raiders', 'pConfig_max_raiders', 'notEmpty', false, false, 'trim');
		SmartyValidate::register_validator('date_format', 'pConfig_date_format', 'notEmpty', false, false, 'trim');
		SmartyValidate::register_validator('time_format', 'pConfig_time_format', 'notEmpty', false, false, 'trim');
		SmartyValidate::register_validator('timezone', 'pConfig_timezone', 'notEmpty', false, false, 'trim');
		SmartyValidate::register_validator('admin', 'pConfig_admin_name', 'notEmpty', false, false, 'trim');
		SmartyValidate::register_validator('admin_email', 'pConfig_admin_email', 'notEmpty', false, false, 'trim');
		SmartyValidate::register_validator('site_url', 'pConfig_site_url', 'notEmpty', false, false, 'trim');
		SmartyValidate::register_validator('report_max', 'pConfig_report_max', 'notEmpty', false, false, 'trim');

		// display form
		$p->display(RAIDER_TEMPLATE_PATH.'configuration.tpl');
	} else {
		// give empty checkboxes a value
		if(empty($_POST['pConfig_dst'])) $_POST['pConfig_dst'] = 0;
		if(empty($_POST['pConfig_allow_anonymous'])) $_POST['pConfig_allow_anonymous'] = 0;
		if(empty($_POST['pConfig_auto_queue'])) $_POST['pConfig_auto_queue'] = 0;
		if(empty($_POST['pConfig_debug_mode'])) $_POST['pConfig_debug_mode'] = 0;
		if(empty($_POST['pConfig_disable_site'])) $_POST['pConfig_disable_site'] = 0;
		if(empty($_POST['pConfig_disable_freeze'])) $_POST['pConfig_disable_freeze'] = 0;
		if(empty($_POST['pConfig_multi_class'])) $_POST['pConfig_multi_class'] = 0;

		foreach($_POST as $key=>$value) {
			if (!preg_match("/^pConfig\_db\_/si", $key)) {
				$sql["REPLACE"] = "config";
				$sql["VALUES"] = array(
									'value'=>$value,
									'name'=>substr($key, 8, strlen($key)-3)
								);

				$db_raid->set_query('replace', $sql, __FILE__, __LINE__);
			}
		}

		pRedirect("index.php?option=com_configuration");
	}
} else if($task == 'install_game') {
	if(!defined("DISALLOW_GAME_INSTALL")) {
		// no caching for this
		$p->caching = false;

		if (extension_loaded('zip')) {
			$pZipSupport = True;

			if(is_writable(RAIDER_GAME_BASE_PATH))
				$games = $pLang['coGamesWritable'];
			else
				$games = $pLang['coGamesNotWritable'];
			// localizations
			$p->assign(
				array(
					// text
					'header' => $pLang['coInstallGame_header'],
					'fileName_text' => $pLang['coInstallGameFileName_text'],
					'game' => $games,

					// task
					'task' => 'install_game',

					// buttons
					'reset' => $pLang['reset'],
					'submit' => $pLang['submit'],

					// Zip support
					'zip_support' => True
				)
			);

			if(empty($_FILES)) {
				// new form, we (re)set the session data
				SmartyValidate::connect($p, true);

				// display form
				$p->display(RAIDER_TEMPLATE_PATH.'configuration_install_game.tpl');

			} else {
				if (empty($_FILES['game_file']['error'])) {
					$_FILES['game_file']['error'] = 0;
				}
				if ($_FILES['game_file']['error'] == 0 && empty($_FILES['game_file']['tmp_name'])) {
					$_FILES['game_file']['error'] = 6;
				}
				if ($_FILES['game_file']['error'] == UPLOAD_ERR_OK) {
					// unzip file
					unzip($_FILES['game_file']['tmp_name'], RAIDER_GAME_BASE_PATH, true, false);

					// execute SQL file
					$sqlErrors = sqlFromFile(RAIDER_GAME_BASE_PATH.'install.sql', 'index.php?option=com_configuration');
					if ($sqlErrors) {
						if (is_array($sqlErrors)) {
							foreach ($sqlErrors as $sqlError) {
								printError($sqlError['error_message'].' with SQL:<strong>'.$sqlError['sql'].'</strong>');
							}
						} else {
							printError($sqlErrors);
						}
					}

					// remove SQL file
					unlink(RAIDER_GAME_BASE_PATH.'install.sql');
				} else {
					// Show error message.
					switch($_FILES['game_file']['error']){
						case UPLOAD_ERR_INI_SIZE:
							$errorMessage = sprintf($pLang['coInstallGame_error_upload'][$_FILES['game_file']['error']],get_cfg_var('upload_max_filesize'));
							break;
						case UPLOAD_ERR_FORM_SIZE:
							$errorMessage = sprintf($pLang['coInstallGame_error_upload'][$_FILES['game_file']['error']],$_POST['MAX_FILE_SIZE']);
							break;
						case UPLOAD_ERR_PARTIAL:
							$errorMessage = $pLang['coInstallGame_error_upload'][$_FILES['game_file']['error']];
							break;
						case UPLOAD_ERR_NO_FILE:
							$errorMessage = $pLang['coInstallGame_error_upload'][$_FILES['game_file']['error']];
							break;
						case UPLOAD_ERR_NO_TMP_DIR:
						case UPLOAD_ERR_CANT_WRITE:
							$uploadDir = get_cfg_var('upload_tmp_dir');
							$uploadDir = (empty($uploadDir)?getenv('temp'):$$uploadDir);
							$errorMessage = sprintf($pLang['coInstallGame_error_upload'][$_FILES['game_file']['error']], $uploadDir);
							break;
						case UPLOAD_ERR_EXTENSION:
							$errorMessage = $pLang['coInstallGame_error_upload'][$_FILES['game_file']['error']];
							break;
						default:
							$errorMessage = $pLang['coInstallGame_error_upload']['unknown'];
					} // switch
					printError($errorMessage);
				}
			}
		} else {
			// localizations
			$p->assign(
				array(
					// text
					'header' => $pLang['coInstallGame_header'],
					'zip_disabled' => $pLang['coGamesZipDisabled'],
					'manual_installation' => sprintf($pLang['coGamesManualInstall'],RAIDER_GAME_BASE_PATH)
				)
			);

			// display form
			$p->display(RAIDER_TEMPLATE_PATH.'configuration_install_game.tpl');
		}
	} else {
		pRedirect('index.php?option=com_configuration');
	}
} else {
	printError($pLang['invalidOption']);
}
?>
