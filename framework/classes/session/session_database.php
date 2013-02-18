<?php

/*
CREATE TABLE `mvcsession` ( 
	`id` varchar(32) NOT NULL, 
	`contents` longtext, 
	`modify_date` int NOT NULL,
PRIMARY KEY (`id`),
KEY `modify_date` (`modify_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/

class Session_Database extends Session_Base
{
	const Cipher = 'rijndael-256';
	const Cipher_Mode = 'cbc';

	private $model;
	private $crypt;
	private $iv;
	private $iv_len;
	

	public function __construct()
	{
		$this->config = Config::get_instance()->get('session.*');

		if($this->config['encrypt'])
		{
			$this->iv_len = mcrypt_get_iv_size(self::Cipher, self::Cipher_Mode);

			if(strlen($this->config['hash']) != $this->iv_len)
			{
				Logger::log(sprintf('The session hash length must be exactly %d characters', self::Cipher_Length), Log_Level::Error);
			}

			$this->iv = mcrypt_create_iv($this->iv_len, MCRYPT_RAND);
		}

		$this->_set_session_handler();
		$this->start();
	}


	public function _open()
	{
		$this->model = new Rz_MVC_Session_Model($this->config['table_name']);

		// garbage collect old sessions
		if(rand(0, 100) <= $this->config['gc_percent'])
		{
			$this->_gc($this->config['timeout']);
		}
		
		return true;
	}


	public function _close()
	{
		return true;
	}


	public function _read($id)
	{
		$data = $this->model->get_session_data($id);
		
		// Logger::log('read data: ' . $data);
		
		return $this->config['encrypt']
			? $this->decrypt($data)
			: $data;
	}


	public function _write($id, $data)
	{
		// Logger::log('write data: ' . $data);
		$data = $this->config['encrypt']
			? $this->encrypt($data)
			: $data;

		// Logger::log('write data: ' . $data);

		return $this->model->save_session_data($id, $data);
	}


	public function _destroy($id)
	{
		return $this->model->destroy_session($id);
	}


	public function _gc($age)
	{
		return $this->model->garbage_collect($age);
	}


	private function get_lock_name()
	{
		return 'sessionlock' . session_id();
	}


	private function encrypt($data)
	{
		return strlen($data) > 0
			? base64_encode(mcrypt_encrypt(self::Cipher, $this->config['hash'], $data, self::Cipher_Mode))
			: null;
	}


	private function decrypt($data)
	{
		return base64_decode(mcrypt_decrypt(self::Cipher, $this->config['hash'], $data, self::Cipher_Mode));
	}


	// public function __destruct()
	// {
	// 	mcrypt_generic_deinit($this->crypt);
	// 	mcrypt_module_close($this->crypt);
	// }

}



/**
* The base model for database sessions.  Should work database-agnostically, however I probably need
* to write individual drivers for each database type to handle race conditions (transactions, row locking).
* Perhaps require database drivers to implement functions for reading & writing the data.  GC and the like
* can be handled in a generic fashion.
*/
class Rz_MVC_Session_Model extends Model
{
	private $table;

	public function __construct($table)
	{
		$this->table = $table;

		parent::__construct();
	}


	public function get_session_data($id)
	{
		$sql = "SELECT contents FROM {$this->table} WHERE id = ?"; // FOR UPDATE on mysql
		if($this->query($sql, array($id)))
		{
			$result = $this->result();
			return $result->num_rows() == 1 ? $result->rows[0]->contents : false;
		}
		return false;
	}


	public function save_session_data($id, $data)
	{
		if($this->get_session_data($id) !== false)
		{
			return $this->query("UPDATE {$this->table} SET contents = ?, modify_date=? WHERE id = ?", array($data, time(), $id));
		}
		else
		{
			return $this->query("INSERT INTO {$this->table} (id, contents, modify_date) VALUES (?, ?, ?)", array($id, $data, time()));
		}
	}


	public function destroy_session($id)
	{
		return $this->query("DELETE FROM {$this->table} WHERE id = ?", array($id));
	}


	public function garbage_collect($age)
	{
		return $this->query("DELETE FROM {$this->table} WHERE modify_date < ?", array((time() - $age)));
	}

}