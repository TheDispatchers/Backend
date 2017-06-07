<?php
/**
 * Created by PhpStorm.
 * User: thacrash
 * Date: 5/31/17
 * Time: 6:03 PM
 */

/**
 * Class taxiObject Taxiobject with the potentially needed variables
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



    function __construct($sessionKey, $ID)
    {
        $this->sessionKey = $sessionKey;
        $this->ID = $ID;
        $this->lat = null;
        $this->lng = null;
        $this->status = null;

    }

}
