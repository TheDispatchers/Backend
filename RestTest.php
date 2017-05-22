<?php
/**
 * Created by PhpStorm.
 * User: thacrash
 * Date: 5/20/17
 * Time: 1:37 AM
 */

// not an actual part of the API, more like a tester
$url = "http://localhost/projects/untitled/R";
$user = "setnewname";
$parameter = "Keith";
$myVars = 'function=' . $user. '&param='. $parameter;
$ch = curl_init($url);
curl_setopt( $ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $myVars);
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt( $ch, CURLOPT_HEADER, 0);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
