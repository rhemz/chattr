<?php


class Logger
{
	const Write_Mode = 'a+';

	private static $config;

	/**
	* Log a message.  Depending on the user configuration, message may either be displayed, written to a 
	* logfile, or both.  Error level logs end application execution.
	* @param string $message The log message
	* @param Log_Level $level The log level
	*/
	public static function log($message, $level = Log_Level::Notice)
	{
		// only do this once
		if(is_null(self::$config))
		{
			$config =& Config::get_instance();
			self::$config = $config->get('logging.*');
		}

		// print message if enabled for the log level
		if(self::$config['print'] & $level)
		{
			$level == Log_Level::Error
				? get_mvc()->load_view('error', array('message' => $message))
				: print(sprintf("%s %s %s %s", PHP_EOL, $message, (self::$config['print_html'] ? '<br />' : null), PHP_EOL));
			
		}

		// write to file. might end up doing more than writing a line
		switch($level)
		{
			case Log_Level::Notice:
				self::write_logfile(Log_Level::Notice, self::$config['notice_path'], $message);
				break;

			case Log_Level::Warning:
				self::write_logfile(Log_Level::Warning, self::$config['warning_path'], $message);
				break;

			case Log_Level::Error:
				self::write_logfile(Log_Level::Error, self::$config['error_path'], $message);
				exit(); // die on error level.  maybe should not?
				break;
		}
	}


	private static function write_logfile($level, $file, $message)
	{
		// check bitmask
		if(self::$config['write_file'] && (self::$config['log_level'] & $level))
		{
			// make logs directory if it doesn't exist
			if(!is_dir(LOG_PATH))
			{
				mkdir(LOG_PATH);
			}

			$handle = fopen(LOG_PATH . DIRECTORY_SEPARATOR . $file, self::Write_Mode);
			fwrite($handle, sprintf("%s: %s%s", date(DATE_RFC822), $message, PHP_EOL));
			fclose($handle);
		}
	}


	/**
	* Simple convenience method for wrapping print_r output in <pre> tags if a debugger is not available.
	* @param mixed $object The variable to dump.
	*/
	public static function print_r($object)
	{
		echo '<pre>';
		print_r($object);
		echo '</pre>';
	}

}