<?php
/**
 * Created by PhpStorm.
 * User: thacrash
 * Date: 5/30/17
 * Time: 11:45 AM
 */

include('shmop.php');
require('DBFacade.php');
require('DBconfig.php');


$newDriverMarkers;
$oldDriverMarkers;


Class Management
{

    public function Display()
    {
        echo "
    <html>
    <body>
    $this->content
    </body>
        $this->footer
        </html>";
    }
}


if (isset($_POST['addCar'])) {
    echo "VIN  :" . $_POST['VIN'] . "  License plate :" . $_POST['carlicenseplate'] . "   Make :" . $_POST['make'] . "   Model :" . $_POST['model'] . "   prodYear :" . $_POST['prodYear'] . "   carType :" . $_POST['carType'];
}
if (isset($_POST['addDriver'])) {
    echo md5($_POST['password']);
}

$management = new Management();

$management->content = $management->content . "<!DOCTYPE html>
<html>
  <head>
  <script type=\"text/javascript\" src=\"http://maps.googleapis.com/maps/api/js?key=AIzaSyBHeIZBpoJWJ3YqtGMSzkORkvfukyvvaRw\"></script>
  <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js\"> </script>

    <style>
    #map {
        height: 100%;
        width: 100%;
       }
    </style>
    <script>
  var marker = null;
  var latitude = 54.913783035792;
  var longitude = 9.77854061842642;
  var map = null;

  function getMapData(){
  $.getJSON( \"http://86.52.212.76/iTax/ReturnMapJson.php\", function(data) {
  
            $.each(data, function(u, f) {
                alert(f[0].ID+'  lat:'+f[0].lat +'  lng:'+f[0].lng);     
            var latLng = new google.maps.LatLng(f[0].lat, f[0].lng);
            var marker = new google.maps.Marker({
                position: latLng,
                icon: \"http://www.busindia.com/images/cab_icon.png\",
                map: map,
                title: \"Driver ID:\"+f[0].ID
            });
            });
})
}
  
  

  function movemarker(id, lat, lng)
  {
    
    var latlng = new google.maps.LatLng(lat, lng);
    marker.setPosition(latlng);
  
  }

  getMapData();
  
    </script>
  </head>
  <body>
    <h3>My Google Maps Demo</h3>
    <div id=\"map\"></div>
    <script>
      function initMap() {
          
          var uluru = {lat : latitude, lng: longitude};
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 13,
          center: uluru
        });     
      }
      
      initMap();
    </script>
  </body>
</html>";


$licenseplates = $dbFac->getAllCarLicensesplates();
$carTypes = $dbFac->getAllCarTypes();

$management->footer = $management->footer.'
        <!-- page footer -->
        <footer>

            <form method="POST">
                <input type="text" name="VIN" id="VIN" value="VIN">
                <input type="text" name="carLicensePlate" id="carLicensePlate" value="License plate">
                <input type="text" name="make" id="make" value="Make">
                <input type="text" name="model" id="model" value="Model">
                <input type="text" name="prodYear" id="prodYear" value="Production Year">
                <select name="carType">';

foreach($licenseplates as $licenseplate){
    $management->footer = $management->footer.'<option value="'.$licenseplate['licensePlate'].'">'.$licenseplate['licensePlate'].'</option>';
}

$management->footer = $management->footer. '</select>
                <input type="submit" name="addCar"></button>
            </form>

            <form method="POST">
                    <input type="text" name="driverFirstName" id="driverFirstName" value="First name">
                    <input type="text" name="driverLastName" id="driverLastName" value="Last name">
                    <input type="password" name="password" value="Password">
                <select name="example">';

foreach($carTypes as $carType){
    $management->footer = $management->footer.'<option value="'.$carType['typeName'].'">'.$carType['typeName'].'</option>';
}

$management->footer = $management->footer.'</select>
                <input type="submit" name="addDriver"></button>
            </form>

        </footer>';

$management->Display();