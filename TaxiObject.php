<?php
/**
 * Created by PhpStorm.
 * User: thacrash
 * Date: 5/31/17
 * Time: 6:03 PM
 */
class taxiObject
{
    public $sessionKey;
    public $ID;
    public $lat;
    public $lng;
    public $status;
    public $userID;
    public $clientsocket;
    public $mastersocket;



    function __construct($sessionKey, $ID)
    {
        $this->sessionKey = $sessionKey;
        $this->ID = $ID;
        $this->lat = null;
        $this->lng = null;
        $this->status = null;

    }

}