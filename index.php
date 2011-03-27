<?php
// set flag for parent component
define('_VALID_RAID',1);

// setup base paths (added in 1.0.2 to simplify directories)
define('RAIDER_BASE_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('RAIDER_INCLUDE_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR);
define('RAIDER_CLASS_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR);
define('RAIDER_LANGUAGE_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR);
define('RAIDER_PLUGIN_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR);
define('RAIDER_COMPONENTS_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR);

if(!defined("RAIDER_TEMPLATE_BASE_PATH"))
	define('RAIDER_TEMPLATE_BASE_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR);

if(!defined("RAIDER_GAME_BASE_PATH"))
	define('RAIDER_GAME_BASE_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'games'.DIRECTORY_SEPARATOR);


// Begin PhpRaider Timing (Mordon)
$starttime = explode(' ', microtime() );
$pStart_time = $starttime[1]+$starttime[0];

// buffering
ob_start();

// checks for configuration file, if none found loads installation page
if(is_dir(RAIDER_BASE_PATH.'install'.DIRECTORY_SEPARATOR)) {
        $self = str_replace('/index.php','', $_SERVER['PHP_SELF']). '/';
        header("Location: http://".$_SERVER['HTTP_HOST'].$self."install/install.php");
        exit();
}

// include the basics
require_once(RAIDER_BASE_PATH.'configuration.php');
require_once(RAIDER_INCLUDE_PATH.'functions.compat.php');
require_once(RAIDER_INCLUDE_PATH.'mainframe.php');
require_once(RAIDER_INCLUDE_PATH.'functions.character.php');
require_once(RAIDER_INCLUDE_PATH.'functions.date.php');
require_once(RAIDER_INCLUDE_PATH.'functions.db.php');
require_once(RAIDER_INCLUDE_PATH.'functions.generic.php');
require_once(RAIDER_INCLUDE_PATH.'functions.raid.php');

// connect to database server
include(RAIDER_BASE_PATH.'includes'.DIRECTORY_SEPARATOR.'database.php');
global $db_raid;
$db_raid = new pr_sql_db($pConfig['db_server'], $pConfig['db_user'], $pConfig['db_pass'], $pConfig['db_name'], $pConfig['db_prefix'], $pConfig['db_pers'],(isset($pConfig['db_newlink'])?$pConfig['db_newlink']:false));
if ($db_raid->db_connect_id!=false) {
	// unset database password for security reasons
	// we won't use it after this point
	unset($pConfig['db_pass']);

	// load configuration variables into configuration array
	$sql['SELECT'] = '*';
	$sql['FROM'] = 'config_auth';
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	while($data = $db_raid->fetch()) {
		$pConfig_auth[$data['name']] = $data['value'];
	}

	// load configuration authentication variables into configuration array
	$sql['SELECT'] = '*';
	$sql['FROM'] = 'config';
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	while($data = $db_raid->fetch()) {
		$pConfig[$data['name']] = $data['value'];
	}

	// paths requiring configuration variables
	define('RAIDER_TEMPLATE_PATH', RAIDER_TEMPLATE_BASE_PATH.$pConfig['template'].DIRECTORY_SEPARATOR);
	define('RAIDER_AUTH_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'authentication'.DIRECTORY_SEPARATOR.$pConfig['authentication'].DIRECTORY_SEPARATOR);
	define('RAIDER_GAME_PATH', RAIDER_GAME_BASE_PATH.$pConfig['game'].DIRECTORY_SEPARATOR);

	// if language file doesn't exist load default english
	// and show nice error
	if(!is_file(RAIDER_LANGUAGE_PATH.$pConfig['language'].'.php')) {
		$errorMsg = 'Language file: "'.RAIDER_LANGUAGE_PATH.$pConfig['language'].'.php" ';
		$errorMsg .= 'could not be located! Loading fail-safe language file. Check your configuration settings.';

		printError($errorMsg);

		$pConfig['language'] = 'english';
	}

	include_once(RAIDER_LANGUAGE_PATH.$pConfig['language'].'.php');

	// setup smarty template object
	include(RAIDER_CLASS_PATH.'smarty'.DIRECTORY_SEPARATOR.'Smarty.class.php');
	include(RAIDER_CLASS_PATH.'smarty'.DIRECTORY_SEPARATOR.'SmartyMenu.class.php');
	include(RAIDER_CLASS_PATH.'smarty'.DIRECTORY_SEPARATOR.'SmartyValidate.class.php');

	// phpMailer
	include(RAIDER_CLASS_PATH.'phpmailer'.DIRECTORY_SEPARATOR.'class.phpmailer.php');

	global $pMail;
	$pMail = new PHPMailer();

	global $p;
	$p = new Smarty;
	$p->caching = false;
	$p->cache_dir = 'cache';
	$p->template_dir = RAIDER_TEMPLATE_PATH;

	if (!empty($pConfig['authentication'])) {
		// authentication
		session_start();
		require(RAIDER_AUTH_PATH.$pConfig['authentication'].'.functions.php');

		// setup phpRaider mainframe
		if (!isset($pMain)) {
			$pMain = new Mainframe($_SESSION);
		}

		// extract our variables
		$option = (empty($_GET['option'])?'':$_GET['option']);
		$task = (empty($_GET['task'])?'':$_GET['task']);
		$id = (isset($_GET['id'])?intval($_GET['id']):false);

		if (!empty($option)) {
			// load module language if it exists.
			if (file_exists(RAIDER_LANGUAGE_PATH.substr($option, 4).'.'.$pConfig['language'].'.php')) {
				include_once(RAIDER_LANGUAGE_PATH.substr($option, 4).'.'.$pConfig['language'].'.php');
			}
		}

		// set user permissions
		if($pMain->getLogged()) {
			$sql['SELECT'] = '*';
			$sql['FROM'] = 'permissions';
			$sql['WHERE'] = 'group_id='.$pMain->getGroupID();
			$db_raid->set_query('select', $sql, __FILE__, __LINE__);

			while($perm = $db_raid->fetch()) {
				$pMain->setPerm($perm['permission_name']);
			}
		}

		// load plugins
		$dh = opendir(RAIDER_PLUGIN_PATH);
		while(false != ($filename = readdir($dh))) {
			if (preg_match("/^plugin\_/si", $filename) == 1) {
				if (is_dir(RAIDER_PLUGIN_PATH.$filename)) {
					$files[] = $filename;
				}
			}
		}

		sort($files);

		foreach($files as $value) {
			include(RAIDER_PLUGIN_PATH.$value.DIRECTORY_SEPARATOR.substr($value, 7).'.php');
		}

		unset($files);

		// load tiny MCE?
		switch($option){
			case '':
			case 'com_view':
			case 'com_frontpage':
				$load_tiny = 0;
				break;
			default:
				$load_tiny = intval((isset($pConfig['enable_tiny']))?$pConfig['enable_tiny']:'1');
		}

		// header
		if($option != 'com_signup') {
			include(RAIDER_BASE_PATH.'header.php');
		}

		// check if site is offline
		if($pConfig['disable_site'] && $option != 'com_login') {
			if($pMain->checkPerm('edit_configuration')) {
				echo '<div align="center" style="background-color:white; border:1px solid red">
					<font color=red><strong>'.$pLang['offlineMode'].'</strong></font></div><br>';
			} else {
				// kill the site
				printError($pLang['offlineTitle'], $pLang['offlineMessage']);
			}
		}

		// clear SQL in case another script uses it
		unset($sql);
		if (empty($option)) {
			// load default component
			include(RAIDER_COMPONENTS_PATH.'com_frontpage'.DIRECTORY_SEPARATOR.'frontpage.php');
		} else {
			// load called component
			if(file_exists(RAIDER_COMPONENTS_PATH.$option)) {
				include(RAIDER_COMPONENTS_PATH.$option.DIRECTORY_SEPARATOR.substr($option, 4).'.php');
			} else {
				// invalid component
				printError($pLang['moduleTitle'], $pLang['moduleError']);
			}
		}

		if($load_footer) {
			include(RAIDER_BASE_PATH.'footer.php');
		}
	} else {
		// No auth method choosen, faulty installation?
		printError($pLang['authenticationTitle'], $pLang['authenticationNoModuleError']);
	}
} else {
	// show database error message
	$error = $db_raid->sql_error();
	die('Database Connection Error: '.$error['message']);
}
?>