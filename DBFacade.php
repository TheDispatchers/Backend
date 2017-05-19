<?php

/**
 * Created by PhpStorm.
 * User: thacrash
 * Date: 5/18/17
 * Time: 11:41 PM
 */

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
                                        FROM user 
                                        WHERE username=:username
                                        LIMIT 1");
            $stmt->execute(array(':username'=>$username));
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if($stmt->rowCount() > 0){
                if(password_verify($upass, $userRow['password'])){
                    $_SESSION['user_session'] = $userRow['ID'];
                    return true;
                }else{
                    return false;
                }
            }else{
                echo 'Error no user found';
            }
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

}