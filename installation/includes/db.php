<?php
/**
 * @package akeebainstaller
 * @copyright Copyright (C) 2009-2011 Nicholas K. Dionysopoulos. All rights reserved.
 * @author Nicholas K. Dionysopoulos - http://www.dionysopoulos.me
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 *
 * Akeeba Backup Installer database connection class
 */

defined('_ABI') or die('Direct access is not allowed');

class ABIDatabase
{

	/** @var string Database type (mysql or mysqli) */
	var $dbtype;
	/** @var string MySQL server host */
	var $dbhost;
	/** @var string MySQL username */
	var $dbuser;
	/** @var string MySQL password */
	var $dbpass;
	/** @var string MySQL databse name */
	var $dbname;
	/** @var string Prefix to use, default is jos_ */
	var $dbprefix;
	/** @var string Any error message reported by the database */
	var $error = '';
	/** @var int MySQL error number */
	var $errno;

	/**
	 * @var resource The MySQL resource handle
	 */
	var $handle;

	var $sql;

	var $resource;

	/**
	 * Public constructor, initializes the connection variables
	 * @param $dbhost string MySQL server host
	 * @param $dbuser string MySQL username
	 * @param $dbpass string MySQL password
	 * @param $dbname string MySQL database name
	 * @param $dbprefix string The database prefix, default is jos_
	 * @return ABIDatabase
	 */
	function ABIDatabase($dbtype, $dbhost, $dbuser, $dbpass, $dbname, $dbprefix = 'jos_')
	{
		$this->dbtype = $dbtype;
		$this->dbhost = $dbhost;
		$this->dbuser = $dbuser;
		$this->dbpass = $dbpass;
		$this->dbname = $dbname;
		$this->dbprefix = $dbprefix;
	}

	/**
	 * Destructor
	 */
	function __destruct()
	{
		$this->disconnect();
	}

	/**
	 * Singleton implementation
	 * @param $dbhost string MySQL server host
	 * @param $dbuser string MySQL username
	 * @param $dbpass string MySQL password
	 * @param $dbname string MySQL database name
	 * @param $dbprefix string The database prefix, default is jos_
	 * @return ABIDatabase
	 */
	static function &getInstance($dbtype, $dbhost='', $dbuser='', $dbpass='', $dbname='', $dbprefix='')
	{
		static $instance;
		static $hash_current;

		// Forcibly create new instance if dbhost parameter exists and the requested connection has
		// different parameters than the existing one
		if(!empty($dbhost))
		{
			// Create a parameter hash of the current connection (if any)
			$hash_requested = $dbtype.$dbhost.$dbuser.$dbpass.$dbname.$dbprefix;
			if( ($hash_current != $hash_requested) || empty($hash_current) )
			{
				$hash_current = $hash_requested;
				if(is_object($instance)) $instance->disconnect();
				$instance = null;
			}
		}

		// Create a new instance if it doesn't exist
		if( !is_object($instance) || !(empty($host)) )
		{
			$instance = new ABIDatabase($dbtype, $dbhost, $dbuser, $dbpass, $dbname, $dbprefix);
		}

		return $instance;
	}


	/**
	 * Tries to connect to the database
	 * @return bool True on success
	 */
	function connect()
	{
		// For the shake of Windows users, I'll try persistent connections first, or reusing an existing connection
		// Ref: http://dev.mysql.com/doc/refman/5.0/en/can-not-connect-to-server.html#can-not-connect-to-server-on-windows
		switch($this->dbtype)
		{
			case 'mysql':
				$this->handle = false;
				if( function_exists('mysql_pconnect') )
					$this->handle = @mysql_pconnect($this->dbhost, $this->dbuser, $this->dbpass);
				if($this->handle === false)
					$this->handle = @mysql_connect($this->dbhost, $this->dbuser, $this->dbpass, false);
				break;

			case 'mysqli':
				$this->handle = @mysqli_connect($this->dbhost, $this->dbuser, $this->dbpass);
				break;
		}

		if($this->handle === false)
		{
			$this->error = 'Could not connect to MySQL server';
			return false;
		}

		// MySQL Connection Parameters
		switch($this->dbtype)
		{
			case 'mysql':
				$result = @mysql_query("SET NAMES 'utf8'", $this->handle);
				break;

			case 'mysqli':
				$result = @mysqli_query($this->handle, "SET NAMES 'utf8'");
				break;
		}

		// Select database
		switch($this->dbtype)
		{
			case 'mysql':
				$result = @mysql_select_db($this->dbname, $this->handle);
				break;

			case 'mysqli':
				$result = @mysqli_select_db($this->handle, $this->dbname);
				break;
		}

		if(!$result)
		{
			// Database does not exist. Let's try create it.
			switch($this->dbtype)
			{
				case 'mysql':
					$result = @mysql_query('CREATE DATABASE `'.$this->dbname.'` DEFAULT COLLATE utf8_general_ci');
					break;

				case 'mysqli':
					$result = @mysqli_query($this->handle, 'CREATE DATABASE `'.$this->dbname.'`  DEFAULT COLLATE utf8_general_ci');
					break;
			}
			// Check if the database was created
			if(!$result)
			{
				$this->error = 'Could not select the '.$this->dbname.' database.';
				return false;
			}
			// Now that it's created, select it
			switch($this->dbtype)
			{
				case 'mysql':
					$result = @mysql_select_db($this->dbname, $this->handle);
					break;

				case 'mysqli':
					$result = @mysqli_select_db($this->handle, $this->dbname);
					break;
			}
			// Check if the database was selected
			if(!$result)
			{
				$this->error = 'Could not select the '.$this->dbname.' database.';
				return false;
			}
		}

		switch($this->dbtype)
		{
			case 'mysql':
				@mysql_query("SET NAMES 'utf8'", $this->handle);
				break;

			case 'mysqli':
				@mysqli_query($this->handle,"SET NAMES 'utf8'");
				break;
		}
		
		// If MySQL 5.x is used, switch to MySQL 4.0 compatibility mode to work around strict mode issues
		if ( strpos( $this->getVersion(), '5' ) === 0 ) {
			switch($this->dbtype)
			{
				case 'mysql':
					@mysql_query("SET sql_mode = 'MYSQL40'", $this->handle);
					break;
	
				case 'mysqli':
					@mysqli_query($this->handle,"SET sql_mode = 'MYSQL40'");
					break;
			}
		}

		return true;
	}

	/**
	 * Disconnects from the database server
	 */
	function disconnect()
	{
		switch($this->dbtype)
		{
			case 'mysql':
				@mysql_close($this->handle);
				break;

			case 'mysqli':
				@mysqli_close($this->handle);
				break;
		}
	}

	/**
	 * Sets the query to execute, replacing the #__ placeholder with the database prefix, if any
	 * @param $sql string An SQL query
	 * @param $onlyFirstInstance bool Only replace the first instance
	 */
	function setQuery($sql, $onlyFirstInstance = true)
	{
		if( !empty($this->dbprefix) )
		{
			// Substitute the db prefix
			if(!$onlyFirstInstance)
			{
				$sql = str_replace('#__', $this->dbprefix, $sql);
			}
			else
			{
				$pos = strpos($sql, '#__');
				// # FIX 2.3.1 -- It is possible that the #__ doesn't exist. Stupid me!
				if($pos !== false)
					$sql = substr_replace( $sql, $this->dbprefix, $pos, 3);
			}
		}

		$this->sql = $sql;
	}

	/**
	 * Runs a query against the server.
	 * @param $sql string If set, this is the query to run, otherwise a call to setQuery() must have been preceded.
	 * @return unknown_type
	 */
	function query($sql = null)
	{
		// Reset error statuses
		$this->errno = 0;
		$this->error = '';

		// Should we set a new query? Allows for tight coding :)
		if(!is_null($sql))
		{
			$this->setQuery($sql);
		}

		// Catch empty SQL statements
		if(empty($this->sql))
		{
			$this->sql = '';
			return true;
		}

		// If there is no active connection, try to connect
		if(empty($this->handle))
		{
			if(!$this->connect())
			{
				// If conenction failed, exit
				return false;
			}
		}

		switch($this->dbtype)
		{
			case 'mysql':
				$result = @mysql_query($this->sql, $this->handle);
				break;

			case 'mysqli':
				$result = @mysqli_query($this->handle, $this->sql);
				break;
		}

		if($result === FALSE)
		{
			switch($this->dbtype)
			{
				case 'mysql':
					$this->error = 'MySQL query failed with error '.mysql_errno($this->handle).' ('. mysql_error($this->handle) .'). The query was:'.$this->sql;
					$this->errno = mysql_errno($this->handle);
					break;

				case 'mysqli':
					$this->error = 'MySQL query failed with error '.mysqli_errno($this->handle).' ('. mysqli_error($this->handle) .'). The query was:'.$this->sql;
					$this->errno = mysqli_errno($this->handle);
					break;
			}
			return false;
		}
		else
		{
			$this->resource = $result;
			$this->sql = '';
			return true;
		}

	}

	/**
	 * Returns a query result as an associative array
	 * @param $sql string The SQL query to execute (optional)
	 * @return array The result, or an empty array if nothing was fetched
	 */
	function getAssocArray($sql = null)
	{
		$output = array();
		$result = $this->query($sql);
		if($result == true)
		{
			switch($this->dbtype)
			{
				case 'mysql':
					$numrows = @mysql_num_rows($this->resource);
					break;

				case 'mysqli':
					$numrows = @mysqli_num_rows($this->resource);
					break;
			}
			if($numrows > 0)
			{
				switch($this->dbtype)
				{
					case 'mysql':
						while ($row = @mysql_fetch_assoc($this->resource))
						{
							$output[] = $row;
						}
						break;

					case 'mysqli':
						while ($row = @mysqli_fetch_assoc($this->resource))
						{
							$output[] = $row;
						}
						break;
				}
			}
		}
		return $output;
	}

	/**
	 * Escapes a value string to be used in a SQL query
	 * @param $string string The string to escape
	 * @return string The escaped string
	 */
	function escape($string)
	{
		switch($this->dbtype)
		{
			default:
			case 'mysql':
				return @mysql_real_escape_string($string, $this->handle);
				break;

			case 'mysqli':
				return @mysqli_real_escape_string($this->handle, $string);
				break;
		}

	}

	function getError()
	{
		return $this->error;
	}
	
	function reset()
	{
		$this->errno = 0;
		$this->error = null;
	}
	
	function getVersion()
	{
		switch($this->dbtype)
		{
			case 'mysql':
				return mysql_get_server_info($this->handle);
				break;

			case 'mysqli':
				return mysqli_get_server_info($this->handle);
				break;
		}		
	}
}