<?php
require_once 'config.php';
?>
<html>

<head>
    <title> Calculating Distance between locations</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script defer
            src="https://maps.googleapis.com/maps/api/js?libraries=places&language=<?= $_SESSION['lang'] ?>&key=AIzaSyAuOmO4jojZE3pfN37iiEbJAATCOtn98Z4"
            type="text/javascript"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
<style>
.Header {
    padding: 70px;
    text-align: center;
    background-image: url("images/Google_maps2.jpg") ;
    color: white;
    font-family: 'Dancing Script', cursive;
    
  }
  .padding{

    padding:10px;
  }

  .button {
  border-radius: 4px;
  background-color: #f4511e;
  border: none;
  color: #FFFFFF;
  text-align: center;
  font-size: 28px;
  padding: 20px;
  width: 200px;
  transition: all 0.5s;
  cursor: pointer;
  margin: 5px;
}

.button span {
  cursor: pointer;
  display: inline-block;
  position: relative;
  transition: 0.5s;
}

.button span:after {
  content: '\00bb';
  position: absolute;
  opacity: 0;
  top: 0;
  right: -20px;
  transition: 0.5s;
}

.button:hover span {
  padding-right: 25px;
}

.button:hover span:after {
  opacity: 1;
  right: 0;
}
<


  

  .description{

    padding: 45px;
    text-align: center;
    background-color: azure;
    color:black;


  }
img{
    border-radius:50%;
}

img.resize {
    width: 2px;
    height:2px;
}

  h4{
	font-family: 'Open Sans', sans-serif;
  margin-bottom: 30px;
  color:white;
}

    </style>
</head>

<body>

<div class="Header">
<h1>Distance Calculator</h1>
<h2>What it does<h2>
<h4>Calculates Distance between 2 locations <br> Calculates best route<br> Change between languages<br> Chooses between modes of transport</h4>
 <img src="images/api.png"> <img src="images/xammp.png">
</div>


<div class="padding"></div>



<div class="container">
        <!-- Language -->
        <script>
            function changeLang() {
                document.getElementById('form_lang').submit();
            }
        </script>
        <form method='get' action='' id='form_lang'>
            <div class="form-group">
            Select Language : <select class="form-control" name='lang' onchange='changeLang();'>
                <option value='en' <?php if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'en') {
                    echo "selected";
                } ?> >English
                </option>
                <option value='fr' <?php if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'fr') {
                    echo "selected";
                } ?> >French
                </option>
                <option value='de' <?php if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'de') {
                    echo "selected";
                } ?> >Germany
                </option>
            </select>
            </div>
        </form>

        <div class="row">
        <div class="col-sm-4">
            <!-- form -->
            <form id="distance_form">
                <div class="form-group"><label><?= $lang['username'] ?>: </label>
                    <input class="form-control" id="username" placeholder="<?= $lang['enter_username'] ?>"/>
                    <input  name="username" required="" type="hidden"/></div>

                <div class="form-group"><label><?= $lang['origin'] ?>: </label>
                    <input class="form-control" id="from_places" placeholder="<?= $lang['enter_location'] ?>"/>
                    <input id="origin" name="origin" required="" type="hidden"/>
                    <a class="form-control" onclick="getCurrentPosition()">Set Current Location</a>
                </div>

                <div class="form-group"><label><?= $lang['destination'] ?>: </label>
                    <input class="form-control" id="to_places" placeholder="<?= $lang['enter_location'] ?>"/>
                    <input id="destination" name="destination" required="" type="hidden"/></div>

                <div class="form-group">
                    <label><?= $lang['travel_mode'] ?></label>
                    <select class="form-control" id="travel_mode" name="travel_mode">
                        <option value="DRIVING"><?= $lang['Driving'] ?></option>
                        <option value="WALKING"><?= $lang['foot'] ?></option>
                        <option value="BICYCLING"><?= $lang['bic'] ?></option>
                        <option value="TRANSIT"><?= $lang['transit'] ?></option>
                    </select>
                </div>

                 <input class="button" type="submit" value="<?= $lang['calculate_btn'] ?>"/>

            </form>
        </div>

        <div class="col-sm-4">
         <div id="map" style="height: 400px; width: 500px" ></div>
        </div>
            <!-- result -->
        <div class="col-sm-4">
            <div style="margin-left: 123px;" id="result" class="hide">
                <ul class="list-group">
                    <li id="in_mile" class="list-group-item d-flex justify-content-between align-items-center"></li> <br>
                    <br>
                    <br>
                    <li id="in_kilo" class="list-group-item d-flex justify-content-between align-items-center"></li> <br>
                    <br>
                    <br>

                    <li id="duration_text" class="list-group-item d-flex justify-content-between align-items-center"></li> <br>
                    <br>

                </ul>
            </div>
        </div>
           
        </div>
        
    </div>


</div>
<script>
    $(function () {
        var origin, destination, map;

        // add input listeners
        google.maps.event.addDomListener(window, 'load', function (listener) {
            setDestination();
            initMap();
        });

        // init or load map
        function initMap() {

            var myLatLng = {
                lat: -29.4893,
                lng: 30.2167
            };
            map = new google.maps.Map(document.getElementById('map'), {zoom: 16, center: myLatLng,});
        }

        function setDestination() {
            var from_places = new google.maps.places.Autocomplete(document.getElementById('from_places'));
            var to_places = new google.maps.places.Autocomplete(document.getElementById('to_places'));

            google.maps.event.addListener(from_places, 'place_changed', function () {
                var from_place = from_places.getPlace();
                var from_address = from_place.formatted_address;
                $('#origin').val(from_address);
            });

            google.maps.event.addListener(to_places, 'place_changed', function () {
                var to_place = to_places.getPlace();
                var to_address = to_place.formatted_address;
                $('#destination').val(to_address);
            });


        }

        function displayRoute(travel_mode, origin, destination, directionsService, directionsDisplay) {
            directionsService.route({
                origin: origin,
                destination: destination,
                travelMode: travel_mode,
                avoidTolls: true
            }, function (response, status) {
                if (status === 'OK') {
                    directionsDisplay.setMap(map);
                    directionsDisplay.setDirections(response);
                } else {
                    directionsDisplay.setMap(null);
                    directionsDisplay.setDirections(null);
                    alert('Could not display directions due to: ' + status);
                }
            });
        }

        // calculate distance , after finish send result to callback function
        function calculateDistance(travel_mode, origin, destination) {

            var DistanceMatrixService = new google.maps.DistanceMatrixService();
            DistanceMatrixService.getDistanceMatrix(
                {
                    origins: [origin],
                    destinations: [destination],
                    travelMode: google.maps.TravelMode[travel_mode],
                    unitSystem: google.maps.UnitSystem.IMPERIAL, // miles and feet.
                    // unitSystem: google.maps.UnitSystem.metric, // kilometers and meters.
                    avoidHighways: false,
                    avoidTolls: false
                }, save_results);
        }

        // save distance results
        function save_results(response, status) {

            if (status != google.maps.DistanceMatrixStatus.OK) {
                $('#result').html(err);
            } else {
                var origin = response.originAddresses[0];
                var destination = response.destinationAddresses[0];
                if (response.rows[0].elements[0].status === "ZERO_RESULTS") {
                    $('#result').html("Sorry , not available to use this travel mode between " + origin + " and " + destination);
                } else {
                    var distance = response.rows[0].elements[0].distance;
                    var duration = response.rows[0].elements[0].duration;
                    var distance_in_kilo = distance.value / 1000; // the kilo meter
                    var distance_in_mile = distance.value / 1609.34; // the mile
                    var duration_text = duration.text;
                    appendResults(distance_in_kilo, distance_in_mile, duration_text);
                    sendAjaxRequest(origin, destination, distance_in_kilo, distance_in_mile, duration_text);
                }
            }
        }

        // append html results
        function appendResults(distance_in_kilo, distance_in_mile, duration_text) {
            $("#result").removeClass("hide");
            $('#in_mile').html("<?= $lang['distance_in_mile'] ?> : <span class='badge badge-pill badge-secondary'>" + distance_in_mile.toFixed(2) + "</span>");
            $('#in_kilo').html("<?= $lang['distance_in_kilo'] ?>: <span class='badge badge-pill badge-secondary'>" + distance_in_kilo.toFixed(2) + "</span>");
            $('#duration_text').html("<?= $lang['in_text'] ?>: <span class='badge badge-pill badge-success'>" + duration_text + "</span>");
        }

        // send ajax request to save results in the database
        function sendAjaxRequest(origin, destination, distance_in_kilo, distance_in_mile, duration_text) {
            var username =   $('#username').val();
            var travel_mode =  $('#travel_mode').find(':selected').text();
            $.ajax({
                url: 'common.php',
                type: 'POST',
                data: {
                    username,
                    travel_mode,
                    origin,
                    destination,
                    distance_in_kilo,
                    distance_in_mile,
                    duration_text
                },
                success: function (response) {
                    console.info(response);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }

        // on submit  display route ,append results and send calculateDistance to ajax request
        $('#distance_form').submit(function (e) {
            e.preventDefault();
            var origin = $('#origin').val();
            var destination = $('#destination').val();
            var travel_mode = $('#travel_mode').val();
            var directionsDisplay = new google.maps.DirectionsRenderer({'draggable': false});
            var directionsService = new google.maps.DirectionsService();
           displayRoute(travel_mode, origin, destination, directionsService, directionsDisplay);
            calculateDistance(travel_mode, origin, destination);
        });

    });

    // get current Position
    function getCurrentPosition() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(setCurrentPosition);
        } else {
            alert("Geolocation is not supported by this browser.")
        }
    }

    // get formatted address based on current position and set it to input
    function setCurrentPosition(pos) {
        var geocoder = new google.maps.Geocoder();
        var latlng = {lat: parseFloat(pos.coords.latitude), lng: parseFloat(pos.coords.longitude)};
        geocoder.geocode({ 'location' :latlng  }, function (responses) {
            console.log(responses);
            if (responses && responses.length > 0) {
                $("#origin").val(responses[1].formatted_address);
                $("#from_places").val(responses[1].formatted_address);
                //    console.log(responses[1].formatted_address);
            } else {
                alert("Cannot determine address at this location.")
            }
        });
    }


</script>

  

</body>

</html>
