<?php
// javascript information
$document_ready = array();
$scripts = '';
$scripts .= '<script type="text/javascript" src="'.$pConfig['site_url'].'/includes/scripts/phpraider/phpraider.js" language="javascript"></script>'."\n";
$scripts .= '<script type="text/javascript" src="'.$pConfig['site_url'].'/includes/scripts/ajax/tooltips/ajax-dynamic-content.js" language="javascript"></script>'."\n";
$scripts .= '<script type="text/javascript" src="'.$pConfig['site_url'].'/includes/scripts/ajax/tooltips/ajax.js" language="javascript"></script>'."\n";
$scripts .= '<script type="text/javascript" src="'.$pConfig['site_url'].'/includes/scripts/ajax/tooltips/ajax-tooltip.js" language="javascript"></script>'."\n";
$scripts .= '<script type="text/javascript" src="'.$pConfig['site_url'].'/includes/scripts/jquery/jquery-1.4.2.min.js" language="javascript"></script>'."\n";
$scripts .= '<script type="text/javascript" src="'.$pConfig['site_url'].'/includes/scripts/jquery/jquery-ui-1.8.custom.min.js" language="javascript"></script>'."\n";

// jQuery UI Language
$language_file='';
if (file_exists(RAIDER_BASE_PATH.'includes/scripts/jquery/jquery.ui.datepicker-'.$pConfig['language'].'.js')) {
	$language_file = '/includes/scripts/jquery/jquery.ui.datepicker-'.$pConfig['language'].'.js';
} else {
	switch($pConfig['language']){
		case 'french':
			if (file_exists(RAIDER_BASE_PATH.'includes/scripts/jquery/jquery.ui.datepicker-fr.js')) {
				$language_file = '/includes/scripts/jquery/jquery.ui.datepicker-fr.js';
			}
			break;
		case 'german':
			if (file_exists(RAIDER_BASE_PATH.'includes/scripts/jquery/jquery.ui.datepicker-de.js')) {
				$language_file = '/includes/scripts/jquery/jquery.ui.datepicker-de.js';
			}
			break;
	}
}
if (!empty($language_file)) {
	$scripts .= '<script type="text/javascript" src="'.$pConfig['site_url'].$language_file.'" language="javascript"></script>'."\n";
}

// load tiny MCE?
if($load_tiny) {
	$scripts .= '<script type="text/javascript" src="'.$pConfig['site_url'].'/includes/scripts/wysiwyg/jquery.tinymce.js" language="javascript"></script>'."\n";
	array_push($document_ready,'$(\'textarea\').tinymce({script_url:\''.$pConfig['site_url'].'/includes/scripts/wysiwyg/tiny_mce.js\',theme:\'advanced\',content_css:\''.$pConfig['site_url'].'/templates/'.$pConfig['template'].'/style/wysiwyg.css\'});');
}
array_push($document_ready,'$(\'#date\').datepicker({firstDay:'.(empty($pConfig['first_day_of_week'])?0:$pConfig['first_day_of_week']).',dateFormat:\'mm/dd/yy\'})');
$scripts .= '<script type="text/javascript" language="javascript">
			$().ready(function() {
';
foreach ($document_ready as $dr) {
	$scripts .= '				'.$dr."\n";
}
$scripts .= '			});
		</script>';
$tooltip = '<div id="dhtmltooltip"></div><script type="text/javascript" src="'.$pConfig['site_url'].'/includes/scripts/tooltips/tooltips.js" language="javascript"></script>';

$p->assign(
	array(
		'javascript'=>$scripts,
		'tooltip'=>$tooltip,
		'confirm_delete'=>$pLang['confirm_delete']
	)
);
if (!empty($id)) {
	$p->assign ('id' , $id);
}

// setup login information
if($pMain->getLogged()) {
	$user_info = sprintf($pLang['userLogged'], $pMain->getUser(), 'index.php?option=com_login&amp;task=logout');
} else {
	$user_info = sprintf($pLang['userNotLogged'], 'index.php?option=com_login&amp;task=login', $pConfig_auth['register_url']);
	$user_info .= '<br><a href="index.php?option=com_password">'.$pLang['lostPassword'].'</a>';
}

$p->assign('user_info', $user_info);

// setup some generic template variables
$p->assign(
	array(
		'absolute_path' => RAIDER_BASE_PATH,
		'site_url' => $pConfig['site_url'],
		'template' => $pConfig['template']
	)
);

// output header template
$p->display(RAIDER_TEMPLATE_PATH.'header.tpl');
?>