<?php
/**
 * Created by PhpStorm.
 * User: thacrash
 * Date: 5/30/17
 * Time: 10:13 AM
 */

/**
 * Class UserObject Userobject with the potentially needed variables
 */
class UserObject
{
    public $sessionKey;
    public $username;
    public $userID;
    public $fromlat;
    public $fromlng;
    public $tolat;
    public $tolng;
    public $status;
    public $priority;
    public $carTypeID;
    public $totalRides;
    public $missedRides;
    public $totalSpent;
    public $lastRideID;
    public $rideStreak;
    public $clientsocket;


    function __construct($sessionKey, $username)
    {
        $this->sessionKey = $sessionKey;
        $this->username = $username;

    }

}