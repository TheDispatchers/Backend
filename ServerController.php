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

Class ServerController
{
    private $dbFac;
    public $userArray=array();
    public $driverArray=array();


    /**
     * ServerController constructor.
     * @param DBFacade $dbFac The database facade is handed over when it's created
     */
    public function __construct(DBFacade $dbFac) {

        $this->dbFac = $dbFac;


    }

    /**
     * @param $Json - A JSON containing what function to run, and the neccesary information for that function
     * @param $clientsocket - for communicating when the response isn't right after write
     * @param $mastersocket - for communicating when the response isn't right after write
     * @return null|string|UserObject|void - Returns an answer from what ever string was called.
     */
    function getMethod($Json, $clientsocket)
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
                    $string = ServerController::register($Json);
                    break;
                case "login":
                    echo "Entered login case \n";
                    $string = ServerController::login($Json);
                    break;
                case "loginDriver":
                    echo "Entered loginDriver case \n";
                    $string = ServerController::loginDriver($Json);
                    break;
                case "orderRide":{
                    echo "Entered orderRide \n";
                    $string = ServerController::orderRide($Json,$clientsocket);
                    break;
                }
                case "driverUpdate":{
                    echo "Entered driverUpdate \n";
                    $string = ServerController::driverUpdate($Json,$clientsocket);
                    break;
                }
                case "getDistanceTimePrice":{
                    echo "Entered driverUpdate \n";
                    $string = ServerController::getDistanceTimePrice($Json);
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
                    $string = ServerController::loginDriver($Json);
                    break;
                case "register":
                    echo "Entered register \n";
                    $string = ServerController::register($Json);
                    break;
                case "login":
                    echo "Entered login case \n";
                    $string = ServerController::login($Json);
                    break;
                default:
                    echo $function." doesn't exist"."\n";
                    $string = 'No function';
                    break;
            }
            return $string;
        }


    }

    /**
     * @param $Json - JSON with the information needed to register a new user
     * @return string - Returns the status of the register function
     */
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

    /**
     * @param $Json - Json containing the information needed to login
     * @return string|UserObject - Returns primarily the sessionkey generated
     */
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

    /**
     * @param $Json - JSON containing the needed information to login
     * @return string - Returns the sessionkey for the login
     */
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

    /**
     * @param $Json - JSON containing the information needed to create a new driver
     */
    function addNewDriver($Json){
        $input_decoded = json_decode($Json);
        $username = $input_decoded->username;
        $password = $input_decoded->password;

    }

    /**
     * @param $Json -  JSON containing the information needed to create of new car
     */
    function addNewCar($Json){
        $input_decoded = json_decode($Json);
        $username = $input_decoded->username;
        $password = $input_decoded->password;


    }

    /**
     * @param $Json - JSON containing the information needed to order a ride
     * @param $clientsocket - socket for future communcation back
     * @param $mastersocket - socket for future communcation back
     * @return string - Returns a JSON with relevant information
     */
    function orderRide($Json, $clientsocket){
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
                $user[0]-> clientsocket = $clientsocket;
                $user[0]-> status = 'ready';
            }
        }
        return "Working on it";

    }

    /**
     * @return array This function checks if there are any drivers and users that can be paired together for a ride. An array containing the driver object and user object.
     * and the relevant
     */
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

    function driverUpdate($Json, $clientsocket){
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
                    $driver[0]-> clientsocket = $clientsocket;

            }

        }
        return "Success";

    }

    /**
     * @param $Json - JSON containing the starting and endpoint to calculate the distance and price
     * @return string - Returns a JSON containing the distance and price
     */
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

    /**
     * Write our drivers to the shared memory (in the future also write the customers)
     */
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