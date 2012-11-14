<?php

/**
 * WARNING!
 * In order for this example to work, please configure your
 * NIC's Wake-On-Lan ability properly. Usually it "wakes up"
 * computer from one of the following power states: sleep, 
 * hibernate or standby. If you want to wake up the computer
 * from "shutdown" power state, you have to enable it first
 * from BIOS.
 */

require_once '../socket.php';

header('Content-Type: text/plain');

// broadcasting ip address
$ip = '192.168.1.255'; // change it to yours
//computer's physical address
$mac = 'XX-XX-XX-XX-XX-XX'; // change it to yours
$mac = explode('-', $mac);
foreach($mac as &$byte){
    $byte = chr(hexdec($byte));
}
$mac = implode('', $mac);

try{
    $client = new socket(array(
        '_client' => true,
        'type' => SOCK_DGRAM,
        'protocol' => SOL_UDP,
        'address' => $ip,
        'port' => 0
    ));
    $client->setOption(SOL_SOCKET, SO_BROADCAST, true);

    $rawData = '';
    for($i=0; $i<6; $i++){
        $rawData .= "\xFF";
    }
    for($i=0; $i<16; $i++){
        $rawData .= $mac;
    }
    $client->write($rawData);
    
    echo 'Magic packet('.strlen($rawData).' bytes) broadcasted to '.$ip.'.';
}catch(Exception $e){
    echo $e->getCode().' '.$e->getMessage();
    echo $e->getTraceAsString();
}