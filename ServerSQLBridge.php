<?php
/**
 * Created by PhpStorm.
 * User: thacrash
 * Date: 5/22/17
 * Time: 3:21 PM
 */


require ("DBFacade.php");

Class ServerSQLBridge
{
    private $dbFac;


    public function __construct(DBFacade $dbFac) {

        $this->dbFac = $dbFac;

    }

    function getMethod($Json)
    {
        $string = null;

        $input_decoded = json_decode($Json);
        $Function = $input_decoded->Function;

        switch ($Function) {
            case "register":
                $string = register($Json);
                break;
            case "login":
                $string = login($Json);
                break;

            default:
                echo "lol";
        }
        return $string;

    }

    function register($Json)
    {
        $input_decoded = json_decode($Json);
        $username = $input_decoded->username;
        $password = $input_decoded->password;
        $email = $input_decoded->email;
        $firstname = $input_decoded->firstname;
        $lastname = $input_decoded->lastname;
        $cartype = $input_decoded->cartype;
        $this-> dbFac ->register($username,$password,$email,$firstname,$lastname,$cartype);


    }

    function login($Json)
    {

        $input_decoded = json_decode($Json);
        $username = $input_decoded->username;
        $password = $input_decoded->password;
        echo "Running DB call";
        $string = $this-> dbFac -> login ($username, $password);

        return $string;

    }

}