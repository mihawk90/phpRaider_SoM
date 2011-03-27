<?php
/***************************************************************************
 *                                 database.php
 *                            -------------------
 *   begin                : Saturday, Feb 13, 2001
 *   copyright            : (C) 2001 The phpBB Group
 *   email                : support@phpbb.com
 *
 *   $Id: mysql.php,v 1.16 2002/03/19 01:07:36 psotfx Exp $
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

if(!defined("PHPRAIDER_SQL_LAYER"))
{

define("PHPRAIDER_SQL_LAYER","mysql");

class pr_sql_db
{

	var $db_connect_id;
	var $query_result;
	var $row = array();
	var $rowset = array();
	var $history = array();
	var $num_queries = 0;
	var $query;
	var $result;
	var $prefix;
	var $dbname;

	function _getFullyQualifiedTableName($table) {
		preg_match('/^(\w+)(?:(?:\s)((as(?:\s))?(\w+)))?$/i',$table,$matches);
		$result = '`'.$this->dbname.'`.`'.$this->prefix.$matches[1].'`'.(isset($matches[2])?' '.$matches[2]:'');
		return $result;
	}

	function _get_magic_quotes_runtime() {
		$result = false;
		if (function_exists('get_magic_quotes_runtime')) {
			$result = @get_magic_quotes_runtime();
		}
		return $result;
	}
	//
	// Constructor
	//
	function pr_sql_db($sqlserver, $sqluser, $sqlpassword, $database, $prefix, $persistency = true, $newlink = false)
	{

		$this->persistency = $persistency;
		$this->user = $sqluser;
		$this->password = $sqlpassword;
		$this->server = $sqlserver;
		$this->dbname = $database;
		$this->prefix = $prefix;

		if($this->persistency)
		{
			$this->db_connect_id = @mysql_pconnect($this->server, $this->user, $this->password);
		}
		else
		{
			$this->db_connect_id = @mysql_connect($this->server, $this->user, $this->password, $newlink);
		}
		if($this->db_connect_id)
		{
			if($database != "")
			{
				$this->dbname = $database;
			}
			return $this->db_connect_id;
		}
		else
		{
			return false;
		}
	}

	//
	// Other base methods
	//
	function sql_close()
	{
		if($this->db_connect_id)
		{
			if($this->query_result)
			{
				@mysql_free_result($this->query_result);
			}
			$result = @mysql_close($this->db_connect_id);
			return $result;
		}
		else
		{
			return false;
		}
	}

	//
	// Base query method
	//
	function sql_query($query = "")
	{
		// Remove any pre-existing queries
		unset($this->query_result);
		if($query != "")
		{
			$this->history[$this->num_queries] = $query;
			$this->num_queries++;

			$this->query_result = @mysql_query($query, $this->db_connect_id);
		}
		if($this->query_result)
		{
			unset($this->row[intval($this->query_result)]);
			unset($this->rowset[intval($this->query_result)]);
			return $this->query_result;
		}
		else
		{
			return false;
		}
	}

	// returns saved query
	function get_query() {
		return $this->query;

		$this->finish_query($array);
		return $this->result;
	}

	function get_count($array) {
		$this->set_query('select', $array, __FILE__, __LINE__);
		$data = $this->sql_fetchrow($this->result);
		return $data[0];
	}

	// prevents SQL injection
	function quote_smart($value) {
		// Stripslashes
		if (function_exists('get_magic_quotes_gpc')) {
			if (@get_magic_quotes_gpc()) {
				$value = stripslashes($value);
			}
		}

		// Quote if not a number or a numeric string
		if (!preg_match('/^\\d+$/',$value) == 1) {
			$value = "'".mysql_real_escape_string($value)."'";
		}
		return $value;
	}

	// retries all field information as an associate array
	function get_field_data($result = '') {
		if(empty($result))
			$result = $this->result;

		$fields = mysql_num_fields($result);
		for($i = 0; $i < $fields; $i++) {
			$data[$i]['type'] = mysql_field_type($result, $i);
			$data[$i]['name'] = mysql_field_name($result, $i);
			$data[$i]['len'] = mysql_field_len($result, $i);
			$data[$i]['flags'] = mysql_field_flags($result, $i);
		}

		return $data;
	}


	function parse_query($type, $sql) {
		switch($type) {
			case 'select':
				$query = "SELECT {$sql["SELECT"]}";
				if (!empty($sql['FROM'])) {
					$query .= ' FROM ';
					if (is_array($sql['FROM'])) {
						$tables = array();
						foreach($sql['FROM'] as $table) {
							$tables[] = $this->_getFullyQualifiedTableName($table);
						}
						$query .= '('.implode(', ', $tables).')';
					} else {
						$query .= $this->_getFullyQualifiedTableName($sql['FROM']);
					}
				}
				if (!empty($sql['JOIN'])) {
					//if (empty(array_keys($sql['JOIN'] {
					if (is_numeric(max(array_keys($sql['JOIN'])))) {
						foreach($sql['JOIN'] as $join) {
							$query .= ' '.((!empty($join['TYPE']))?$join['TYPE'].' ':'').'JOIN '.$this->_getFullyQualifiedTableName($join['TABLE']).' ON '.$join['CONDITION'];
						}
					} else {
						$query .= ' '.((!empty($sql['JOIN']['TYPE']))?$sql['JOIN']['TYPE'].' ':'').'JOIN '.$this->_getFullyQualifiedTableName($sql['JOIN']['TABLE']).' ON '.$sql['JOIN']['CONDITION'];
					}
				}
				break;
			case 'update':
				$query = 'UPDATE '.$this->_getFullyQualifiedTableName($sql['UPDATE']).' SET ';

				foreach($sql['VALUES'] as $key=>$value) {
					$value = $this->quote_smart($value);
					$query .= "`{$key}`={$value},";
				}

				$query = substr($query, 0, strlen($query)-1);
				break;
			case 'replace':
				$query = 'REPLACE INTO '.$this->_getFullyQualifiedTableName($sql['REPLACE']).' (';

				foreach($sql['VALUES'] as $key=>$value) {
					$query .= "`{$key}`,";
				}

				$query = substr($query, 0, strlen($query)-1).") VALUES (";

				foreach($sql['VALUES'] as $value) {
					$value = $this->quote_smart($value);
					$query .= "{$value},";
				}

				$query = substr($query, 0, strlen($query)-1).")";
				break;
			case 'insert':
				$query = 'INSERT INTO '.$this->_getFullyQualifiedTableName($sql['INSERT']).' (';

				foreach($sql['VALUES'] as $key=>$value) {
					$query .= "`{$key}`,";
				}

				$query = substr($query, 0, strlen($query)-1).") VALUES (";

				foreach($sql['VALUES'] as $value) {
					$value = $this->quote_smart($value);
					$query .= "{$value},";
				}

				$query = substr($query, 0, strlen($query)-1).")";
				break;
			case 'delete':
				$query = 'DELETE FROM '.$this->_getFullyQualifiedTableName($sql['DELETE']);
				break;
		}

		if(!empty($sql["WHERE"]) && $type !='insert' && $type != 'replace') {
			$query .= " WHERE {$sql["WHERE"]}";
		}

		if (!empty($sql['GROUPBY'])) {
			$query .= " GROUP BY {$sql["GROUPBY"]}";
		}

		if(!empty($sql["SORT"]) && $type == 'select') {
			$query .= " ORDER BY {$sql["SORT"]}";
		}

		if(!empty($sql['LIMIT']) && $type == 'select') {
			$query .= " LIMIT {$sql['LIMIT']}";
		}

		return $query;
	}

	// set the query
	function set_query($type, $sql, $file, $line) {

		$this->query = $this->parse_query($type, $sql).';';

		$this->result = $this->sql_query($this->query) or $this->show_error($this->get_query(), mysql_error(), $file, $line);

		return $this->result;
	}

	// display errors
	function show_error($sql, $error, $file, $line) {
		echo "<div class=errorHeader>SQL Error Encountered!</div>";
		echo "<div class=errorBody style=\"text-align:left\">
				<b>Query</b>: {$sql}<br>
				<b>Error</b>: {$error}<br>
				<b>File</b>: {$file}<br>
				<b>Line</b>: {$line}
			</div><br>";
		exit;
	}

	// runs the fetch on the query
	function fetch() {
		$data = $this->sql_fetchrow($this->result);

		return $data;
	}

	//
	// Other query methods
	//
	function sql_numrows($query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = @mysql_num_rows($query_id);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_affectedrows()
	{
		if($this->db_connect_id)
		{
			$result = @mysql_affected_rows($this->db_connect_id);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_numfields($query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = @mysql_num_fields($query_id);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_fieldname($offset, $query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = @mysql_field_name($query_id, $offset);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_fieldtype($offset, $query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = @mysql_field_type($query_id, $offset);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_fetchrow($query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = @mysql_fetch_array($query_id);
			// Remove slashes if get_magic_quotes_runtime is enabled
			if ($this->_get_magic_quotes_runtime()) {
				foreach ($result as $key => $row) {
					$result[$key] = stripslashes($row);
				}
			}
			$this->row[intval($query_id)] = $result;
			return $this->row[intval($query_id)];
		}
		else
		{
			return false;
		}
	}
	function sql_fetchrowset($query_id = 0, $result_type = MYSQL_BOTH)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			unset($this->rowset[intval($query_id)]);
			unset($this->row[intval($query_id)]);
			while($res = @mysql_fetch_array($query_id, $result_type))
			{
				// Remove slashes if get_magic_quotes_runtime is enabled
				if ($this->_get_magic_quotes_runtime()) {
					foreach ($res as $key => $row) {
						$res[$key] = stripslashes($row);
					}
				}
				$this->rowset[intval($query_id)] = $res;
				$result[] = $this->rowset[intval($query_id)];
			}
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_fetchfield($field, $rownum = -1, $query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			if($rownum > -1)
			{
				// Remove slashes if get_magic_quotes_runtime is enabled
				$result = (($this->_get_magic_quotes_runtime())?stripslashes(@mysql_result($query_id, $rownum, $field)):@mysql_result($query_id, $rownum, $field));
			}
			else
			{
				if(empty($this->row[intval($query_id)]) && empty($this->rowset[intval($query_id)]))
				{
					if($this->sql_fetchrow())
					{
						$result = $this->row[intval($query_id)][$field];
					}
				}
				else
				{
					if($this->rowset[intval($query_id)])
					{
						$result = $this->rowset[intval($query_id)][$field];
					}
					else if($this->row[intval($query_id)])
					{
						$result = $this->row[intval($query_id)][$field];
					}
				}
			}
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_rowseek($rownum, $query_id = 0){
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = @mysql_data_seek($query_id, $rownum);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_nextid(){
		if($this->db_connect_id)
		{
			$result = @mysql_insert_id($this->db_connect_id);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_freeresult($query_id = 0){
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}

		if ($query_id)
		{
			unset($this->row[intval($query_id)]);
			unset($this->rowset[intval($query_id)]);

			@mysql_free_result($query_id);

			return true;
		}
		else
		{
			return false;
		}
	}
	function sql_error($query_id = 0)
	{
		if (!empty($this->db_connect_id)) {
			$result["message"] = @mysql_error($this->db_connect_id);
			$result["code"] = @mysql_errno($this->db_connect_id);
		} else {
			$result["message"] = @mysql_error();
			$result["code"] = @mysql_errno();
		}
		return $result;
	}

} // class sql_db

} // if ... define

?>