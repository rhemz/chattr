 <?php

 class Database_PostgreSQL extends Database_Base
 {
 	const Default_Port = 5432;
 	private $connect_string = 'host=%s port=%d dbname=%s user=%s password=%s';


 	public function __construct($config)
	{
		$this->host = $config['hostname'];
		$this->port = is_null($config['port']) ? self::Default_Port : $config['port'];
		$this->user = $config['username'];
		$this->password = $config['password'];
		$this->database = $config['database'];
	}


	public function connect()
	{
		$this->conn = pg_connect(
			sprintf($this->connect_string, $this->host, $this->port, $this->database, $this->user, $this->password)
		);

		if(!$this->conn)
		{
			throw new Database_Connection_Exception('PostgreSQL', $this->host, $this->port, $this->user, $this->password);
		}
	}

	
	public function query($sql, $bindings = null)
	{
		if(!$this->result = pg_query(is_null($bindings) ? $sql : $this->parse_bindings($sql, $bindings)))
		{
			Logger::log(pg_last_error($this->conn), Log_Level::Warning);
		}
		return true;
	}


	public function result()
	{
		if(pg_num_rows($this->result))
		{
			$result = array();
			while($row = pg_fetch_assoc($this->result))
			{
				$result[] = $row;
			}
			pg_free_result($this->result);

			return new Result_Set($result);
		}
		return new Result_Set();
	}


	public function close()
	{
		pg_close($this->conn);
	}


	public function escape($str)
	{
		return pg_escape_string($str);
	}


	public function last_insert_id()
	{
		$id = pg_fetch_row(pg_query($this->conn, "SELECT lastval();"));
		return $id[0];
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