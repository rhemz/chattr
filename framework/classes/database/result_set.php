<?php

/**
* Generic database resultset object.  Provides a simple container for any number of rows returned
* by a database query in object form.
*/
class Result_Set
{
	public $rows = array();


	public function __construct($data = array())
	{
		// make selected column names and their values accessible as instance variables
		foreach($data as $row)
		{
			$row_obj = new stdClass();  // use php's included stdclass
			
			foreach($row as $key => $value)
			{
				$row_obj->$key = $value;
			}

			$this->rows[] = $row_obj;
		}
	}


	public function num_rows()
	{
		return sizeof($this->rows);
	}
}