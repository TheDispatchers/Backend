<?php
/**
 * Created by PhpStorm.
 * User: thacrash
 * Date: 5/18/17
 * Time: 11:43 PM
 */

/**
 * Generate the relevant information to establish a connection, which is then passed to the DBfacade
 */

$DB_host = "86.52.212.76";
$DB_user = "DMU4";
$DB_pass = "github";
$DB_name = "iTax";


try{
    $DB_connect = new PDO("mysql:host={$DB_host};dbname={$DB_name}", $DB_user, $DB_pass);
    $DB_connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    echo $e->getMessage();
}


include_once 'DBFacade.php';

$dbFac = new DBFacade($DB_connect);