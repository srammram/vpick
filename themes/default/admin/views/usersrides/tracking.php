<style>
#map_canvas {
      	 height: 400px;
		 width:100%;
		 position:relative;
		 float:left;
      }
.checked{color:#EFD911;}
.star {
        font-size: x-large;
        width: auto;
        display: inline-block;
        color: gray;
    }
    .star:last-child {
        margin-right: 0;
    }
    .star:before {
        content: '\2605';
    }
    .star.on {
        color: #000;
    }
    .star.half:after {
            content: '\2605';
            color: #000;
            margin-left: -20px;
            width: 10px;
            position: absolute;
            overflow: hidden;
        }
</style>
<div class="box">
    
    <?php 
	 $driveraddress = $this->site->findLocationWEB($rides->driver_latitude, $rides->driver_longitude);  
	 ?>
    <div class="box-content">
        <div class="row">
        	<div class="col-xs-12">
            	<h2 class="col-xs-12">Details</h2>
                <p class="col-xs-12"><strong>Booking NO : <?= $rides->booking_no ?></strong></p>
                <p class="col-xs-12"><strong>Booking Timing : <?= $rides->booking_timing ?></strong></p>
                <p class="col-xs-12"><strong>Booking Type :
                 <?php if($rides->booked_type== 1){ echo 'Cityride'; }elseif($rides->booked_type== 2){ echo 'Rental'; }elseif($rides->booked_type== 3){ echo 'Outstation'; } ?></strong></p>
                <p class="col-xs-12"><strong>Status :
                 <?php
                if($rides->status == 1){
                    $msg = 'Request Ride';
                }elseif($rides->status == 2){
                    $msg = 'Booked Ride';
                }elseif($rides->status == 3){
                    $msg = 'Onride Ride';
                }elseif($rides->status == 4){
                    $msg = 'Waiting Ride';
                }elseif($rides->status == 5){
                    $msg = 'Completed Ride';
                }elseif($rides->status == 6){
                    $msg = 'Cancelled Ride';
                }elseif($rides->status == 7){
                    $msg = 'Ride Later Ride';
                }elseif($rides->status == 8){
                    $msg = 'Ride Rejected';
                }
				echo $msg;
                ?></strong></p>
                <?php if($rides->status == 8 || $rides->status == 6){ ?>
                <p class="col-xs-12"><strong>Cancel Details :</strong><br>
                 Cancel Location : <?= $rides->cancel_location ?><br>
                 Cancel Message : <?= $rides->cancel_msg ?>
                 </p>
                <?php } ?>
                
                <p class="col-xs-6">
                <strong>Customer <?php if($rides->customer_id != 0){ ?><small><a href="<?= admin_url('people/customer_view/'.$rides->customer_id) ?>">Click here</a></small><?php } ?></strong><br>
                <?= $rides->cfname ?> <?= $rides->clname ?><br>
                <?= $rides->cccode.$rides->cmobile ?>
                </p>
                <p class="col-xs-6">
                <?php 
				if(!empty($rides->vmobile)){
				?>
                <strong>Vendor <?php if($rides->vendor_id != 0){ ?><small><a href="<?= admin_url('people/vendor_edit/'.$rides->vendor_id) ?>">Click here</a></small><?php } ?></strong>
                <br>
                <?= $rides->vfname ?> <?= $rides->vlname ?> <br>
                <?= $rides->vccode.$rides->vmobile ?>
                <?php
				}
				?>
                <strong>Driver <?php if($rides->driver_id != 0){ ?><small><a href="<?= admin_url('people/driver_edit/'.$rides->driver_id) ?>">Click here</a></small><?php } ?></strong>
                <br>
                <?= $rides->dfname ?> <?= $rides->dlname ?> <br>
                <?= $rides->dccode.$rides->dmobile ?>
                </p>
                <div class="clearfix"></div>
                <p class="col-xs-6">
                <strong>Pick Up</strong><br>
                
                <?php  
					$pick = $this->site->findLocationWEB($rides->start_lat, $rides->start_lng); 
					echo $pick;
				?>
                <br>
                <strong>Pickup Timing : <?= $rides->ride_timing ?> </strong> 
                </p>
                <p class="col-xs-6">
                <strong>Drop</strong><br>
                
                <?php 
					$drop = $this->site->findLocationWEB($rides->end_lat, $rides->end_lng); 
					echo $drop;
				?>
                <br>
                <strong>Dropoff Timing :  <?= $rides->ride_timing_end ?> </strong>
                <!--<strong>Actual</strong><br>-->
                
                <?php 
				//$actual = $this->site->findLocationWEB($rides->actual_lat, $rides->actual_lng); 
				//echo $actual;
				?>
                </p>
                <div class="clearfix"></div>
                <p class="col-xs-6">
                	<strong>Ratings : <br>
                    </strong>
                    <br>
                    <small>Booking process star : 
                    	
                        <?php
						for( $x = 0; $x < 5; $x++ )
						{
							if( floor($rides->booking_process_star)-$x >= 1 )
							{ echo '<i class="fa fa-star"></i>'; }
							elseif( $rides->booking_process_star-$x > 0 )
							{ echo '<i class="fa fa-star-half-o"></i>'; }
							else
							{ echo '<i class="fa fa-star-o"></i>'; }
						}
						?>
                       
						
                    </small>
                    <br>
                    <small>Cab cleanliness star : 
                    	
                        <?php
						for( $x = 0; $x < 5; $x++ )
						{
							if( floor($rides->cab_cleanliness_star)-$x >= 1 )
							{ echo '<i class="fa fa-star"></i>'; }
							elseif( $rides->cab_cleanliness_star-$x > 0 )
							{ echo '<i class="fa fa-star-half-o"></i>'; }
							else
							{ echo '<i class="fa fa-star-o"></i>'; }
						}
						?>
                    </small>
                    <br>
                    <small>Drive comfort star : 
                    	
                        <?php
						for( $x = 0; $x < 5; $x++ )
						{
							if( floor($rides->drive_comfort_star)-$x >= 1 )
							{ echo '<i class="fa fa-star"></i>'; }
							elseif( $rides->drive_comfort_star-$x > 0 )
							{ echo '<i class="fa fa-star-half-o"></i>'; }
							else
							{ echo '<i class="fa fa-star-o"></i>'; }
						}
						?>
                    </small>
                    <br>
                    <small>Drive politeness star : 
                    	
                        <?php
						for( $x = 0; $x < 5; $x++ )
						{
							if( floor($rides->drive_politeness_star)-$x >= 1 )
							{ echo '<i class="fa fa-star"></i>'; }
							elseif( $rides->drive_politeness_star-$x > 0 )
							{ echo '<i class="fa fa-star-half-o"></i>'; }
							else
							{ echo '<i class="fa fa-star-o"></i>'; }
						}
						?>
                    </small>
                    <br>
                    <small>Fare star : 
                    	
                        <?php
						for( $x = 0; $x < 5; $x++ )
						{
							if( floor($rides->fare_star)-$x >= 1 )
							{ echo '<i class="fa fa-star"></i>'; }
							elseif( $rides->fare_star-$x > 0 )
							{ echo '<i class="fa fa-star-half-o"></i>'; }
							else
							{ echo '<i class="fa fa-star-o"></i>'; }
						}
						?>
                    </small>
                    <br>
                    <small>Easy of payment star : 
                    	
                        <?php
						for( $x = 0; $x < 5; $x++ )
						{
							if( floor($rides->easy_of_payment_star)-$x >= 1 )
							{ echo '<i class="fa fa-star"></i>'; }
							elseif( $rides->easy_of_payment_star-$x > 0 )
							{ echo '<i class="fa fa-star-half-o"></i>'; }
							else
							{ echo '<i class="fa fa-star-o"></i>'; }
						}
						?>
                    </small>
                    <br>
                    <small>Overall : 
                    	
                        <?php
						for( $x = 0; $x < 5; $x++ )
						{
							if( floor($rides->overall)-$x >= 1 )
							{ echo '<i class="fa fa-star"></i>'; }
							elseif( $rides->overall-$x > 0 )
							{ echo '<i class="fa fa-star-half-o"></i>'; }
							else
							{ echo '<i class="fa fa-star-o"></i>'; }
						}
						?>
                    </small>
                </p>
                
                <div class="col-xs-6">
                	<p>
                    	<strong>Payment : </strong>
                        <p><?= $rides->payment_name ?></p>
                        <p>Total parking : <?= $rides->total_parking ?></p>
                        <p>Extra fare : <?= $rides->extra_fare ?></p>
                        <p>Total fare : <?= $rides->total_fare ?></p>
                    </p>
                </div>
                
            </div>
            <div class="col-lg-12">
                <div id="map_canvas"></div>
            </div>

        </div>
    </div>
</div>
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>-->
<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBxxnqAHBqmceXMT1YwJsuEvx40yXPqG3M&sensor=false"></script>
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
var pickAddress = "<?= $pick ?>";
var pickIcon	= "http://13.233.9.134/themes/default/admin/assets/images/track.png";

var droplat     = "<?= $rides->end_lat ?>";
var droplon     = "<?= $rides->end_lng ?>";
var dropAddress = "<?= $drop ?>";
var dropIcon	= "http://13.233.9.134/themes/default/admin/assets/images/track.png";
<?php
if($rides->status == 2 || $rides->status == 3 || $rides->status == 4){
?>
var carlat		= "<?= $rides->driver_latitude ?>";
var carlng		= "<?= $rides->driver_longitude ?>";
var driver_lat	= "<?= $rides->driver_latitude ?>";
var driver_lng	= "<?= $rides->driver_longitude ?>";

var carAddress	= "<?= $driveraddress ?>";
var carIcon	= "http://13.233.9.134/themes/default/admin/assets/images/track.png";
<?php
}else{
?>
var carlat		= 0;
var carlng		= 0;
var carAddress	= 0;
var carIcon	= 0;
<?php
}
?>

function initialize() {

    directionsDisplay = new google.maps.DirectionsRenderer({
        suppressMarkers: true
    });

    var mapOptions = {
        zoom: 10,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
    }

    map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
	var rendererOptions = {
		map: map,
		suppressMarkers: true,
		polylineOptions: {
		  strokeColor: "block",
		  strokeOpacity:1,
		  strokeWeight: 4
		}
	};

	directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);
	
    directionsDisplay.setMap(map);
    calcRoute(pickUpLat,pickUplon,pickAddress,pickIcon,  droplat,droplon,dropAddress,dropIcon, carlat,carlng,carAddress,carIcon);
}
	
function calcRoute(pickUpLat,pickUplon,pickAddress,pickIcon,  droplat,droplon,dropAddress,dropIcon, carlat,carlng,carAddress,carIcon) {
	
	var start = new google.maps.LatLng(pickUpLat,pickUplon);
    var end = new google.maps.LatLng(droplat,droplon);
	
	if(carlat != 0 && carlng != 0){
		var car = new google.maps.LatLng(carlat,carlng);
		if(!carMarker) {
			carMarker = createMarker(car,carAddress,carIcon);
		} else {
			carMarker.setPosition(car);
		}
	}
	
	if(!pickUpMarker) {
        pickUpMarker = createMarker(start,pickAddress,pickIcon);
    } else {
        pickUpMarker.setPosition(start);
    }
	
	if(!dropToMarker) {
        dropToMarker = createMarker(end,dropAddress,dropIcon);
    } else {
        dropToMarker.setPosition(start);
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

initialize();

function makeRequest(driver_lat, driver_lng){
	 var myLatLng = { lat: driver_lat, lng: driver_lng};
	 carMarker.setPosition(myLatLng);
} 
//setInterval(makeRequest(driver_lat, driver_lng), (3000));

});
</script>
<script src="<?=base_url('serverkapp/node_modules/socket.io/node_modules/socket.io-client/dist/socket.io.js')?>"></script>
<script>
var socket = io('http://13.233.109.60:5000');
 //socket.on('connect', function(){});
socket.on('admin_drivers_location', function(data){
	var admin_driver_id = data.admin_driver_id;
	var admin_ride_id = data.admin_ride_id;
	var admin_lat = data.admin_lat;
	var admin_lng = data.admin_lng;
	if(admin_driver_id == 0 && admin_ride_id == 0){
		console.log('No ride');
	}else{
		if(ride_id == admin_ride_id){
			makeRequest(admin_lat, admin_lng);
		}else{
			console.log('No ride');
		}
	}
	console.log(data);	
});


  //socket.on('disconnect', function(){});
</script> 


    

