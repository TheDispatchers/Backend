<?php
/**
 * Created by PhpStorm.
 * User: thacrash
 * Date: 5/17/17
 * Time: 12:40 PM
 */

$host    = "86.52.212.76";
$port    = 8113;
$message = "Hello Servers";
echo "Message To server :".$message;
// create socket
$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
// connect to server
$result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");
// send string to server
socket_write($socket, $message, strlen($message)) or die("Could not send data to server\n");
// get server response
$result = socket_read ($socket, 1024) or die("Could not read server response\n");
echo "Reply From Server  :".$result;
// close socket
socket_close($socket);