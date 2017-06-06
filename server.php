<?php
/**
 * Created by PhpStorm.
 * User: PkStrSk
 * Date: 21.5.2017
 * Time: 19:13
 */

include ('ServerSQLBridge.php');
include ('shmop.php');

error_reporting(~E_NOTICE);
set_time_limit(0);


$serverSQLBridge = new ServerSQLBridge($dbFac);

$address = "0.0.0.0";
$port = 8114;
$max_clients = 10;


if (!($sock = socket_create(AF_INET, SOCK_STREAM, 0))) {
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);

    die("Couldn't create socket: [$errorcode] $errormsg \n");
}

socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
socket_set_option($sock, SOL_SOCKET, SO_REUSEPORT, 1);


echo "Socket created \n";

// Bind the source address
if (!socket_bind($sock, $address, 8113)) {
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);

    die("Could not bind socket : [$errorcode] $errormsg \n");
}

echo "Socket bind OK \n";

if (!socket_listen($sock, 10)) {
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);

    die("Could not listen on socket : [$errorcode] $errormsg \n");
}

echo "Socket listen OK \n";

echo "Waiting for incoming connections... \n";

//array of client sockets
$client_socks = array();
$logged_in_socks = array();

//array of sockets to read
$read = array();

//start loop to listen for incoming connections and process existing connections
while (true) {
    //prepare array of readable client sockets
    $read = array();
    //first socket is the master socket
    $read[0] = $sock;

    //now add the existing client sockets
    for ($i = 0; $i < $max_clients; $i++) {
        if ($client_socks[$i] != null) {
            $read[$i + 1] = $client_socks[$i];
        }
    }

    //now call select - blocking call
    if (socket_select($read, $write, $except, null) === false) {
        $errorcode = socket_last_error();
        $errormsg = socket_strerror($errorcode);

        die("Could not listen on socket : [$errorcode] $errormsg \n");
    }

    //if ready contains the master socket, then a new connection has come in
    if (in_array($sock, $read)) {
        for ($i = 0; $i < $max_clients; $i++) {
            if ($client_socks[$i] == null) {
                $client_socks[$i] = socket_accept($sock);

                //display information about the client who is connected
                if (socket_getpeername($client_socks[$i], $address, $port)) {
                    echo "Client $address : $port is now connected to us. \n";
                }

                //Send Welcome message to client
                break;
            }
        }
    }


    //check each client if they send any data
    for ($i = 0; $i < $max_clients; $i++) {
        if (in_array($client_socks[$i], $read)) {
            $Json = socket_read($client_socks[$i], 1024000);

            echo $Json;

            $input_decoded = json_decode($Json);
            $function = $input_decoded->function;
            echo "Server side function read :" . $function;
            $output = $serverSQLBridge->getMethod($Json,$client_socks[$i],$read[0]);
            echo $output;


            if ($Json == null) {
                //zero length string meaning disconnected, remove and close the socket

                socket_close($client_socks[$i]);
                unset($client_socks[$i]);
            } else {

                // orderRide and driverUpdate needs to be handled differently, since they don't neccesarily sent a response back immediately.
                if ($input_decoded->function == "orderRide" or $input_decoded->function == "driverUpdate" ){
                    $array=$serverSQLBridge->checkAvailability();
                    echo json_encode($array)."\n";
                    $user = $array[0];
                    $driver = $array[1];

                    echo json_encode($user->userID)."\n";
                    echo json_encode($driver->ID)."\n";
                    if($user != null) {
                        echo "Sending output to user: ". $user->clientsocket. "    Sending output to driver:". $driver->clientsocket."\n";
                        socket_write($user->clientsocket, $array[2]);
                        socket_write($driver->clientsocket,$array[3]);
                    }

                    $serverSQLBridge->writeToShmop();
                }
                else {
                    echo "Sending output to client \n" . $output;
                    //send response to client
                    socket_write($client_socks[$i], $output);
                    $serverSQLBridge->writeToShmop();
                }


            }
        }
    }

}