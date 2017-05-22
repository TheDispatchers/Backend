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
            $stmt = $this->db->prepare("SELECT ID, Password 
                                        FROM User 
                                        WHERE Username=:username
                                        LIMIT 1");
            $stmt->execute(array(':username'=>$username));
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if($stmt->rowCount() > 0){
                if(password_verify($upass, $userRow['password'])){
                    $_SESSION['user_session'] = $userRow['ID'];
                    return "Logged in";
                }else{
                    return "Invalid login";
                }
            }else{
                echo 'Error no user found';
            }
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    public function register($user_username, $user_password, $user_email, $user_firstname, $user_lastname, $user_cartype){
        try{
            if(filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
                $stmt = $this->db->prepare("INSERT INTO user(Username, Password, Email, Firstname, Lastname,CarType) 
                                            VALUES(:uusername, :upass, :uemail, :ufirstname, :ulastname, ucartype)");
                $stmt->bindParam(":uusername", $user_username);
                $stmt->bindParam(":upass", $user_password);
                $stmt->bindParam(":uemail", $user_email);
                $stmt->bindParam(":ufirstname", $user_firstname);
                $stmt->bindParam(":ulastname", $user_lastname);
                $stmt->bindParam(":ucartype", $user_cartype);

                $stmt->execute();
                return true;
            }else{
                echo "Your email is not correct";
                return false;
            }
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }



}