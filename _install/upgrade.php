<?php
session_start();

// writes sql file after link established and db selected
function writesql($prefix) {
	$oldErrorReporting = error_reporting();
	//error_reporting(NONE);
	include(RAIDER_BASE_PATH.'version.php');
	$file = explode('.',$_POST['upgrade_file']);

	// get count of all install files
	$dir = RAIDER_BASE_PATH.'install'.DIRECTORY_SEPARATOR.'schema'.DIRECTORY_SEPARATOR.'upgrade';
	$dh = opendir($dir);
	while(false != ($filename = readdir($dh))) {
		$files[] = $filename;
	}

	sort($files);
	array_shift($files);
	array_shift($files);
	$sql_error = 0;
	for($i = 0; $i<count($files); $i++) {
		$filename = RAIDER_BASE_PATH.'install'.DIRECTORY_SEPARATOR.'schema'.DIRECTORY_SEPARATOR.'upgrade'.DIRECTORY_SEPARATOR.$file[0].'.'.$file[1].'.'.$file[2].'.sql';
		if (file_exists($filename)) {
			echo 'Running file: '.$filename . '<br>';
			$sqlErrors = sqlFromFile($filename);
			if ($sqlErrors) {
				$sql_error = 1;
				if (is_array($sqlErrors)) {
					foreach ($sqlErrors as $sqlError) {
						echo '<font color=red><strong>Error:</strong> '.$sqlError['error_message'].' ('.$sqlError['error_number'].')</font> with sql <b>'.$sqlError['sql'].'</b><br>';
					}
				} else {
					echo $sqlErrors;
				}
			}
		}
		$file[2]++;
	}
	echo '<br>All done!<br><br>';

	error_reporting($oldErrorReporting);
}

// set flag for parent component
define('_VALID_SETUP',1);

// buffering
ob_start();

// start session to store variables we need later
session_start();

define('RAIDER_BASE_PATH', str_replace('install'.DIRECTORY_SEPARATOR,'',dirname(__FILE__).DIRECTORY_SEPARATOR));

// setup smarty template object
include(RAIDER_BASE_PATH.'version.php');
include(RAIDER_BASE_PATH.'includes'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'smarty'.DIRECTORY_SEPARATOR.'Smarty.class.php');
include(RAIDER_BASE_PATH.'includes'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'smarty'.DIRECTORY_SEPARATOR.'SmartyValidate.class.php');
include(RAIDER_BASE_PATH.'includes'.DIRECTORY_SEPARATOR.'functions.db.php');

global $p;
$p = &new Smarty;
$p->cache_dir = '../cache';
$p->compile_dir = '../templates_c';
$p->caching = false;

// setup common variables
isset($_GET['task']) ? $task = $_GET['task'] : $task = '';
isset($_GET['option']) ? $option = $_GET['option'] : $option = '';

// header
include('header.php');

if(empty($_POST)) {
	// setup upgrade list
	$dir = RAIDER_BASE_PATH.'install'.DIRECTORY_SEPARATOR.'schema'.DIRECTORY_SEPARATOR.'upgrade';

	$dh = opendir($dir);
	$files = array();
	while(false != ($filename = readdir($dh))) {
		if (preg_match("/^[\d\.]+\.sql$/si", $filename) == 1) {
			$files[] = substr($filename, 0 , strlen($filename)-4);
		}
	}

	sort($files);
	$p->assign('upgrades', $files);
	unset($files);

	$p->display(RAIDER_BASE_PATH.'install'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'upgrade.tpl');
} else {
	$output = '';
	if(version_compare($version,'1.0.2','<=')) {
		if(file_exists(RAIDER_BASE_PATH.'configuration_auth.php')) {
			echo 'Detected an old version of <b>configuration.php</b>!<br>';
			echo 'Converting to new standard...<br>';

			// convert configuration.php
			echo 'Saving old file as <b>configuration.php.bak</b> If upgrade works properly delete this file!<br><br>';

			rename(RAIDER_BASE_PATH.'configuration.php', RAIDER_BASE_PATH.'configuration.php.bak');

			// make new file
			$handle = @fopen(RAIDER_BASE_PATH."configuration.php.bak", "r");
			if ($handle) {
			    while (!feof($handle)) {
			        $buffer = fgets($handle, 4096);

			        if(substr($buffer, 0, 18) == '$pConfig_db_server') {
			        	$server = trim(substr($buffer, 22));
			        	$server = substr($server, 0, strlen($substr)-2);
			        }

			        if(substr($buffer, 0, 16) == '$pConfig_db_name') {
			        	$name = trim(substr($buffer, 20));
			        	$name = substr($name, 0, strlen($substr)-2);
			        }

			        if(substr($buffer, 0, 16) == '$pConfig_db_user') {
			        	$user = trim(substr($buffer, 20));
			        	$user = substr($user, 0, strlen($substr)-2);
			        }

			        if(substr($buffer, 0, 16) == '$pConfig_db_pass') {
			        	$pass = trim(substr($buffer, 20));
			        	$pass = substr($pass, 0, strlen($substr)-2);
			        }

			        if(substr($buffer, 0, 18) == '$pConfig_db_prefix') {
			        	$prefix = trim(substr($buffer, 22));
			        	$prefix = substr($prefix, 0, strlen($substr)-2);
			        }


			        if(substr($buffer, 0, 16) == '$pConfig_db_pers') {
			        	$pers = trim(substr($buffer, 20));
			        	$pers = substr($pers, 0, strlen($substr)-2);
			        }

			        if(substr($buffer, 0, 23) == '$pConfig_authentication') {
			        	$auth = trim(substr($buffer, 27));
			        	$auth = substr($auth, 0, strlen($substr)-2);
			        }
			    }
			    fclose($handle);
			}

			echo 'Extracted the following information:<br>';
			echo "<b>pConfig_db_server</b>: {$server}<br>";
			echo "<b>pConfig_db_name</b>: {$name}<br>";
			echo "<b>pConfig_db_user</b>: {$user}<br>";
			echo "<b>pConfig_db_pass</b>: {$pass}<br>";
			echo "<b>pConfig_db_prefix</b>: {$prefix}<br>";
			echo "<b>pConfig_db_pers</b>: {$pers}<br>";

			// write configuration.php (new)
			$output = "<?php\n// This file is generated automatically\n// Do not alter unless instructed to do so!\n\n";
			$output .= "global \$pConfig;\n";

			$output .= "\$pConfig['db_server'] = '{$server}';\n";
			$output .= "\$pConfig['db_name'] = '{$name}';\n";
			$output .= "\$pConfig['db_user'] = '{$user}';\n";
			$output .= "\$pConfig['db_pass'] = '{$pass}';\n";
			$output .= "\$pConfig['db_prefix'] = '{$prefix}';\n";
			$output .= "\$pConfig['db_pers'] = '{$pers}';\n";

			$output .= "?>";

			// setup output
			$output_html = "&lt;?php<br>// This file is generated automatically<br>// Do not alter unless instructed to do so!<br><br>";
			$output_html .= "global \$pConfig;<br>";

			$output_html .= "\$pConfig['db_server'] = '{$server}';<br>";
			$output_html .= "\$pConfig['db_name'] = '{$name}';<br>";
			$output_html .= "\$pConfig['db_user'] = '{$user}';<br>";
			$output_html .= "\$pConfig['db_pass'] = '{$pass}';<br>";
			$output_html .= "\$pConfig['db_prefix'] = '{$prefix}';<br>";
			$output_html .= "\$pConfig['db_pers'] = '{$pers}';<br>";

			$output_html .= "?>";

			$pConfig['db_name'] = $name;
			$pConfig['db_prefix'] = $prefix;
			$link = mysql_connect($server, $user, $pass);
			mysql_select_db($name);

			// update a few configuration settings
			$self = str_replace('/install/upgrade.php','', $_SERVER['PHP_SELF']). '/';

			mysql_query("UPDATE ".$prefix."config SET `site_url`='http://{$_SERVER['HTTP_HOST']}{$self}'");

			echo "<br>Wrote <b>http://{$_SERVER['HTTP_HOST']}{$self}</b> as url in ".$prefix.'config...<br>';

			writesql($prefix);

			mysql_close($link);

			echo "Writing new file...<br><br>";
			// write configuration file
			$fd = fopen(RAIDER_BASE_PATH.'configuration.php', 'w+');

			if(!$fd) {
				$p->assign('error',"<div align=center>
										<div class=errorBody>
											Unable to write configuration file. Please follow the instructions
											below to create the file manually.
											<ol>
												<li>
													Create a blank file named <strong>configuration.php</strong>.
												</li>
												<li>
													Copy and paste the following inside the file you just created

													<table width=75% cellpadding=5 cellspacing=0 border=1 style=border:1px solid #ffffff>
														<tr style=background-color:#ffffff>
															<td>".$output_html."</td>
														</tr>
													</table>
												</li>
												<li>
													Save the file.
												</li>
												<li>
													Upload the file to your webserver in the base directory <strong>".RAIDER_BASE_PATH."</strong>
												</li>
											</ol>
											Aftewards, click <a href=install.php?option={$next_option}>here</a> to continue.
										</div>
									</div>");
			} else {
				echo '<div align="center"><div style="border: 1px solid #000000; background-color:#DEFFD9; text-align:left">'.$output_html.'</div></div><br>';
				fwrite($fd, $output);
				fclose($fd);
			}

			echo 'Detected an old version of <b>configuration_auth.php</b>!<br>';
			echo 'Redirecting to install to convert to database format.<br><br>';
			echo 'Click <a href="install.php?option=7">here</a> when ready.';

			rename(RAIDER_BASE_PATH.'configuration_auth.php',RAIDER_BASE_PATH.'configuration_auth.php.bak');
		} else {
			echo 'Detected a version of phpRaider prior to 1.0.2 but no configuration_auth.php file was found!<br>';
			echo 'Automatic upgrade is not available';
		}
	} else {
		// include configuration file
		include(RAIDER_BASE_PATH.'configuration.php');

		$link = mysql_connect($pConfig['db_server'], $pConfig['db_user'], $pConfig['db_pass']);
		mysql_select_db($pConfig['db_name']);

		writesql($pConfig['db_prefix']);

		mysql_close($link);

		$p->display(RAIDER_BASE_PATH.'install'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'upgrade_complete.tpl');
	}
}

include('footer.php');

ob_flush();
?>