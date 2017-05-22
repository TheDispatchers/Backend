<?php
/**
 * Created by PhpStorm.
 * User: thacrash
 * Date: 5/19/17
 * Time: 4:29 PM
 */

$fp = stream_socket_client("tcp://86.52.212.76:8113", $errno, $errstr, 30);
if (!$fp)
{
    echo "$errstr ($errno)<br />n";
}
else
{
    $out = "GET / HTTP/1.1rnrn";
    ///Send data
    fwrite($fp, $out);

    ///Receive data - in small chunks :)
    while (!feof($fp))
    {
        echo fgets($fp, 1024000);
    }

    fclose($fp);
}