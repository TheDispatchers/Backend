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

    /**
     * @param $username - username for user login
     * @param $upass - password from the user login
     * @return string|UserObject - returns status of the login, if succesfull a sessionkey, if failed it returns invalid
     */
    public function login($username, $upass){
        try{
            $stmt = $this->db->prepare("CALL login(@sessionKey,@validLogin,?,?,@userID,@carTypeID,@totalRides,@missedRides, @totalSpent, @lastRideID, @rideStreak)");
            //$stmt->bindParam(1, $sessionKey, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT);
            //$stmt->bindParam(2, $validLogin , PDO::PARAM_BOOL |PDO::PARAM_INPUT_OUTPUT);
            $stmt->bindParam(1, $username, PDO::PARAM_STR);
            $stmt->bindParam(2, $upass, PDO::PARAM_STR);

            $stmt->execute();

            $stmt = $this->db->prepare("SELECT @validLogin as boolean, @sessionKey as sessionKey, @userID as userID, @carTypeID as carTypeID, @missedRides as missedRides, @totalRides as totalRides, @totalSpent as totalSpent, @lastRideID as lastRideID, @rideStreak as rideStreak");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['boolean']){
                echo "Valid login \n";
                echo $result['totalSpent']."\n";
                $user = new UserObject($result['sessionKey'],$username);
                $user -> userID = $result['userID'];
                $user -> totalSpent = $result['totalSpent'];
                return $user;

            }
            else{
                echo "Invalid login \n";
                return "invalid";

            }

        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    /**
     * @param $ID - Driver ID
     * @param $password - Driver password
     * @return string - returns status of the login, if succesfull a sessionkey, if failed it returns invalid
     */
    public function loginDriver($ID, $password){
        try{
            $stmt = $this->db->prepare("CALL loginDriver(@sessionKey,@validLogin,?,?)");
            //$stmt->bindParam(1, $sessionKey, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT);
            //$stmt->bindParam(2, $validLogin , PDO::PARAM_BOOL |PDO::PARAM_INPUT_OUTPUT);
            $stmt->bindParam(1, $ID, PDO::PARAM_INT);
            $stmt->bindParam(2, $password, PDO::PARAM_STR);

            $stmt->execute();

            $stmt = $this->db->prepare("SELECT @validLogin as boolean, @sessionKey as sessionKey");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['boolean']){
                return $result['sessionKey'];

            }
            else{
                echo "Invalid login";
                return "invalid";

            }

        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    /**
     * @param $user_username - Desired username
     * @param $user_password - Desired password
     * @param $user_email - Desired email to be used
     * @param $user_firstname - Firstname of the user
     * @param $user_lastname - Lastname of the user
     * @param $user_carTypeID - Prefered cartype
     * @return string - Returns the status of the registration
     */
    public function register($user_username, $user_password, $user_email, $user_firstname, $user_lastname, $user_carTypeID){
        try{
            if(filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
                $stmt = $this->db->prepare("CALL register (@success, ?,?,?,?,?,?) ");
                $stmt->bindParam(1, $user_username, PDO::PARAM_STR);
                $stmt->bindParam(2, $user_password, PDO::PARAM_STR);
                $stmt->bindParam(3, $user_email, PDO::PARAM_STR);
                $stmt->bindParam(4, $user_firstname, PDO::PARAM_STR);
                $stmt->bindParam(5, $user_lastname, PDO::PARAM_STR);
                $stmt->bindParam(6, $user_carTypeID, PDO::PARAM_INT);
                $stmt->execute();

                $stmt = $this->db->prepare("SELECT @success as success");
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                echo $result['success'];

                return $result['success'];
                // $success can be returned as either string : Email and Username taken, Email taken, Username taken or Success

            }else{
                return "Your email is not valid";
            }
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    /**
     * @param $firstName - firstname of the driver
     * @param $lastName - lastname of the driver
     * @param $password - password for the login
     * @param $licensePlate - licenseplate of the car the driver is driving
     * @return mixed - Returns if it was succesfull or not
     */
    public function addNewDriver($firstName, $lastName, $password, $licensePlate){
        try{
                $stmt = $this->db->prepare("CALL addNewCar (@success, ?,?,?,?) ");
                $stmt->bindParam(1, $firstName, PDO::PARAM_STR);
                $stmt->bindParam(2, $lastName, PDO::PARAM_STR);
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

    /**
     * @param $VIN - VIN of the car
     * @param $licensePlate - Licenseplate of the car
     * @param $make - Make of the car
     * @param $model - Model of the car
     * @param $prodYear - Production year of the car
     * @param $carTypeID - ID of the cartype
     * @return mixed - Returns of the creation was succesfull.
     */
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

    /**
     * @return mixed - Returns all the licenseplates in the car table
     */
    public function getAllCarLicensesplates(){
        try{
            $stmt = $this->db->prepare("CALL getAllCarLicenseplates() ");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
            // $success can be returned as either string : Email and Username taken, Email taken, Username taken or Success

        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    /**
     * @return mixed - Returns all the cartypeNames (not ID).
     */
    public function getAllCarTypes(){
        try{
            $stmt = $this->db->prepare("CALL getAllCarTypes() ");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
            // $success can be returned as either string : Email and Username taken, Email taken, Username taken or Success

        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    public function finishRide($userID, $driverID, $rideDate, $price){
        try{
            $stmt = $this->db->prepare("CALL newRideHistory(?,?,?,?) ");
            $stmt->bindParam(1, $userID, PDO::PARAM_INT);
            $stmt->bindParam(2, $driverID, PDO::PARAM_INT);
            $stmt->bindParam(3, $rideDate, PDO::PARAM_STR);
            $stmt->bindParam(4, $price, PDO::PARAM_STR);

            $stmt->execute();

            return "inserted";
            // $success can be returned as either string : Email and Username taken, Email taken, Username taken or Success

        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

}