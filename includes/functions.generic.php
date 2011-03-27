<?php
// prints a nice error
function printError($error, $die = 0) {
	global $pLang;

	echo '<div align="center">';
	echo '<div class="errorHeader">'.$pLang['genericErrorHeader'].'</div>';
	echo '<div class="errorBody">';
	echo '<strong>'.$error.'</strong>';
	echo '</div></div><br>';

	// unrecoverable error, die
	if($die)
		exit;
}

// sets up generic output information for reports
function setupOutput() {
	global $report, $pConfig;

	$report->setMainAttributes('width="100%" cellpadding="3" cellspacing="0" border="0" class="dataOutline"');
	$report->setRowAttributes('class="row1"', 'class="row2"', 'rowHover');
	$report->setFieldHeadingAttributes('class="listHeader"');
	$report->setListRange((empty($_GET['Base'])?'0':$_GET['Base']), $pConfig['report_max']);
}

// simple redirect
function pRedirect($url, $delay = 0) {
	header("Location: $url");
	exit;
}

function getMainframeFromProfileID($profile_id){
	global $db_raid,$pMain;

	$sql['SELECT'] = '*';
	$sql['FROM'] = 'profile';
	$sql['WHERE'] = '`profile_id`='.$profile_id;
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	if($db_raid->sql_numrows() > 0)	{
		$data = $db_raid->fetch();
		$data['session_logged_in'] = 1;
	} else {
		$data = array(
			$data['session_logged_in'] = 0,
		);
	}
	$Main = new Mainframe($data);

	if ($Main->getProfileID()>-1) {
		$sql['SELECT'] = '*';
		$sql['FROM'] = 'permissions';
		$sql['WHERE'] = 'group_id='.$Main->getGroupID();
		$db_raid->set_query('select', $sql, __FILE__, __LINE__);

		while($perm = $db_raid->fetch()) {
			$Main->setPerm($perm['permission_name']);
		}
	}
	return $Main;
}

// retrieve profile ID
function getProfileFromTable($table, $field, $id)
{
	global $db_raid, $pConfig;

	$sql['SELECT'] = 'profile_id';
	$sql['FROM'] = $table;
	$sql['WHERE'] = "{$field}={$id}";
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);
	$temp = $db_raid->fetch();

	return($temp['profile_id']);
}

// retrieves profile ID given username
function getProfileID($username) {
	global $pConfig, $db_raid;

	$sql['SELECT'] = '*';
	$sql['FROM'] = 'profile';
	$sql['WHERE'] = "username=".$db_raid->quote_smart($username);
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);
	$temp = $db_raid->fetch();

	return($temp['profile_id']) ;
}

// format a message
function formatText($data, $type, $len = 0) {
	switch ($type) {
		case '_NOHTML_':
			$data = strip_tags($data);
			break;
	}

	if($len != 0) {
		if(strlen($data) > $len)
			$data = substr($data, 0, $len) . '...';
	}

	return $data;
}

// returns array of specified type index by ids
function getData($arg) {
	global $db_raid, $pConfig;

	$temp = array();

	$sql['SELECT'] = sprintf('%s_id,%s_name',$arg,$arg);
	$sql['FROM'] = $arg;
	$sql['SORT'] = $arg.'_name';
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	while($data = $db_raid->fetch()) {
		$temp[$data[$arg.'_id']] = $data[$arg.'_name'];
	}

	return $temp;
}

// returns array of specified type index by ids
function getCustomData($table, $field) {
	global $db_raid, $pConfig;

	$temp = array();

	$sql['SELECT'] = '*';
	$sql['FROM'] = $table;
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	while($data = $db_raid->fetch()) {
		$temp[$data[$table.'_id']] = $data[$field];
	}

	return $temp;
}

// returns associate array of specified type indexed by name
function getIds($arg) {
	global $db_raid, $pConfig;

	$temp = array();

	$sql['SELECT'] = '*';
	$sql['FROM'] = $arg;
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	while($data = $db_raid->fetch()) {
		$temp[$data[$arg.'_name']] = $data[$arg.'_id'];
	}

	return $temp;
}

// checks if a profile ID matches value in database for given table
function checkOwn($table, $table_id, $profile) {
	global $db_raid, $pConfig;

	$sql['SELECT'] = 'profile_id';
	$sql['FROM'] = $table;
	$sql['WHERE'] = "{$table}_id={$table_id}";
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);
	$data = $db_raid->fetch();

	if($data['profile_id'] == $profile)
		return true;
	else
		return false;
}

// properly escapes HTML characters so they don't break javascript
function escapeHTML($arg) {
	$arg = str_replace("'", "\'", $arg);
	return $arg;
}

function generate_password($length = 10) {

	// This variable contains the list of allowable characters
	// for the password.  Note that the number 0 and the letter
	// 'O' have been removed to avoid confusion between the two.
	// The same is true of 'I' and 1
	$allowable_characters = "abcdefghijklmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789";

	// We see how many characters are in the allowable list
	$ps_len = strlen($allowable_characters);

	// Seed the random number generator with the microtime stamp
	// (current UNIX timestamp, but in microseconds)
	mt_srand((double) microtime() * 1000000);

	// Declare the password as a blank string.
	$pass = "";

	// Loop the number of times specified by $length
	for($i = 0; $i < $length; $i++) {

		// Each iteration, pick a random character from the
		// allowable string and append it to the password.
		$pass .= $allowable_characters[ mt_rand (0, $ps_len- 1) ];
	}

	// Retun the password we've selected
	return $pass;
}

// uses phpMailer to send emails
// simple emails only, more complex emails are handled directly
function pMailer($to, $from, $subject, $message) {
	global $pMail;

	$pMail->From = $from;
	$pMail->AddAddress($to);
	$pMail->Subject = $subject;
	$pMail->Body = $message;
	$pMail->IsHTML(true);

	if(!$pMail->Send()) {
		printError('phpMailer error: '.$pMail->ErrorInfo, 1);
	}
}

/**
 * Unzip the source_file in the destination dir
 * Modded by Mordon
 * @param   string      The path to the ZIP-file.
 * @param   string      The path where the zipfile should be unpacked, if false the directory of the zip-file is used
 * @param   boolean     Indicates if the files will be unpacked in a directory with the name of the zip-file (true) or not (false) (only if the destination directory is set to false!)
 * @param   boolean     Overwrite existing files (true) or not (false)
 *
 * @return  boolean     Succesful or not
 */
function unzip($src_file, $dest_dir=false, $create_zip_name_dir=true, $overwrite=true) {
	if(function_exists("zip_open")) {
		if(!is_resource(zip_open($src_file))) {
			$src_file=dirname($_SERVER['SCRIPT_FILENAME']).DIRECTORY_SEPARATOR.$src_file;
		}

		if (is_resource($zip = zip_open($src_file))) {
			$splitter = ($create_zip_name_dir === true) ? "." : "/";
			if ($dest_dir === false) $dest_dir = substr($src_file, 0, strrpos($src_file, $splitter))."/";

			// Create the directories to the destination dir if they don't already exist
			create_dirs($dest_dir);

			// For every file in the zip-packet
			while ($zip_entry = zip_read($zip)) {
				// Now we're going to create the directories in the destination directories

				// If the file is not in the root dir
				$pos_last_slash = strrpos(zip_entry_name($zip_entry), "/");
				if ($pos_last_slash !== false) {
					// Create the directory where the zip-entry should be saved (with a "/" at the end)
					create_dirs($dest_dir.substr(zip_entry_name($zip_entry), 0, $pos_last_slash+1));
				}

				// Open the entry
				if (zip_entry_open($zip,$zip_entry,"r")) {
					// The name of the file to save on the disk
					$file_name = $dest_dir.zip_entry_name($zip_entry);

					// Check if the files should be overwritten or not
					if ($overwrite === true || $overwrite === false && !is_file($file_name)) {
						// Get the content of the zip entry
						$fstream = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

						if(!is_dir($file_name)) {
							file_put_contents($file_name, $fstream );
						}

						// Set the rights
						if(file_exists($file_name)) {
							chmod($file_name, 0777);
							echo "<span style=\"color:#1da319;\">file saved: </span>".$file_name."<br />";
						} else {
							echo "<span style=\"color:red;\">file not found: </span>".$file_name."<br />";
						}
					}

					// Close the entry
					zip_entry_close($zip_entry);
				}
			}
			// Close the zip-file
			zip_close($zip);
		} else {
			echo "No Zip Archive Found.";
			return false;
		}
		return true;
	} else {
		if(version_compare(phpversion(), "5.2.0", "<"))
			$infoVersion="(use PHP 5.2.0 or later)";

		echo "You need to install/enable the php_zip.dll extension $infoVersion";
	}
}

function create_dirs($path)
{
  if (!is_dir($path))
  {
    $directory_path = "";
    $directories = explode("/",$path);
    array_pop($directories);

    foreach($directories as $directory)
    {
      $directory_path .= $directory.DIRECTORY_SEPARATOR;
      if (!is_dir($directory_path))
      {
        mkdir($directory_path);
        chmod($directory_path, 0777);
      }
    }
  }
}

function dump_array($array, $level = 0) {
	if(empty($array)) {
		$return = '-&gt; Empty<br>';
	} else {
		$arrows = '';
		for($i = 0; $i < $level; $i++) {
			$arrows .= '--';
		}

		$return = '';
		foreach($array as $key => $value) {
			if(is_array($value)) {
				$return .= $arrows.'&gt; <strong>'.$key.': dumping array...</strong><br>';
				$return .= dump_array($value, $level+1);
			} elseif (is_object($value)) {
				$return .= $arrows.'&gt; <strong>'.$key.': dumping object...</strong><br>';
				$return .= dump_array($value, $level+1);
			} else {
				$return .= $arrows.'-&gt; <strong>'.$key.':</strong> '.htmlspecialchars($value).'<br>';
			}
		}
	}
	return $return;
}

function dump_query_history() {
	global $db_raid, $pConfig;

	$result = '';
	if(!empty($db_raid->history)) {
		$history = $db_raid->history;
		$result .= "<table>\n";
		foreach ($history as $query) {
			$result .= "<tr><th valign=\"top\" align=\"left\">Query</th><td>".$query."</td></tr>\n";
			if (!empty($pConfig['db_show_plan'])) {
				$res = $db_raid->sql_query('EXPLAIN '.$query);
				if ($res) {
					$plan = $db_raid->sql_fetchrowset($res, MYSQL_ASSOC);
					$result .= "<tr><th valign=\"top\" align=\"left\">Plan</th><td><table>";
					$result .= "<tr>";
					foreach (array_keys($plan[0])as $key) {
						$result .= "<th>".$key."</th>";
					}
					$result .= "</tr>";
					foreach ($plan as $line) {
						$result .= "<tr>";
						foreach ($line as $value) {
							$result .= "<td>".$value."</td>";
						}
						$result .= "</tr>";
					}
					$result .= "</table></td></tr>\n";
				}
			}
		}
		$result .= "</table>\n";
	}
	return $result;
}
?>