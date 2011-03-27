<?php
// no direct access
defined('_VALID_SETUP') or die('Restricted Access');

function in_Open_Basedir_path($path, $open_basedir) {
	$result = false;
	$open_basedirs = explode(PATH_SEPARATOR,strtolower($open_basedir));
	$path = strtolower($path);

	foreach ($open_basedirs as $dir) {
		if ($dir == '.') {
			$dir = strtolower(dirname(__FILE__));
		}
		if (substr($dir,-1)!=DIRECTORY_SEPARATOR) {
			$dir .= DIRECTORY_SEPARATOR;
		}
		if (strpos($path, $dir) === 0) {
			$result = true;
			break;
		}
	}
	return $result;
}

// introduction file
if($task == '') {
	$base_path = str_replace('install', '', dirname(__FILE__));
	$open_basedir = ini_get('open_basedir');

	in_Open_Basedir_path('C:\\Webdev\\Site\\phpRaider_svn\\trunk\\install\\', $open_basedir);

	if (!$open_basedir || in_Open_Basedir_path($base_path, $open_basedir)) {
		$phpraider = '';
		if(is_writable($base_path.'templates_c')) {
			$templates = '<div style="color: lime">The <strong>templates/</strong> directory is writeable.</div>';
		} else {
			$templates = '<div style="color: red">The <strong>templates/</strong> directory IS NOT writable!</div>';
		}
		if(is_writable($base_path.'cache')) {
			$cache = '<div style="color: lime">The <strong>cache/</strong> directory is writeable.</div>';
		} else {
			$cache = '<div style="color: red">The <strong>cache/</strong> directory IS NOT writable!</div>';
		}
		if (file_exists($base_path.'configuration.php')) {
			if(is_writable($base_path.'configuration.php')) {
				$configuration = '<div style="color: lime">The <strong>configuration.php</strong> file is writeable.</div>';
			} else {
				$configuration = '<div style="color: red">The <strong>configuration.php</strong> file IS NOT writable!</div>';
			}
		} else {
			// File doesn't exist, file should be writeable if the directory is writeable.
			if(is_writable($base_path)) {
				$configuration = '<div style="color: lime">The <strong>configuration.php</strong> file is writeable.</div>';
			} else {
				$configuration = '<div style="color: red">The <strong>configuration.php</strong> file doesn\'t exist!</div>';
				$phpraider = '<div style="color: red">The <strong>phpRaider/</strong> directory IS NOT writable!</div>';
			}
		}
	} else {
		$phpraider = '<div style="color: red">PHP\'s <a href="http://www.php.net/manual/en/ini.core.php#ini.open-basedir">open_basedir</a> restrictions is set to "'.$open_basedir.'" this will interfer with phpRaider, please add "'.$base_path.'" to your open_basedir setting.</div>';
		$templates = '';
		$cache = '';
		$configuration = '';
	}

	// Get the path for php.ini for later use.
	$php_configuration_file = get_cfg_var('cfg_file_path');

	// Checks the error reporting settings.
	$errorReporting = error_reporting();
	$errorReportingNotice = $errorReporting & 8;
	if ($errorReportingNotice != 0) {
		$php_error_reporting = '<div style="color: red">Your PHP setup is reporting notices as well as errors. This is a development setting in PHP.</div>';
	} else {
		$php_error_reporting = '';
	}

	// Check if session cookies are enabled
	$session_use_only_cookies = ini_get('session.use_only_cookies');
	if ($session_use_only_cookies == 1) {
		$session_cookie = '<div style="color: lime">Session cookies are used.</div>';
		$session_trans_sid = '<div style="color: lime">Session trans_sid are not used.</div>';
	} else {
		$session_cookie_enabled = ini_get('session.use_cookies');
		if ($session_cookie_enabled == 1) {
			$session_cookie = '<div style="color: lime">Session cookies are used.</div>';
		} else {
			$session_cookie = '<div style="color: red">Session cookies are disabled.!</div>';
		}
		$session_trans_sid_enabled = ini_get('session.use_trans_sid');
		if ($session_trans_sid_enabled == 1) {
			$session_trans_sid = '<div style="color: red">Session trans_sid are used, this setting isn\'t recomended,since it\'s a safety risk.</div>';
		} else {
			$session_trans_sid = '<div style="color: lime">Session trans_sid are not used.</div>';
		}
	}

	// Check if session save path is configured.
	$session_save_path = ini_get('session.save_path');
	if (!empty($session_save_path)) {
		// Session save path is defined, let's check if it's writeable.
		if (!preg_match('/\\'.DIRECTORY_SEPARATOR.'$/',$session_save_path)) {
			$session_save_path .= DIRECTORY_SEPARATOR;
		}
		if (!$open_basedir || in_Open_Basedir_path($session_save_path, $open_basedir)) {
			if (file_exists($session_save_path)) {
				if( is_writable($session_save_path)) {
					$session_path = '<div style="color: lime">The session save path <strong>'.$session_save_path.'</strong> directory is writeable.</div>';
				} else {
					$session_path = '<div style="color: red">The session save path <strong>'.$session_save_path.'</strong> directory IS NOT writable!</div>';
				}
			} else {
				$session_path = '<div style="color: red">The session save path <strong>'.$session_save_path.'</strong> directory doesn\'t exist!</div>';
			}
		} else {
			$session_path = '<div style="color: orange">Can not determine if session save path <strong>'.$session_save_path.'</strong> is writeable because <a href="http://www.php.net/manual/en/ini.core.php#ini.open-basedir">open_basedir</a> restricts the allowed php paths to "'.$open_basedir.'"</div>';
		}
	} else {
		// Session save path not defined.
		$session_path = '<div style="color: red">The session save path is not defined in your php.ini '.(empty($php_configuration_file)?'':'('.$php_configuration_file.') ').'file!</div>';
	}

	// Check if register_globals is enabled or disabled.
	if (ini_get('register_globals')) {
		$register_globals = '<div style="color: red">register_globals is on. For your own safety, it\'s better to turn this option off.</div>';
	 } else {
		$register_globals = '<div style="color: lime">register_globals is off.</div>';
	 }

	$p->assign(
		array(
			'templates' => $templates,
			'cache' => $cache,
			'phpraider' => $phpraider,
			'configuration' => $configuration,
			'php_error_reporting' => $php_error_reporting,
			'session_cookie' => $session_cookie,
			'session_trans_sid' => $session_trans_sid,
			'session_path' => $session_path,
			'register_globals' => $register_globals,
			'next_option'=>$next_option
		)
	);
	$p->display('1.tpl');
} else {
	printError($pLang['invalidOption']);
}
?>