<?php
/**
 * Created by PhpStorm.
 * User: thacrash
 * Date: 5/22/17
 * Time: 3:21 PM
 */


include ("DBFacade.php");
include ('UserObject.php');
include('TaxiObject.php');

Class ServerSQLBridge
{
    private $dbFac;
    public $userArray=array();
    public $driverArray=array();

    public function __construct(DBFacade $dbFac) {

        $this->dbFac = $dbFac;


    }

    function getMethod($Json, $clientsocket, $mastersocket)
    {
        $string = null;

        $input_decoded = json_decode($Json);
        $function = $input_decoded->function;

        echo $function."\n";

        $inDriverArray = false;
        $inUserArray = false;

        foreach ($this->driverArray as $driver){
            if($input_decoded->sessionKey==$driver[0]->sessionKey){
                $inDriverArray = true;
            }
        }

        foreach ($this->userArray as $user){
            if($input_decoded->sessionKey==$user[0]->sessionKey){
                $inUserArray = true;
            }
        }


        if ($inUserArray OR $inDriverArray) {
            switch ($function) {
                case "register":
                    echo "Entered register \n";
                    $string = ServerSQLBridge::register($Json);
                    break;
                case "login":
                    echo "Entered login case \n";
                    $string = ServerSQLBridge::login($Json);
                    break;
                case "loginDriver":
                    echo "Entered loginDriver case \n";
                    $string = ServerSQLBridge::loginDriver($Json);
                    break;

                case "addNewDriver":
                    echo "Entered addNewDriver \n";
                    $string = ServerSQLBridge::addNewDriver($Json);
                    break;
                case "orderRide":{
                    echo "Entered orderRide \n";
                    $string = ServerSQLBridge::orderRide($Json,$clientsocket,$mastersocket);
                    break;
                }
                case "driverUpdate":{
                    echo "Entered driverUpdate \n";
                    $string = ServerSQLBridge::driverUpdate($Json,$clientsocket,$mastersocket);
                    break;
                }
                case "getDistanceTimePrice":{
                    echo "Entered driverUpdate \n";
                    $string = ServerSQLBridge::getDistanceTimePrice($Json);
                    break;
                }

                default:
                    echo $function." doesn't exist"."\n";
                    $string = 'No function idiot';
                    break;
            }
            return $string;
        }
        else {
            switch ($function) {
                case "loginDriver":
                    echo "Entered loginDriver case \n";
                    $string = ServerSQLBridge::loginDriver($Json);
                    break;
                case "register":
                    echo "Entered register \n";
                    $string = ServerSQLBridge::register($Json);
                    break;
                case "login":
                    echo "Entered login case \n";
                    $string = ServerSQLBridge::login($Json);
                    break;
                default:
                    echo $function." doesn't exist"."\n";
                    $string = 'No function';
                    break;
            }
            return $string;
        }


    }

    function register($Json)
    {
        $input_decoded = json_decode($Json);
        $username = $input_decoded->username;
        $password = $input_decoded->password;
        $email = $input_decoded->email;
        $firstname = $input_decoded->firstName;
        $lastname = $input_decoded->lastName;
        $cartypeID = $input_decoded->carTypeID;
        $string = $this-> dbFac ->register($username,$password,$email,$firstname,$lastname,$cartypeID);
        echo"        Got this string from register :".$string."\n";
        return  $string;


    }

    function login($Json)
    {
        $input_decoded = json_decode($Json);
        $username = $input_decoded->username;
        $password = $input_decoded->password;
        echo "Running DB call";
        $user = $this-> dbFac -> login ($username, $password);
        echo $user -> sessionkey;



        if($user!= "invalid"){

            $this->userArray[] = array($user);
            //array_push($this->userArray,$user);
            return $user->sessionKey;
        }
        else{
            return $user;
        }
    }

    function loginDriver($Json)
        {
            $input_decoded = json_decode($Json);
            $ID= $input_decoded->ID;
            $password = $input_decoded->password;
            echo "Running DB call \n";
            $string = $this-> dbFac -> loginDriver ($ID, $password);

            if($string != "invalid"){

                $this->driverArray[]= array(new taxiObject($string,$ID));
            }

            return $string;

        }

    function addNewDriver($Json){
        $input_decoded = json_decode($Json);
        $username = $input_decoded->username;
        $password = $input_decoded->password;

    }

    function addNewCar($Json){
        $input_decoded = json_decode($Json);
        $username = $input_decoded->username;
        $password = $input_decoded->password;


    }

    function orderRide($Json, $clientsocket, $mastersocket){
        $input_decoded = json_decode($Json);
        $sessionKey = $input_decoded->sessionKey;
        $fromlat = $input_decoded->fromlat;
        $fromlng = $input_decoded->fromlng;
        $tolat = $input_decoded->tolat;
        $tolng = $input_decoded->tolng;

        foreach ($this->userArray as $user){
            if($user[0]->sessionKey == $sessionKey){

                $user[0]->fromlat = $fromlat;
                $user[0]->fromlng = $fromlng;
                $user[0]->tolat = $tolat;
                $user[0]->tolng = $tolng;
                $user[0]-> mastersocket = $mastersocket;
                $user[0]-> clientsocket = $clientsocket;
                $user[0]-> status = 'ready';
            }
        }
        return "Working on it";

    }

    function checkAvailability(){

        foreach ($this->driverArray as $driver){
            echo "Check driver in the array :" . json_encode($driver[0]->sessionKey) . "\n";
            $driverUserArray[]=array();
            if ($driver[0]->status == 'ready') {

                echo "Found usable driver with status :".$driver[0]->status;
                $highestPriority=0;

                foreach ($this->userArray as $user){
                        echo "Found user with :".$user[0]->totalSpent." priority";
                        if($user[0]->totalSpent > $highestPriority and $user[0]->status == 'ready'){
                            echo "Made highest priority user:".$user."\n";
                            $highestPriority = $user[0]->totalSpent;
                            $driverUserArray[0] = $user[0];
                    }
                }
                if($driverUserArray[0]->userID != null) {
                $driverUserArray[0]->status ='ongoing';
                $driver[0]->status = 'ongoing';
                $driver[0]->userID = $driverUserArray[0]-->userID;
                echo "Checking driver and user  driver id :".$driver[0]->ID."and user id:".$driverUserArray[0]->userID;
                $driverUserArray[1]=$driver[0];

                    echo ($driver[0] -> lat."\n");
                    echo ($driver[0] -> lng."\n");
                    echo ($driverUserArray[0] -> fromlat."\n");
                    echo ($driverUserArray[0] -> fromlng."\n");

                $Json->fromlat= $driver[0] -> lat;
                $Json->fromlng= $driver[0] -> lng;
                $Json->tolat = $driverUserArray[0] -> fromlat;
                $Json->tolng= $driverUserArray[0] -> fromlng;

                $userJson = $this->getDistanceTimePrice(json_encode($Json));
                $userJson = json_decode($userJson);
                $userJson -> driverID = $driver[0] -> ID;
                $userJson -> currentDate = date("Y-M-d H:i");

                echo json_encode($userJson);

                $driverUserArray[2]=json_encode($userJson);

                $Json -> tolat = $driverUserArray[0] -> tolat;
                $Json -> tolng = $driverUserArray[0] -> tolng;
                $driverUserArray[3]=$this->getDistanceTimePrice(json_encode($Json));

                    echo "Found a match for driver and client \n";
                    return $driverUserArray;
                }

            }

        }

    }

    function driverUpdate($Json, $clientsocket, $mastersocket){
        echo"driverUpdate Json :". $Json;
        $input_decoded = json_decode($Json);
        $sessionKey = $input_decoded->sessionKey;
        $lat = $input_decoded->lat;
        $lng = $input_decoded->lng;
        $status = $input_decoded->status;
        $ID = $input_decoded->ID;

        echo "Check whole array :".json_encode($this->driverArray)."\n";

        foreach ($this->driverArray as $driver){
                echo "Check driver in the array :" . json_encode($driver[0]->sessionKey) . "\n";
                if ($driver[0]->sessionKey == $sessionKey) {
                    echo "Found driver match in driverUpdate \n";
                    $driver[0]->lat = $lat;
                    $driver[0]->lng = $lng;
                    $driver[0]->status = $status;
                    $driver[0]->ID = $ID;
                    $driver[0]-> mastersocket = $mastersocket;
                    $driver[0]-> clientsocket = $clientsocket;

            }

        }
        return "Success";

    }

    function getDistanceTimePrice($Json){

        $Jsondecoded = json_decode($Json);

        $fromlat =$Jsondecoded->fromlat;
        $fromlng =$Jsondecoded->fromlng;
        $tolat =$Jsondecoded->tolat;
        $tolng =$Jsondecoded->tolng;

        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metrics&origins=".$fromlat.",".$fromlng."&destinations=".$tolat.",".$tolng."&mode=driving&key=AIzaSyBHeIZBpoJWJ3YqtGMSzkORkvfukyvvaRw";

//$url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=44311&destinations=45735&key=Your-API-Key";

//fetch json response from googleapis.com:
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = json_decode(curl_exec($ch), true);
//If google responds with a status of OK
//Extract the distance text:

        echo serialize($response);

        if($response['status'] == "OK"){
            $dist = $response['rows'][0]['elements'][0]['distance']['text'];
            $dura = $response['rows'][0]['elements'][0]['duration']['text'];
            $destination = $response['destination_addresses'][0];
            $start = $response['origin_addresses'][0];
            echo "<p>Dist: $dist</p>"."  "."<p>Dura: $dura</p>"."\n";

            $myObj->distance = $dist;
            $myObj->time = $dura;
            $myObj->price= $dist *8;
            $myObj->destination= $destination;
            $myObj->start= $start;

            return json_encode($myObj);
        }
        else{
            echo $response['status']."\n";
        }

    }

    function writeToShmop()
    {
        echo json_decode("Actual driver array :".$this->driverArray."\n");

        $drivers = array();
        foreach ($this->driverArray as $driver){

            echo json_encode($driver);
            if($driver[0]->lat!=null) {
                $drivers[] =
                    array(
                        array(
                            "ID" => $driver[0]->ID,
                            "lat" => $driver[0]->lat,
                            "lng" => $driver[0]->lng,
                            "status" => $driver[0]->status

                        )
                    );
            }
        }

        $Json = json_encode($drivers);

        echo json_decode("Managements driver array: ".$Json."\n");

        save_cache($Json, 'Cabs', 600);


    }

}