<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// load footer?
$load_footer = 1;

if(!$pMain->checkPerm('allow_backups'))
	pRedirect('index.php?option=com_login&task=login');

if(empty($task) || $task == '') {
	if(empty($_POST)) {
		// Get the list of tables from the DB.
		$tables = array();
		$result = $db_raid->sql_query('SHOW TABLE STATUS FROM '.$pConfig['db_name'].' LIKE \''.$pConfig['db_prefix'].'%\'');

		while($table = $db_raid->sql_fetchrow($result)) {
			if (preg_match('/^'.$pConfig['db_prefix'].'((?!config|config_auth).*)$/si', $table['Name'],$matches) != False) {
				$tables[] = $matches[1];
			}
		}

		$p->assign(
			array(
				'header'=>$pLang['baHeader'],
				'submit'=>$pLang['submit'],
				'reset'=>$pLang['reset'],
				'chooseTables'=>$pLang['baChooseTables'],
				'tables' => $tables,
			)
		);
		$p->display(RAIDER_TEMPLATE_PATH.'backups.tpl');
	} else {
		ob_end_clean();

		$fh = fopen(RAIDER_BASE_PATH.'cache/mysql_dump.txt', 'w');
		fwrite($fh, backupTable($_POST['tables']));
		fclose($fh);

		header("Content-Length: " .filesize(RAIDER_BASE_PATH.'cache/mysql_dump.txt'));
		header('Content-type: text/x-delimtext');
		header('Content-Disposition: attachment; filename="mysql_dump.sql"');

		readfile(RAIDER_BASE_PATH.'cache/mysql_dump.txt');
		exit;
	}
}
?>