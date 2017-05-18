<?php
/**
 * Created by PhpStorm.
 * User: PkStrSk
 * Date: 17.5.2017
 * Time: 15:47
 */
/*************************************/
/********Socket Server*********************/

Class server2
{

    public function Display()
    {

        echo "<html>\n<head>\n";
        $this->DisplayTitle();
        $this->DisplayKeywords();
        $this->DisplayKeywords();
        $this->DisplayStyles();
        echo "</head>\n<bopy>";
        $this->DisplayHeader();
        $this->DisplayMenu($this->buttons);
        echo $this->content;
        $this->DisplayFooter();
        echo "</bopy>\n</html>\n";


    }
}

$ServerOnBool=False;


if(isset($_POST['ServerOn'])){
    $ServerOnBool=True;
}

if(isset($_POST['ServerOff'])){
    $ServerOnBool=False;
}


while ($ServerOnBool) {
    set_time_limit(0);
// Set the ip and port we will listen on
    $address = '0.0.0.0';
    $port = 8113;
// Create a TCP Stream socket
    $sock = socket_create(AF_INET, SOCK_STREAM, 0); // 0 for  SQL_TCP
// Bind the socket to an address/port
    if (!socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1)) {
        echo socket_strerror(socket_last_error($socket));
        exit;
    }
    socket_bind($sock, $address, $port) or die('Could not bind to address');  //0 for localhost
// Start listening for connections
    socket_listen($sock);
//loop and listen
    while (true) {
        /* Accept incoming  requests and handle them as child processes */
        $client = socket_accept($sock);
// Read the input  from the client – 1024000 bytes
        $input = socket_read($client, 1024000);
// Display output  back to client
        socket_write($client, $input);
        socket_close($client);
    }
// Close the master sockets
    socket_close($sock);
}


$server2 = new Server2();

$server2->content = $server2->content."<form action='' method='post'>
        <input type='submit' value='On' name='ServerOn'>
         <input type='submit' value='Off' name='ServerOff'>";

$server2->Display();