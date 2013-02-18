<?php


class Database_MySQL extends Database_Base
{
	const Default_Port = 3306;


	public function __construct($config)
	{
		$this->port = is_null($config['port']) ? self::Default_Port : $config['port'];
		$this->host = sprintf("%s:%s", $config['hostname'], $this->port);
		$this->user = $config['username'];
		$this->password = $config['password'];
		$this->database = $config['database'];

		$this->unicode = $config['unicode'];
	}


	public function connect()
	{
		$this->conn = mysql_connect($this->host, $this->user, $this->password);
		if(!$this->conn)
		{
			throw new Database_Connection_Exception('MySQL', $this->host, $this->port, $this->user, $this->password);
		}

		if(!mysql_select_db($this->database, $this->conn))
		{
			throw new Database_Selection_Exception(sprintf("Could not use the specified MySQL database (%s) on %s", $this->database, $this->host));
		}

		if($this->unicode)
		{
			$this->query("SET NAMES 'utf8' COLLATE 'utf8_unicode_ci'");
		}
	}


	public function query($sql, $bindings = null)
	{
		if(!$this->result = mysql_query((is_null($bindings) ? $sql : $this->parse_bindings($sql, $bindings)), $this->conn))
		{
			Logger::log(mysql_error(), Log_Level::Warning);
			// throw error or return false
		}
		return true;
	}


	public function result()
	{
		if(mysql_num_rows($this->result) > 0)
		{
			$result = array();
			while($row = mysql_fetch_assoc($this->result))
			{
				$result[] = $row;
			}

			mysql_free_result($this->result);

			return new Result_Set($result);
		}
		return new Result_Set();
	}


	public function close()
	{
		mysql_close($this->conn);
	}


	public function escape($str)
	{
		return mysql_real_escape_string($str);
	}


	public function last_insert_id()
	{
		return mysql_insert_id($this->conn);
	}


	protected function translate_binding_datatype($val)
	{
		if(is_string($val))
		{
			return sprintf("'%s'", $this->escape($val));
		}
		else if(is_bool($val))
		{
			return ($val === true) ? 1 : 0;
		}
		else if(is_null($val))
		{
			return 'NULL';
		}

		return $val;
	}

}