<?php
	// SQL error return function (always fatal)
	function sqlError($sql) {
		global $pLang;

		echo '<div class="errorHeader">'.$pLang['sqlTitle'].'</div>';
		echo '<div class="errorBody">';
		echo '<br><strong>'.$pLang['sqlDetails'].':</strong> '.$sql;
		echo '<br><br><strong>'.$pLang['sqlError'].':</strong> '.mysql_error();
		echo '</div>';

		// unrecoverable error, die
		exit;
	}

	function sqlFromFile($fh, $redirect = '') {
		global $pConfig;

		$errors = array();
		// parse the sql file
		if(!$fd = fopen($fh, 'r')) {
			return '<font color=red>Unable to find SQL file.</font><br>';
			//die("Unable to) open file for parsing");
		} else {
			$sql = '';
			while(!feof($fd)) {
				$line = fgetc($fd);
				$sql .= $line;
				if($line == ';') {
					$sql = substr(str_replace('`phpraider_','`'.$pConfig['db_name'].'`.`'.$pConfig['db_prefix'], $sql), 0, -1);

					if(!mysql_query($sql)) {
						array_push($errors, array('sql'=>$sql,'error_number'=>mysql_errno(),'error_message'=>mysql_error()));
						//printError(mysql_error().' with SQL:<strong>'.$sql.'</strong>');
					}

					$sql = '';
				}
			}
		}

		if(!empty($redirect)) {
			header("Location: $redirect");
		}
		return ((count($errors)>0)?$errors:false);
	}

	function backupTable($table) {
		global $db_raid, $pConfig;

		// Get the list of tables from the DB.
		$result = $db_raid->sql_query('SHOW TABLE STATUS FROM '.$pConfig['db_name'].' LIKE \''.$pConfig['db_prefix'].'%\'');

		while($tableData = $db_raid->sql_fetchrow($result)) {
			if (preg_match('/^'.$pConfig['db_prefix'].'((?!config|config_auth).*)$/si', $tableData['Name'],$matches) != False) {
				$tables[] = $matches[1];
			}
		}

		// setup date and time
		$date = newDate($pConfig['date_format'],time(),$pConfig['timezone'] + $pConfig['dst']);
		$time = newDate($pConfig['time_format'],time(),$pConfig['timezone'] + $pConfig['dst']);

		$output .= "# phpRaider MySQL DUMP\n"
			. "# $date $time\n";

		$sql['SELECT'] = '*';
		foreach($table as $value) {
			if (in_array($value,$tables)) {
				// start lookup of fields
				$output .= "\n# TABLE: ".$value."\n";
				$sql['FROM'] = $value;
				$result = $db_raid->set_query('select', $sql, __FILE__, __LINE__);
				$num_fields = $db_raid->sql_numfields($result);

				$fields = array();
				for($i = 0; $i < $num_fields; $i++) {
					$fields[$i] = $db_raid->sql_fieldname($i, $result);
				}

				while($data = $db_raid->sql_fetchrow($result)) {
					// initial output
					$output .= "INSERT INTO `".$pConfig['db_prefix'].$value."` (";

					// loop fields
					for($i = 0; $i < count($fields)-1; $i++)
						$output .= "`".$fields[$i]."`,";

					$output .= "`".$fields[count($fields)-1]."`) VALUES (";

					// loop values
					for($i = 0; $i < (count($data)/2)-1; $i++) {
						$output .= $db_raid->quote_smart($data[$i]).',';
					}

					$output .= $db_raid->quote_smart($data[(count($data)/2)-1]).');';

					$output .= "\n";
				}
			}
		}
		return $output;
	}
?>