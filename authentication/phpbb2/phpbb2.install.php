<?php
// SECURITY MEASURE DO NOT REMOVE!!
defined('_VALID_SETUP') or die('Restricted Access');

include(RAIDER_BASE_PATH.'authentication'.DIRECTORY_SEPARATOR.'phpbb2'.DIRECTORY_SEPARATOR.'phpbb2.configure.php');
include(RAIDER_BASE_PATH.'configuration.php');

// add to database
$link = mysql_connect($pConfig['db_server'], $pConfig['db_user'], $pConfig['db_pass']);

if(!$link) {
	$output .= '<font color=red>Unable to connect to database.</font><br>';
	$error = 1;
}

if(!mysql_select_db($pConfig['db_name']))
	$output .= '<font color=red>Unable to select database.</font><br>';
else
	$output .= '<font color=green>Database selected.</font><br>';

$result = mysql_query("SELECT * FROM {$pConfig['db_prefix']}profile");
$count = mysql_num_rows($result);

// setup output
$output = '<form method="post" action="install.php?option='.$i.'">';

if($count == 0)
	$output .= '<strong>THE FIRST USER TO VISIT PHPRAIDER AFTER LOGGING INTO THE FORUMS WILL BE SET AS AN ADMINISTRATOR!!!</strong><br><br>';

// each field for configuration
$output .= "<div class=contentHeader>Setup Authentication Method</div><br>
				<div class=contentBody>
					<div align=center>
						<table width=100% cellpadding=3 cellspacing=1 border=0>";

for($j=0; $j<count($auth_option); $j++) {
	if(empty($_POST[$auth_option[$j]['variable']])) {
		$name = $auth_option[$j]['text'];
	} else {
		$name = $_POST[$auth_option[$j]['variable']];
	}

	$output .= '<tr>
					<td width="30%" valign="top"><div align="right"><strong>'.$auth_option[$j]['text'].':</strong><br>'.$auth_option[$j]['description'].'</div></td>
					<td width="70%" valign="top">
						<div align="left">
							<input type="text" name="'.$auth_option[$j]['variable'].'" value="'.$auth_option[$j]['value'].'" class="post" style="width:200px">
						</div>
					</td>
				</tr>';
}

$output .= "</table></div><br></div><div align=center><br><input type=submit name=submit value=Submit class=mainoption></div>";

// introduction file
if($task == '') {
	if(empty($_POST)) {
		$p->assign($_POST);
		$p->assign('output', $output);

		// display form
		$p->display($option.'.tpl');
	} else {
		// add to database
		$link = mysql_connect($pConfig['db_server'], $pConfig['db_user'], $pConfig['db_pass']);

		if(!$link) {
			$output .= '<font color=red>Unable to connect to database.</font><br>';
			$error = 1;
		}

		if(!mysql_select_db($pConfig['db_name']))
			$output .= '<font color=red>Unable to select database.</font><br>';
		else
			$output .= '<font color=green>Database selected.</font><br>';

		// do the auth options
		for($j=0;$j<count($auth_option);$j++) {
			$sql = sprintf("INSERT INTO %sconfig_auth"
				. "\n (`name`, `value`)"
				. "\n VALUES ('%s','%s');"
				,$pConfig['db_prefix'],$auth_option[$j]['variable'],(get_magic_quotes_gpc())?$_POST[$auth_option[$j]['variable']]:addslashes($_POST[$auth_option[$j]['variable']]));
			mysql_query($sql) or die(mysql_error());
		}

		// do the auth required
		foreach($auth_default as $key=>$value) {
			$sql = sprintf("INSERT INTO %sconfig_auth"
				. "\n (`name`, `value`)"
				. "\n  VALUES ('%s','%s');"
				,$pConfig['db_prefix'],$key,$value);
			mysql_query($sql) or die(mysql_error());
		}

		mysql_close($link);

		header('Location: install.php?option='.$next_option);
		exit;
	}
} else {
	printError($pLang['invalidOption']);
}
?>