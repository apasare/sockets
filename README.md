# PHP socket class

`socket` is a wrapper class for PHP's [Socket Functions](http://www.php.net/manual/en/ref.sockets.php)

## Requirements

PHP 5.x.x  
php_sockets extension

## Usage

	// base code for a client socket
	try{
		$socket = new socket;
		$socket->create($domain, $type, $protocol)
			->connect($address, $port);
	}catch(Exception $e){
		// Log error
	}

	// base code for a server socket
	try{
		$socket = new socket;
		$socket->create($domain, $type, $protocol)
			->bind($address, $port)
			->listen();
	}catch(Exception $e){
		// Log error
	}

Please check [Wiki](https://github.com/godvsdeity/sockets/wiki) section for more examples and some tips and tricks. (coming soon)