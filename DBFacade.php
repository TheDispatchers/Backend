<?php

/**
 * Created by PhpStorm.
 * User: thacrash
 * Date: 5/18/17
 * Time: 11:41 PM
 */

include('DBconfig.php');

class DBFacade
{
    private $db;
    function __construct($DB_connect)
    {
        $this->db = $DB_connect;
    }


    public function login($username, $upass){
        try{
            $stmt = $this->db->prepare("CALL login(@sessionKey,@validLogin,?,?)");
            //$stmt->bindParam(1, $sessionKey, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT);
            //$stmt->bindParam(2, $validLogin , PDO::PARAM_BOOL |PDO::PARAM_INPUT_OUTPUT);
            $stmt->bindParam(1, $username, PDO::PARAM_STR);
            $stmt->bindParam(2, $upass, PDO::PARAM_STR);

            $stmt->execute();

            $stmt = $this->db->prepare("SELECT @validLogin as boolean, @sessionKey as sessionkey");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['boolean']){
                return $result['sessionKey'];

            }
            else{
                echo "Invalid login";
                return NULL;

            }

        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    public function loginDriver($ID, $password){
        try{
            $stmt = $this->db->prepare("CALL loginDriver(@sessionKey,@validLogin,?,?)");
            //$stmt->bindParam(1, $sessionKey, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT);
            //$stmt->bindParam(2, $validLogin , PDO::PARAM_BOOL |PDO::PARAM_INPUT_OUTPUT);
            $stmt->bindParam(1, $ID, PDO::PARAM_INT);
            $stmt->bindParam(2, $password, PDO::PARAM_STR);

            $stmt->execute();

            $stmt = $this->db->prepare("SELECT @validLogin as boolean, @sessionKey as sessionkey");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['boolean']){
                return $result['sessionKey'];

            }
            else{
                echo "Invalid login";
                return NULL;

            }

        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }


    public function register($user_username, $user_password, $user_email, $user_firstname, $user_lastname, $user_carTypeID){
        try{
            if(filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
                $stmt = $this->db->prepare("CALL register (@success, user_username=: username, user_password =: password, user_email =: email, user_firstname =:firstname, user_lastname=:lastanme,user_carTypeID =: carTypeID) ");
                $stmt->bindParam(":username", $user_username, PDO::PARAM_STR);
                $stmt->bindParam(":password", $user_password, PDO::PARAM_STR);
                $stmt->bindParam(":email", $user_email, PDO::PARAM_STR);
                $stmt->bindParam(":firstname", $user_firstname, PDO::PARAM_STR);
                $stmt->bindParam(":lastname", $user_lastname, PDO::PARAM_STR);
                $stmt->bindParam(":carTypeID", $user_carTypeID, PDO::PARAM_INT);
                $stmt->execute();

                $stmt = $this->db->prepare("SELECT @success as success");
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                return $result['success'];
                // $success can be returned as either string : Email and Username taken, Email taken, Username taken or Success

            }else{
                return "Your email is not valid";
            }
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }


    public function addNewDriver($firstName, $lastname, $password, $licensePlate){
        try{
                $stmt = $this->db->prepare("CALL addNewCar (@success, ?,?,?,?) ");
                $stmt->bindParam(1, $firstName, PDO::PARAM_STR);
                $stmt->bindParam(2, $lastname, PDO::PARAM_STR);
                $stmt->bindParam(3, $password, PDO::PARAM_STR);
                $stmt->bindParam(4, $licensePlate, PDO::PARAM_STR);
                $stmt->execute();

                $stmt = $this->db->prepare("SELECT @success as success");
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                return $result['success'];
                // $success can be returned as either string : Email and Username taken, Email taken, Username taken or Success

        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    public function addNewCar($VIN, $licensePlate, $make, $model, $prodYear, $carTypeID){
        try{
            $stmt = $this->db->prepare("CALL addNewCar (@success, ?,?,?,?,?,?) ");
            $stmt->bindParam(1, $VIN, PDO::PARAM_STR);
            $stmt->bindParam(2, $licensePlate, PDO::PARAM_STR);
            $stmt->bindParam(3, $make, PDO::PARAM_STR);
            $stmt->bindParam(4, $model, PDO::PARAM_STR);
            $stmt->bindParam(4, $prodYear, PDO::PARAM_INT);
            $stmt->bindParam(4, $carTypeID, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $this->db->prepare("SELECT @success as success");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['success'];
            // $success can be returned as either string : Email and Username taken, Email taken, Username taken or Success

        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }


}