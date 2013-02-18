<?php


/**
* All database driver classes extend Database_Base and implement the abstract methods necessary
* for basic database interaction.  It is possible to extend database drivers beyond the scope 
* of what the base database class defines, and make that functionality available to all user-created
* models automatically given the base Model class's overriding PHP's magic __call() method.
*/
abstract class Database_Base
{
	protected static $instance;

	protected $host;
	protected $port;
	protected $user;
	protected $password;
	protected $database;

	protected $unicode;

	protected $conn;
	protected $result;


	abstract public function connect();

	abstract public function query($sql, $bindings = null);

	abstract public function result();

	abstract public function close();

	abstract public function escape($str);

	abstract public function last_insert_id();

	abstract protected function translate_binding_datatype($val);



	/**
	* When called for the first time, spins up an instance of the appropriate database driver as 
	* defined in the application database config.
	* @param string $type The database type (mysql, mysqli, postgres, pdo_mssql, etc)
	* @param array $config The database connection configuration array loaded from app config
	* @return Database driver
	*/
	public static function &get_instance($type = null, $config = null)
	{
		if(is_null(self::$instance) && !is_null($type) && !is_null($config))
		{
			self::$instance = new $type($config);

			try
			{
				self::$instance->connect();
			}
			catch(Database_Connection_Exception $dce)
			{
				Logger::log($dce->getMessage(), Log_Level::Error);
			}
			catch(Database_Selection_Exception $dse)
			{
				Logger::log($dse->getMessage(), Log_Level::Error);
			}
		}
		return self::$instance;
	}


	/**
	* Common method used by all database drivers, responsible for binding query parameters
	* to their corresponding values by datatype.  Relies on individual driver's implementation of 
	* translate_binding_datatype(), as different databases handle datatypes differently (i.e. mysql
	* escapes single quotes with a \, mssql escapes quotes with another quote, etc).
	* @param string $sql The sql query to be bound
	* @param array $bindings An array containing the data to bind to the query
	* @return string The bound sql query
	*/
	protected function parse_bindings($sql, $bindings)
	{
		// todo: use actual mysqli lib binding
		
		$qbits = explode('?', $sql);
		$i = 0;

		if(!is_null($bindings) && (sizeof($bindings) != substr_count($sql, '?')))
		{
			Logger::log(
				sprintf('The number of query bindings(%d) passed does not match the SQL statement (%d)', 
					sizeof($bindings), 
					sizeof(array_filter($qbits))
				), 
				Log_Level::Error);
		}

		// start building bound query
		$sql = $qbits[0];
		foreach($bindings as $val)
		{
			$sql .= $this->translate_binding_datatype($val) . $qbits[++$i];
		}
		
		return $sql;
	}

}





class Database_Connection_Exception extends Rz_MVC_Exception
{
	public function __construct($type, $host, $port, $user, $pass)
	{ 
		$msg = sprintf("Unable to connect to %s server ", $type);

		$msg .= ENVIRONMENT == Environment::Development
			? sprintf("(%s) on port %d with credentials %s/%s", $host, $port, $user, $pass)
			: "using the supplied settings";

		parent::__construct($msg);
	}
}


class Database_Selection_Exception extends Rz_MVC_Exception
{
	public function __construct($msg) { parent::__construct($msg); }
}
