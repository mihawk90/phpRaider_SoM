<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2009
 */

// Make sure DATE_RFC2822 is defined
if (!defined('DATE_RFC2822'))
	define('DATE_RFC2822','D, d M Y H:i:s O');

// PHP Upload error definitions
if (!defined('UPLOAD_ERR_OK'))
	define('UPLOAD_ERR_OK', 0);
if (!defined('UPLOAD_ERR_INI_SIZE'))
	define('UPLOAD_ERR_INI_SIZE', 1);
if (!defined('UPLOAD_ERR_FORM_SIZE'))
	define('UPLOAD_ERR_FORM_SIZE', 2);
if (!defined('UPLOAD_ERR_PARTIAL'))
	define('UPLOAD_ERR_PARTIAL', 3);
if (!defined('UPLOAD_ERR_NO_FILE'))
	define('UPLOAD_ERR_NO_FILE', 4);
if (!defined('UPLOAD_ERR_NO_TMP_DIR'))
	define('UPLOAD_ERR_NO_TMP_DIR', 6);
if (!defined('UPLOAD_ERR_CANT_WRITE'))
	define('UPLOAD_ERR_CANT_WRITE', 7);
if (!defined('UPLOAD_ERR_EXTENSION'))
	define('UPLOAD_ERR_EXTENSION', 8);

// Todo: AttayCombine
if (!function_exists('array_combine')) {
	/**
	 * array_combine()
	 *
	 * @param array $keys
	 * @param array $values
	 * @return array
	 */
	function array_combine($keys, $values ) {
		$result = array();
		$keys = array_values($keys);
		$values = array_values($values);
		foreach($keys as $key => $value) {
			$result[$value] = $values[$key];
		}
		return $result;
	}
}

// file_put_contents constants.
if (!defined('FILE_USE_INCLUDE_PATH'))
	define('FILE_USE_INCLUDE_PATH', 1);
if (!defined('FILE_APPEND'))
	define('FILE_APPEND', 8);
if (!defined('LOCK_EX'))
	define ('LOCK_EX', 2);

if (!function_exists('file_put_contents')) {
	/**
	 * file_put_contents()
	 *
	 * @param string $filename
	 * @param mixed $data
	 * @param int $flags
	 * @return
	 */
	function file_put_contents($filename, $data, $flags = 0) {
		$f = @fopen($filename, (($flags & FILE_APPEND) == FILE_APPEND)?'a':'w');
		if (!$f) {
			return false;
		} else {
			$bytes = fwrite($f, $data);
			fclose($f);
			return $bytes;
		}
	}
}
?>