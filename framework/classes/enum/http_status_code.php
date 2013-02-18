<?php

/**
* Helper enumeration containing a common list of frequently used HTTP response strings ready
* to be sent with header()
*/
class HTTP_Status_Code extends Enum
{
	const OK = 'HTTP/1.1 200 OK';
	const Moved_Permanently = 'HTTP/1.1 301 Moved Permanently';
	const Found = 'HTTP/1.1 302 Found';
	const Temporary_Redirect = 'HTTP/1.1 307 Temporary Redirect';
	const Bad_Request = 'HTTP/1.1 400 Bad Request';
	const Unauthorized = 'HTTP/1.1 401 Unauthorized';
	const Not_Found = 'HTTP/1.1 404 Not Found';
	const Method_Not_Allowed = 'HTTP/1.1 405 Method Not Allowed';
	const Request_Timeout = 'HTTP/1.1 408 Request Timeout';
	const Internal_Server_Error = 'HTTP/1.1 500 Internal Server Error';
	const Not_Implemented = 'HTTP/1.1 501 Not Implemented';
	const Bad_Gateway = 'HTTP/1.1 502 Bad Gateway';
	const Service_Unavailable = 'HTTP/1.1 503 Service Unavailable';
	const Gateway_Timeout = 'HTTP/1.1 504 Gateway Timeout';
}