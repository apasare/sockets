<?php

require_once '../socket.php';

header('Content-Type: text/plain');

try{
	$client = new socket(array(
		'_create' => true,
		'_connect' => true,
		'address' => 'www.google.ro',
		'port' => '80'
	));
	
	$message  = "GET / HTTP/1.0\r\n";
	$message .= "HOST: www.google.ro\r\n";
	$message .= "\r\n";
	$client->write($message);
	
	$client->setOption(SOL_SOCKET, SO_RCVTIMEO, array('sec' => 1, 'usec' => 0));
	$output = '';
	while(strlen($buffer = $client->recv())){
		$output .= $buffer;
	}
	echo $output;
}catch(Exception $e){
	echo $e->getCode().' '.$e->getMessage();
	echo $e->getTraceAsString();
}