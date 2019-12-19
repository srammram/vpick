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
            	<h2 class="col-xs-12"><?= lang('details') ?> <?php if($rides->status == 5){ ?><a href="<?= admin_url('rides/pdf/').$rides->id.'/v' ?>" class="btn btn-primary pull-right"><?= lang('invoice_pdf') ?></a> <?php } ?></h2>
                <p class="col-xs-12"><strong><?= lang('booking_no') ?> : <?= $rides->booking_no ?></strong></p>
                <p class="col-xs-12"><strong><?= lang('booking_time') ?> : <?= $rides->booking_timing ?></strong></p>
                <p class="col-xs-12"><strong><?= lang('type') ?> :
                 <?php if($rides->booked_type== 1){ echo 'Cityride'; }elseif($rides->booked_type== 2){ echo 'Rental'; }elseif($rides->booked_type== 3){ echo 'Outstation'; } ?></strong></p>
                <p class="col-xs-12"><strong><?= lang('status') ?> :
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
                }elseif($rides->status == 9){
                    $msg = 'Incomplete';
                }elseif($rides->status == 10){
                    $msg = 'Next Ride';
                }
				echo $msg;
                ?></strong></p>
                <?php if($rides->status == 8 || $rides->status == 6){ ?>
                <p class="col-xs-12"><strong><?= lang('cancel_detail') ?> :</strong><br>
                 <?= lang('cancel_location') ?> : <?= $rides->cancel_location ?><br>
                 <?= lang('cancel_msg') ?> : <?= $rides->cancel_msg ?>
                 </p>
                <?php } ?>
                
                <p class="col-xs-6">
                <strong><?= lang('customer') ?> <?php if($rides->customer_id != 0){ ?><small><a href="<?= admin_url('people/customer_view/'.$rides->customer_id) ?>">Click here</a></small><?php } ?></strong><br>
                <?= $rides->cfname ?> <?= $rides->clname ?><br>
                <?= $rides->cccode.$rides->cmobile ?>
                </p>
                <p class="col-xs-6">
                <?php 
				if(!empty($rides->vmobile)){
				?>
                <strong><?= lang('vendor') ?> <?php if($rides->vendor_id != 0){ ?><small><a href="<?= admin_url('people/vendor_edit/'.$rides->vendor_id) ?>">Click here</a></small><?php } ?></strong>
                <br>
                <?= $rides->vfname ?> <?= $rides->vlname ?> <br>
                <?= $rides->vccode.$rides->vmobile ?>
                <?php
				}
				?>
                <strong><?= lang('driver') ?> <?php if($rides->driver_id != 0){ ?><small><a href="<?= admin_url('people/driver_edit/'.$rides->driver_id) ?>">Click here</a></small><?php } ?></strong>
                <br>
                <?= $rides->dfname ?> <?= $rides->dlname ?> <br>
                <?= $rides->dccode.$rides->dmobile ?>
                </p>
                <div class="clearfix"></div>
                <p class="col-xs-6">
                <strong><?= lang('pickup') ?></strong><br>
                
                <?php  
					$pick = $this->site->findLocationWEB($rides->start_lat, $rides->start_lng); 
					echo $pick;
				?>
                <br>
                <strong><?= lang('pickup_timing') ?> : <?= $rides->ride_timing ?> </strong> 
                </p>
                <p class="col-xs-6">
                <strong><?= lang('drop') ?></strong><br>
                
                <?php 
					$drop = $this->site->findLocationWEB($rides->end_lat, $rides->end_lng); 
					echo $drop;
				?>
                <br>
                <strong><?= lang('dropoff_timing') ?> :  <?= $rides->ride_timing_end ?> </strong>
                <!--<strong>Actual</strong><br>-->
                
                <?php 
				//$actual = $this->site->findLocationWEB($rides->actual_lat, $rides->actual_lng); 
				//echo $actual;
				?>
                </p>
                <div class="clearfix"></div>
                <p class="col-xs-6">
                	<strong><?= lang('rating') ?> : <br>
                    </strong>
                    <br>
                    <small><?= lang('booking_bar') ?> : 
                    	
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
                    <small><?= lang('cab_bar') ?> : 
                    	
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
                    <small><?= lang('driver_bat') ?> : 
                    	
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
                    <small><?= lang('driver_politness') ?> : 
                    	
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
                    <small><?= lang('fare_star') ?> : 
                    	
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
                    <small><?= lang('easy_bar') ?> : 
                    	
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
                    <small><?= lang('overall'); ?> : 
                    	
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
                    	<strong><?= lang('payment') ?> : </strong>
                        <p><?= $rides->payment_name ?></p>
                        <p><?= lang('total_parking') ?> : <?= $rides->total_parking ?></p>
                        <p><?= lang('extra_fare') ?> : <?= $rides->extra_fare ?></p>
                        <p><?= lang('total_fare') ?> : <?= $rides->total_fare ?></p>
                    </p>
                </div>
                
            </div>
            
            <div class="row">
        	<div class="col-sm-12 col-xs-12">
            	
                <div class="col-xs-3">
                	<img src="<?= base_url('themes/default/admin/assets/images/booked.png') ?>"> <b>Booked</b>  <!--<span id="booked">0</span>-->
                </div>
                <div class="col-xs-3">
                	<img src="<?= base_url('themes/default/admin/assets/images/ongoing.png') ?>"> <b>Ongoing</b>  <!--<span id="ongoing">0</span>-->
                </div>
                <div class="col-xs-3">
                	<img src="<?= base_url('themes/default/admin/assets/images/incomplete.png') ?>"> <b>Incomplete</b>  <!--<span id="incomplete">0</span>-->
                </div>
                <div class="col-xs-3">
                	<img src="<?= base_url('themes/default/admin/assets/images/complete.png') ?>"> <b>Completed</b>  <!--<span id="online">0</span>-->
                    
                </div>
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
		/*polylineOptions: {
		  strokeColor: "block",
		  strokeOpacity:1,
		  strokeWeight: 4
		}*/
	};

	directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);
	
    directionsDisplay.setMap(map);
	
	calcRoute(pickUpLat,pickUplon,pickAddress,pickIcon,  droplat,droplon,dropAddress,dropIcon);
		
		var flightPlanCoordinates = <?= $rides->location ?>;
        var flightPath = new google.maps.Polyline({
          path: flightPlanCoordinates,
          geodesic: true,
          strokeColor: '#FF0000',
          strokeOpacity: 2,
          strokeWeight: 4
        });

        flightPath.setMap(map);
		
}
	
function calcRoute(pickUpLat,pickUplon,pickAddress,pickIcon,  droplat,droplon,dropAddress,dropIcon, carlat, carlng, carAddress, carIcon) {
	
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
<!--
<script src="<?=base_url('serverkapp/node_modules/socket.io/node_modules/socket.io-client/dist/socket.io.js')?>"></script>

<script>
var socket = io.connect('http://'+window.location.hostname+':7000');
socket.on('connect', function(){
	 console.log('S Connect');	 
});
var addresslatlng = [];

var online = 0;
var booked = 0;
var ongoing = 0;
var incomplete = 0;
 
socket.on('admin_drivers_location', function(data){
	var admin_driver_id = data.admin_driver_id;
	var admin_ride_id = data.admin_ride_id;
	var admin_status = data.admin_status;
	var admin_lat = data.admin_lat;
	var admin_lng = data.admin_lng;
	var is_connected = data.admin_is_connected;
	
	console.log(data);
	if(admin_driver_id == 0 && admin_ride_id == 0){
		console.log('No driver');
	}else{
		console.log('Data driver');
		//console.log(markers);
		if(is_connected == 1){	
			console.log(getUnique(multiple_driver));
			
			console.log('Connected');
			
			if(getUnique(multiple_driver).includes(admin_driver_id)){
				console.log('Exit Driver');
				
				if(admin_ride_id != 0){
						console.log('Exit Driver'+admin_driver_id);	
						//console.log(markers[admin_driver_id]);	
						console.log('setIcon');		
						if(admin_status == 2){
							markers[admin_driver_id].setIcon('http://13.235.8.87/themes/default/admin/assets/images/booked.png');
						}else if(admin_status == 3){
							markers[admin_driver_id].setIcon('http://13.235.8.87/themes/default/admin/assets/images/onging.png');
						}else if(admin_status == 9){
							markers[admin_driver_id].setIcon('http://13.235.8.87/themes/default/admin/assets/images/incomplete.png');
						}else{
							markers[admin_driver_id].setIcon('http://13.235.8.87/themes/default/admin/assets/images/online.png');
						}
					//var url_google = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" + admin_lat + "," + admin_lng + "&key=AIzaSyAQggnzNxn0UFplcovbvhXQPsA8-zUsDk8";
				//console.log(url_google);
				 //var add = '';
					//$.getJSON(url_google,function (data, textStatus) {
					   // add = data.results[0].formatted_address;
					   //console.log(data.results[0].formatted_address+'test');
					  // markers[admin_driver_id].setContent(data.results[0].formatted_address);
					   //markers[admin_driver_id] =  addMarker(lat_lng1, data.results[0].formatted_address,'http://13.233.9.134/themes/default/admin/assets/images/track.png', admin_driver_id);
					//});
					
				}else{
					console.log(admin_driver_id);	
					console.log('Exit Driver@@@@@@@'+admin_driver_id);	
					markers[admin_driver_id].setIcon('http://13.235.8.87/themes/default/admin/assets/images/online.png');
					//markers[admin_driver_id].setIcon('http://13.233.9.134/themes/default/admin/assets/images/Completed.png');
				}				
				
			}else{
				console.log('New Driver');	
				online = online + 1;		
				//console.log(addresslatlng);
				console.log(admin_driver_id+'Driver');
				console.log(admin_lat);
				console.log(admin_lng);
				var lat_lng1 = new google.maps.LatLng(admin_lat,admin_lng);
				

				  var imageicon = '';
				  if(admin_ride_id != 0){
					  if(admin_status == 2){
							markers[admin_driver_id].setIcon('http://13.235.8.87/themes/default/admin/assets/images/booked.png');
						}else if(admin_status == 3){
							markers[admin_driver_id].setIcon('http://13.235.8.87/themes/default/admin/assets/images/onging.png');
						}else if(admin_status == 9){
							markers[admin_driver_id].setIcon('http://13.235.8.87/themes/default/admin/assets/images/incomplete.png');
						}else{
							markers[admin_driver_id].setIcon('http://13.235.8.87/themes/default/admin/assets/images/online.png');
						}
				  }else{
					  imageicon =  'http://13.235.8.87/themes/default/admin/assets/images/online.png'
				  }
				   markers[admin_driver_id] =  addMarker(lat_lng1, '',imageicon, admin_driver_id);

				console.log( markers[admin_driver_id]);
				
				multiple_driver.push(admin_driver_id);	
				
			}
		
			changeMarkerPosition(map, marker, admin_driver_id, admin_lat, admin_lng);
		}else{
			var index = multiple_driver.indexOf(admin_driver_id);
			if (index > -1) {
				multiple_driver.splice(index, 1);
			}
			deleteMarkers(admin_driver_id);
		}
	}
});

//$('#online').text(online);
//$('#booked').text(booked);
//$('#ongoing').text(ongoing);
//('#incomplete').text(incomplete);

</script>-->


    

