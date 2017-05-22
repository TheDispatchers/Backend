<?php
/**
 * Created by PhpStorm.
 * User: thacrash
 * Date: 5/17/17
 * Time: 12:40 PM
 */


if(!($sock = socket_create(AF_INET, SOCK_STREAM, 0)))
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);

    die("Couldn't create socket: [$errorcode] $errormsg \n");
}

echo "Socket created \n";

//Connect socket to remote server
if(!socket_connect($sock , '86.52.212.76' , 8113))
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);

    die("Could not connect: [$errorcode] $errormsg \n");
}

echo "Connection established \n";

$message = "Testing";

//Send the message to the server
if( ! socket_send ( $sock , $message , strlen($message) , 0))
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);

    die("Could not send data: [$errorcode] $errormsg \n");
}

echo "Message send successfully \n";


//print the received message

$myObj->Function = "LogIn";
$myObj->username = "User";
$myObj->password = "Password";

$myJSON = json_encode($myObj);
echo $myJSON;

//Send the message to the server
if( ! socket_send ( $sock , $myJSON , strlen($myJSON) , 0))
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);

    die("Could not send data: [$errorcode] $errormsg \n");
}

echo "Message send successfully \n";

//Now receive reply from server
if(socket_recv ( $sock , $buf , 2045 , MSG_WAITFORONE ) === FALSE)
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);

    die("Could not receive data: [$errorcode] $errormsg \n");
}

//print the received message
echo $buf;