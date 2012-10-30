<?php

class socket{
	const INVALID_RESOURCE_ERROR = "Invalid socket resource\r\n";
	
	private $_socket;
	
	protected $_data = array(
		'_create' => false,
		'_connect' => false,
		'_bind' => false,
		'domain' => AF_INET,
		'type' => SOCK_STREAM,
		'protocol' => SOL_TCP,
		'address' => '',
		'port' => 0
	);
	
	/**
	 * socket::__construct()
	 * 
	 * @param array $params
	 * @return void
	 */
	function __construct($params = null){
		if(isset($params['_socket'])){
			$this->_socket = $params['_socket'];
			//$this->_data = array_merge($this->_data, $this->getSockName());
			
			return;
		}
		
		if(is_array($params)){
			$this->_data = array_merge($this->_data, $params);
		}
		
		if($this->_data['_create']){
			$this->create();
		}
		
		if($this->_data['_connect']){
			$this->connect();
		}else if($this->_data['_bind']){
			$this->bind();
		}
	}
	
	/**
	 * Create a socket (endpointeger for communication)
	 * 
	 * @param integer $domain (AF_INET || AF_INET6 || AF_UNIX)
	 * @param integer $type (SOCK_STREAM || SOCK_DGRAM || SOCK_SEQPACKET || SOCK_RAW || SOCK_RDM)
	 * @param integer $protocol (SOL_TCP || SOL_UDP || SOL_SOCKET)
	 * @return object socket
	 */
	function create($domain = '', $type = '', $protocol = ''){
		$this->close();
		
		if(!empty($domain) && $domain != $this->_data['domain'])
			$this->_data['domain'] = $domain;
		if(!empty($type) && $type != $this->_data['type'])
			$this->_data['type'] = $type;
		if(!empty($protocol) && $protocol != $this->_data['protocol'])
			$this->_data['protocol'] = $protocol;
		
		$this->_socket = @socket_create($this->_data['domain'], $this->_data['type'], $this->_data['protocol']);
		if($this->_socket === false){
			$this->throwException('socket::create() failed, reason: ');
		}
		
		return $this;
	}
	
	/**
	 * Initiates a connection on a socket
	 * 
	 * @param string $address (ip address || hostname)
	 * @param integer $port
	 * @return object socket
	 */
	function connect($address = '', $port = ''){
		if(!is_resource($this->_socket)){
			throw new Exception(self::INVALID_RESOURCE_ERROR);
		}
		
		if(!empty($address) && $address != $this->_data['address'])
			$this->_data['address'] = $address;
		if(!empty($port) && $port != $this->_data['port'])
			$this->_data['port'] = (int)$port;
		
		if(@socket_connect($this->_socket, $this->_data['address'], $this->_data['port']) === false){
			$this->throwException('socket::connect() failed, reason: ');
		}
		
		return $this;
	}
	
	/**
	 * Queries the remote side of the given socket which may either result in host/port or in a Unix filesystem path, dependent on its type
	 * 
	 * @return array
	 */
	function getPeerName(){
		if(!is_resource($this->_socket)){
			throw new Exception(self::INVALID_RESOURCE_ERROR);
		}
		
		$return = array(
			'address' => '',
			'port' => ''
		);
		$response = @socket_getpeername($this->_socket, $return['address'], $return['port']);
		if($response === false){
			$this->throwException('socket::getPeerName() failed, reason: ');
		}
		
		return $return;
	}
	
	/**
	 * Binds a name to a socket
	 * 
	 * @param string $address (ip address || hostname)
	 * @param integer $port
	 * @return object socket
	 */
	function bind($address = '', $port = ''){
		if(!is_resource($this->_socket)){
			throw new Exception(self::INVALID_RESOURCE_ERROR);
		}
		
		if(!empty($address) && $address != $this->_data['address'])
			$this->_data['address'] = $address;
		if(!empty($port) && $port != $this->_data['port'])
			$this->_data['port'] = (int)$port;
		
		if(@socket_bind($this->_socket, $this->_data['address'], $this->_data['port']) === false){
			$this->throwException('socket::bind() failed, reason: ');
		}
		
		return $this;
	}
	
	/**
	 * Listens for a connection on a socket
	 * 
	 * @param integer $backlog
	 * @return object socket
	 */
	function listen($backlog = 0){
		if(!is_resource($this->_socket)){
			throw new Exception(self::INVALID_RESOURCE_ERROR);
		}
		
		if(@socket_listen($this->_socket, $backlog) === false){
			$this->throwException('socket::listen() failed, reason: ');
		}
		
		return $this;
	}
	
	/**
	 * Accepts a connection on a socket
	 * 
	 * @return object socket
	 */
	function accept(){
		if(!is_resource($this->_socket)){
			throw new Exception(self::INVALID_RESOURCE_ERROR);
		}
		
		if(($socket = @socket_accept($this->_socket)) === false){
			$this->throwException('socket::accept() failed, reason: ');
		}
		
		return new socket(array('_socket' => $socket));
	}
	
	/**
	 * Queries the local side of the given socket which may either result in host/port or in a Unix filesystem path, dependent on its type
	 * 
	 * @return array
	 */
	function getSockName(){
		if(!is_resource($this->_socket)){
			throw new Exception(self::INVALID_RESOURCE_ERROR);
		}
		
		$return = array(
			'address' => '',
			'port' => ''
		);
		$response = @socket_getsockname($this->_socket, $return['address'], $return['port']);
		if($response === false){
			$this->throwException('socket::getSockName() failed, reason: ');
		}
		
		return $return;
	}
	
	/**
	 * Reads a maximum of length bytes from a socket
	 * 
	 * @param integer $length
	 * @param integer $type (PHP_BINARY_READ || PHP_NORMAL_READ)
	 * @return string
	 */
	function read($length = 2048, $type = PHP_NORMAL_READ){
		if(!is_resource($this->_socket)){
			throw new Exception(self::INVALID_RESOURCE_ERROR);
		}
		
		$buffer = @socket_read($this->_socket, $length, $type);
		if($buffer === false){
			$this->throwException('socket::read() failed, reason: ');
		}
		
		return $buffer;
	}
	
	/**
	 * Write to a socket
	 * 
	 * @param string $message
	 * @return object socket
	 */
	function write($message){
		if(!is_resource($this->_socket)){
			throw new Exception(self::INVALID_RESOURCE_ERROR);
		}
		
		$bytes = @socket_write($this->_socket, $message, strlen($message));
		if($bytes === false){
			$this->throwException('socket::write() failed, reason: ');
		}
		
		return $this;
	}
	
	/**
	 * Receives data from a connected socket
	 * 
	 * @param integer $length
	 * @param integer $flags (MSG_OOB | MSG_PEEK | MSG_WAITALL | MSG_DONTWAIT)
	 * @return string
	 */
	function recv($length = 2048, $flags = MSG_WAITALL){
		if(!is_resource($this->_socket)){
			throw new Exception(self::INVALID_RESOURCE_ERROR);
		}
		
		$buffer = '';
		$bytes = @socket_recv($this->_socket, $buffer, $length, $flags);
		if($bytes === false){
			$this->throwException('socket::recv() failed, reason: ');
		}
		
		return $buffer;
	}
	
	/**
	 * Sends data to a connected socket
	 * 
	 * @param string $message
	 * @param integer $flags (MSG_OOB | MSG_EOR | MSG_EOF | MSG_DONTROUTE)
	 * @return object socket
	 */
	function send($message, $flags = MSG_DONTROUTE){
		if(!is_resource($this->_socket)){
			throw new Exception(self::INVALID_RESOURCE_ERROR);
		}
		
		$bytes = @socket_send($this->_socket, $message, strlen($message), $flags);
		if($bytes === false){
			$this->throwException('socket::send() failed, reason: ');
		}
		
		return $this;
	}
	
	/**
	 * Gets socket options for the socket
	 * 
	 * @param integer $level
	 * @param integer $optname
	 * @return mixed
	 */
	function getOption($level, $optname){
		if(!is_resource($this->_socket)){
			throw new Exception(self::INVALID_RESOURCE_ERROR);
		}
		
		$optval = @socket_get_option($this->_socket, $level, $optname);
		if($optval === false){
			$this->throwException('socket::getOption() failed, reason: ');
		}
		
		return $optval;
	}
	
	/**
	 * Sets socket options for the socket
	 * 
	 * @param integer $level (SOL_SOCKET || SOL_TCP || SOL_UDP)
	 * @param integer $optname (SO_DEBUG || SO_BROADCAST || ...)
	 * @param mixed $optval
	 * @return
	 */
	function setOption($level, $optname, $optval){
		if(!is_resource($this->_socket)){
			throw new Exception(self::INVALID_RESOURCE_ERROR);
		}
		
		if(@socket_set_option($this->_socket, $level, $optname, $optval) === false){
			$this->throwException('socket::setOption() failed, reason: ');
		}
		
		return $this;
	}
	
	/**
	 * Sets blocking mode on a socket resource
	 * 
	 * @return object socket
	 */
	function setBlock(){
		if(!is_resource($this->_socket)){
			throw new Exception(self::INVALID_RESOURCE_ERROR);
		}
		
		if(@socket_set_block($this->_socket) === false){
			$this->throwException('socket::setBlock() failed, reason: ');
		}
		
		return $this;
	}
	
	/**
	 * Sets nonblocking mode for a socket resource
	 * 
	 * @return object socket
	 */
	function setNonBlock(){
		if(!is_resource($this->_socket)){
			throw new Exception(self::INVALID_RESOURCE_ERROR);
		}
		
		if(@socket_set_nonblock($this->_socket) === false){
			$this->throwException('socket::setNonBlock() failed, reason: ');
		}
		
		return $this;
	}
	
	/**
	 * Shuts down a socket for receiving, sending, or both
	 * 
	 * @param integer $how (0 - Shutdown socket reading | 1 - Shutdown socket writing | 2 - Shutdown socket reading and writing)
	 * @return void
	 */
	function shutdown($how = 2){
		if(is_resource($this->_socket)){
			if(@socket_shutdown($this->_socket, $how) === false){
				$this->throwException('socket::shutdown() failed, reason: ');
			}
		}
		
		return $this;
	}
	
	/**
	 * Closes a socket resource
	 * 
	 * @return object socket
	 */
	function close(){
		if(is_resource($this->_socket)){
			if(@socket_close($this->_socket) === false){
				$this->throwException('socket::close() failed, reason: ');
			}
		}
		
		return $this;
	}
	
	/**
	 * socket::__destruct()
	 * 
	 * @return void
	 */
	function __destruct(){
		$this->close();
	}
	
	/**
	 * Return a string describing a socket error
	 * 
	 * @return string
	 */
	function getError(){
		if(is_resource($this->_socket))
			return socket_strerror(@socket_last_error($this->_socket));
		
		return socket_strerror(socket_last_error());
	}
	
	/**
	 * Returns the last error on the socket 
	 * 
	 * @return integer
	 */
	function getErrorCode(){
		if(is_resource($this->_socket))
			return @socket_last_error($this->_socket);
		
		return socket_last_error();
	}
	
	/**
	 * socket::throwException()
	 * 
	 * @param string $message
	 * @return void
	 */
	protected function throwException($message = ''){
		throw new Exception($message.$this->getError(), $this->getErrorCode());
	}
}
