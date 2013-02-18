<?php


class Database_PDO_MSSQL extends Database_Base
{
	const PDO_Connection_String = "sqlsrv:Server=%s;Database=%s;";

	private $config;

	public function __construct($config)
	{
		$this->config = $config;
	}

	public function connect()
	{
		try
		{
			$this->conn = new PDO(
				sprintf(self::PDO_Connection_String, $this->config['hostname'], $this->config['database']),
				$this->config['username'],
				$this->config['password']);
		}
		catch(PDOException $e)
		{
			Logger::log($e->getMessage(), Log_Level::Error);
		}
	}
	
	public function query($sql, $bindings = null)
	{
		
	}

	public function result()
	{
		
	}

	public function close()
	{
		
	}

	public function escape($str)
	{
		
	}
}