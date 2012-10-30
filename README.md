# PHP socket class

## Description

This class is a wrapper for PHP's [Socket Functions](http://www.php.net/manual/en/ref.sockets.php)

## Requirements

PHP 5.x.x  
php_sockets extension

## Usage

	// base code for a client socket
	$socket = new socket;
	$socket->create($domain, $type, $protocol)
		->connect($address, $port);

	// base code for a server socket
	$socket = new socket;
	$socket->create($domain, $type, $protocol)
		->bind($address, $port)
		->listen();

Please check [Wiki](https://github.com/godvsdeity/sockets/wiki) section for more examples and some tips and tricks. (coming soon)