<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tracking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="<?= $assets ?>images/favicon.ico"/>
    <link href="<?= $assets ?>styles/theme.css" rel="stylesheet"/>
    <link href="<?= $assets ?>styles/style.css" rel="stylesheet"/>
    <link href="<?= $assets ?>styles/helpers/login.css" rel="stylesheet"/>
    <script type="text/javascript" src="<?= $assets ?>js/jquery-2.0.3.min.js"></script>
    <!--[if lt IE 9]>
    <script src="<?= $assets ?>js/respond.min.js"></script>
    <![endif]-->
	<style>
	#map_canvas {
			 height: 100vh;
			 width:100%;
			 position:relative;
			 float:left;
		  }
	</style>
</head>
<body>
<div class="box">
    
    <?php 
	
	 $driveraddress = $this->site->findLocationWEB($rides->driver_latitude, $rides->driver_longitude);  
	 ?>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <div id="map_canvas"></div>
            </div>

        </div>
    </div>
</div>
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>-->
<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBxxnqAHBqmceXMT1YwJsuEvx40yXPqG3M&sensor=false"></script>
<script src="<?=base_url('serverkapp/node_modules/socket.io/node_modules/socket.io-client/dist/socket.io.js')?>"></script>

<script>
function mobile_status(mob) {
	if(mob == null){
		return 'N/A';
	}else{
		var mobile = mob.slice(-4);		
		return '******'+mobile;
	}
}
</script>

<script>
$(document).ready(function() {
var ride_id = <?= $id ?>;
var directionDisplay;
var directionsService = new google.maps.DirectionsService();
var infowindow = new google.maps.InfoWindow();
var map;
var pickUpMarker, dropToMarker, carMarker; // add marker variables
var pickUpLat   = "<?= $rides->start_lat ?>";
var pickUplon   = "<?= $rides->start_lng ?>";
var pickAddress = "<?= $rides->start ?>";
var pickIcon	= "http://13.233.9.134/themes/default/admin/assets/images/track.png";

var droplat     = "<?= $rides->end_lat ?>";
var droplon     = "<?= $rides->end_lng ?>";
var dropAddress = "<?= $rides->end ?>";
var dropIcon	= "http://13.233.9.134/themes/default/admin/assets/images/track.png";

var cablat     = "";
var cablon     = "";
var cabAddress = "";
var cabIcon	   = "";
var cabStatus  = "0";
var Drivermarkers = [];
var zoom = 7;

function initialize(zoom) {

    directionsDisplay = new google.maps.DirectionsRenderer({
        suppressMarkers: true
    });

    var mapOptions = {
        zoom: zoom,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
    }

    map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
	var rendererOptions = {
		map: map,
		suppressMarkers: true,
		/*polylineOptions: {
		  strokeColor: "block",
		  strokeOpacity:1,
		  strokeWeight: 4
		}*/
	};

	directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);
	
    directionsDisplay.setMap(map);
	
	calcRoute(pickUpLat,pickUplon,pickAddress,pickIcon,  droplat,droplon,dropAddress,dropIcon, cablat,cablon,cabIcon, cabStatus);
	<?php
	if($rides->status == 5){
	?>	
		var flightPlanCoordinates = <?= $rides->location ?>;
        var flightPath = new google.maps.Polyline({
          path: flightPlanCoordinates,
          geodesic: true,
          strokeColor: '#006635',
          strokeOpacity: 2,
          strokeWeight: 4
        });

        flightPath.setMap(map);
	<?php
	}
	?>	
}
	
function calcRoute(pickUpLat,pickUplon,pickAddress,pickIcon,  droplat,droplon,dropAddress,dropIcon, cablat,cablon,cabIcon, cabStatus) {
	
	var start = new google.maps.LatLng(pickUpLat,pickUplon);
    var end = new google.maps.LatLng(droplat,droplon);
	var cab = new google.maps.LatLng(cablat,cablon);
	console.log('recived');
	
	console.log(pickUpMarker);
	console.log(dropToMarker);
	console.log(carMarker);
	
	if(!pickUpMarker) {
        pickUpMarker = createMarker(start,pickAddress,pickIcon);
    } else {
        pickUpMarker.setPosition(start);
    }
	
	if(!dropToMarker) {
        dropToMarker = createMarker(end,dropAddress,dropIcon);
    } else {
        dropToMarker.setPosition(end);
    }
	
	if(!carMarker) {
        carMarker = createMarker(cab,'cab',cabIcon);
    } else {
		console.log('set');
		if(cabStatus == 2){
			carMarker.setIcon('http://13.235.8.87/themes/default/admin/assets/images/booked.png');
		}else if(cabStatus == 3){
			carMarker.setIcon('http://13.235.8.87/themes/default/admin/assets/images/onging.png');
		}else if(cabStatus == 9){
			carMarker.setIcon('http://13.235.8.87/themes/default/admin/assets/images/incomplete.png');
		}else{
			carMarker.setIcon('http://13.235.8.87/themes/default/admin/assets/images/online.png');
		}
        carMarker.setPosition(cab);
    }
	
    var request = {
        origin: start,
        destination: end,
        optimizeWaypoints: true,
        travelMode: google.maps.DirectionsTravelMode.DRIVING
    };

    directionsService.route(request, function (response, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(response);
            var route = response.routes[0];
        }
    });
}

function deleteMarkers(admin_driver_id) {
    Drivermarkers[admin_driver_id].setMap(null);
	Drivermarkers.length-1;
}


function createMarker(latlng,title,icon) {
    var marker = new google.maps.Marker({
        position: latlng,
        animation: google.maps.Animation.DROP,
        title: title,
        icon: icon,
        map: map
    });
    google.maps.event.addListener(marker, 'click', function () {
        infowindow.setContent(title);
        infowindow.open(map, marker);
    });

    return marker;
}

initialize(zoom);

var booking_driver_id = '<?= $rides->driver_id ?>';
var booking_customer_id = '<?= $rides->customer_id ?>';
var booking_id = '<?= $rides->id ?>';

var socket = io.connect('http://'+window.location.hostname+':5000');
socket.on('connect', function(){
	 console.log('S Connect');	 
});
var addresslatlng = [];

socket.on('admin_drivers_location', function(data){
	var admin_driver_id = data.admin_driver_id;
	var admin_ride_id = data.admin_ride_id;
	var admin_status = data.admin_status;
	var admin_lat = data.admin_lat;
	var admin_lng = data.admin_lng;
	var is_connected = data.admin_is_connected;
	
	console.log(data);
	if(admin_ride_id == booking_id){
		console.log('Rides');
		if(is_connected == 1){	
			console.log('Connected');
			
			var lat_lng1 = new google.maps.LatLng(admin_lat,admin_lng);
			
		
			if(admin_status == 2){
				cabIcon = 'http://13.235.8.87/themes/default/admin/assets/images/booked.png';
			}else if(admin_status == 3){
				cabIcon = 'http://13.235.8.87/themes/default/admin/assets/images/onging.png';
			}else if(admin_status == 9){
				cabIcon = 'http://13.235.8.87/themes/default/admin/assets/images/incomplete.png';
			}else{
				cabIcon = 'http://13.235.8.87/themes/default/admin/assets/images/online.png';
			}
				
			console.log(cabIcon);
			calcRoute(pickUpLat,pickUplon,pickAddress,pickIcon,  droplat,droplon,dropAddress,dropIcon, admin_lat,admin_lng,cabIcon, admin_status)
			console.log('change emit');
			
		}else{
			
			deleteMarkers(admin_driver_id);
		}
	}else{
		console.log('No rides');
	}
});



});
</script>
</body>
</html>
