<?php


class Output
{
	const File_Mode = 'rb'; // read, binary no translate
	const File_Chunk_Size = 4096;


	/**
	* Send appropriate HTTP headers to redirect the client to another resource.
	* @param string $location The URI to redirect to
	* @param bool $perm Whether or not to send a permanent redirect header (default true)
	*/
	public static function redirect($location, $perm = true)
	{
		$perm
			? header(HTTP_Status_Code::Moved_Permanently)
			: header(HTTP_Status_Code::Temporary_Redirect);

		header(sprintf('Location: %s', $location));
		exit();
	}


	/**
	* Send a local resource to the client via HTTP
	* @param string $path The path to the local resource (file)
	* @param string $name The optional file name to send the client.  If none is provided the original filename will be used
	* @param string $mime_type The optional MIME-type header to send
	* @param return bool
	*/
	public static function file($path, $name = null, $mime_type = null)
	{
		@apache_setenv('no-gzip', 1);
		@ini_set('zlib.output_compression', 'Off');

		if(!is_file($path))
		{
			Logger::log(sprintf('Cannot access path: %s', $path), Log_Level::Error);
			return false;
		}

		$size = filesize($path);
		$name = !is_null($name) ? $name : basename($path);

		if($file = fopen($path, self::File_Mode))
		{
			header('Pragma: public');
			header('Expires: -1');
			header('Cache-Control: public, must-revalidate, post-check=0, pre-check=0');
			header(sprintf('Content-Type: %s', ($is_null($mime_type) ? 'application/octet-stream' : $mime_type)));
			header('Content-Length: ' . sprintf('%u', $size));
			header(sprintf('Content-Disposition: attachment; filename="%s"', $name));

			while(($chunk = fread($file, self::File_Chunk_Size)) !== false)
			{
				echo $chunk;
			}
			return true;
		}
		return false;
	}


	/**
	* Send a JSON-encoded representation of the data to the client and end execution.
	* @param mixed $data The payload
	* @param bool $exit Stop execution after echoing out JSON data.  Usually want to leave this to true.
	*/
	public static function return_json($data, $exit = true)
	{
		echo json_encode($data);
		if($exit)
		{
			exit();
		}
	}


	/**
	* Send an XML-encoded representation of the data to the client and end application.
	* @param mixed $data The payload
	* @param string $root_node The name of the root node in the XML document
	* @param bool $exit Stop execution after echoing out XML data.  Usually want to leave this to true.
	*/
	public static function return_xml($data, $root_node = 'data', $exit = true)
	{
		if(is_array($data))
		{
			$xml = new SimpleXMLElement(sprintf('<%s/>', $root_node));
			self::array_to_xml($data, $xml);
			echo $xml->asXML();

			if($exit)
			{
				exit();
			}
			
		}
		return false;
	}



	/**
	* Recursive function to turn an associative array into XML
	* @param mixed $data The payload
	* @param ref SimpleXMLElement &$xml_obj The reference to the SimpleXML object
	*/
	private function array_to_xml($data, &$xml_obj)
	{
		foreach($data as $key => $val)
		{
			if(is_array($val))
			{
				if(!is_numeric($key))
				{
					$node = $xml_obj->addChild("$key");
					self::array_to_xml($val, $node);
				}
				else
				{
					self::array_to_xml($val, $xml_obj);
				}
			}
			else
			{
				$xml_obj->addChild("$key", "$val");
			}
		}
	}

}